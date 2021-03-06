<?php

namespace App\Synchronizer;

use App\Converter\PaymentConverter;
use App\Converter\SubscriptionConverter;
use App\Entity\Subscription;
use App\Entity\User;
use ChartMogul\Subscription as ChartMogulSubscription;
use Paddle\API;

class SubscriptionSynchronizer
{
	public function __construct(
		private SubscriptionConverter $subscriptionConverter,
		private PaymentConverter $paymentConverter,
	)
	{
	}

	/**
	 * @return array<mixed,Subscription>
	 */
	public function fetch(User $user, API $paddle): array
	{
		$availableSubscriptionsData = $paddle->subscription()->listUsers();
		$subscriptions = array_map(function ($subscriptionData) use ($user) {
			return $this->subscriptionConverter->hydrate($subscriptionData, $user);
		}, $availableSubscriptionsData);
		
		return $subscriptions;
	}

	public function cancel(Subscription $subscription): void
	{
		/** @var ChartMogulSubscription|null $chartMogulSubscription */
		$chartMogulSubscription = ChartMogulSubscription::all([
			"customer_uuid" => $subscription->getCustomer()->getChartMogulId(),
			"external_id" => $subscription->getPaddleId(),
		])->first();
		
		if ($chartMogulSubscription) {
			$chartMogulSubscription->cancel(date("Y-m-d"));
		}
		
		$subscription->setState("deleted");
	}
}