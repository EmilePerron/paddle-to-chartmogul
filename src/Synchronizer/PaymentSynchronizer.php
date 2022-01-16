<?php

namespace App\Synchronizer;

use App\Converter\PaymentConverter;
use App\Converter\SubscriptionConverter;
use App\Entity\DataSource;
use App\Entity\Payment;
use App\Entity\User;
use ChartMogul\CustomerInvoices as ChartMogulCustomerInvoices;
use ChartMogul\Invoice as ChartMogulInvoice;
use ChartMogul\LineItems\Subscription as ChartMogulSubscription;
use ChartMogul\Transactions\Payment as ChartMogulPayment;
use DateTime;
use Paddle\API;

class PaymentSynchronizer
{
	public function __construct(
		private PaymentConverter $paymentConverter,
		private SubscriptionConverter $subscriptionConverter,
	)
	{
	}

	/**
	 * @return array<mixed,Payment>
	 */
	public function fetch(User $user, API $paddle): array
	{
		$availablePaymentsData = $paddle->subscription()->listPayments();
		$payments = array_filter(array_map(function ($paymentData) use ($user) {
			if (!$paymentData["is_paid"]) {
				return null;
			}

			return $this->paymentConverter->hydrate($paymentData, $user);
		}, $availablePaymentsData));
		
		return $payments;
	}

	public function sync(Payment $payment, DataSource $dataSource): ?Payment
	{
		// If the transaction has already been imported, there's nothing else to do.
        if ($payment->getSynced() || $payment->getChartMogulId()) {
        	return $payment;
        }

		$subscription = $payment->getSubscription();
		$subscription->setLastSyncDate(new DateTime());
		
		// Create the subscription if it doesn't exist
		$chartMogulSubscriptionData = $this->subscriptionConverter->convert($subscription);
		$chartMogulSubscription = new ChartMogulSubscription($chartMogulSubscriptionData);

		$chartMogulPaymentData = $this->paymentConverter->convert($payment);
		$transaction = new ChartMogulPayment($chartMogulPaymentData);

		$invoice = new ChartMogulInvoice([
			"external_id" => $payment->getPaddleId(),
			"date" => $chartMogulPaymentData["date"],
			"currency" => $payment->getCurrency(),
			"customer_external_id" => $subscription->getCustomer()->getPaddleId(),
			"data_source_uuid" => $dataSource->getChartMogulId(),
			"line_items" => [$chartMogulSubscription],
			"transactions" => [$transaction]
		]);

		ChartMogulCustomerInvoices::create([
			"customer_uuid" => $subscription->getCustomer()->getChartMogulId(),
			"invoices" => [$invoice]
		]);

		$payment->setChartMogulId($invoice->uuid)
			->setSynced(true)
			->setLastSyncDate(new DateTime());
		$subscription->getOwner()->addPayment($payment);
		
		return $payment;
	}
}