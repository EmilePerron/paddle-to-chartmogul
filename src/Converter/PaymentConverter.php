<?php

namespace App\Converter;

use App\Entity\Payment;
use App\Entity\User;
use App\Repository\PaymentRepository;
use App\Repository\SubscriptionRepository;
use DateTime;

class PaymentConverter
{
	public function __construct(
		private PaymentRepository $repository,
		private SubscriptionRepository $subscriptionRepository,
		private CustomerConverter $customerConverter,
	)
	{
	}

	public function convert(Payment $payment): array
	{
		$amount = $payment->getAmount();
		$planAmount = round($payment->getSubscription()->getPlan()->getRecurringPrice() * 100);

		if ($amount > $planAmount) {
			$amount = $planAmount;
		}

		return [
            "type" => "payment",
			"result" => "successful",
			"date" => $payment->getPayoutDate()->format("Y-m-d"),
			"amount_in_cents" => $amount,
			"currency" => $payment->getCurrency(),
			"external_id" => $payment->getPaddleId(),
        ];
	}

	public function hydrate(array $data, User $user): Payment
	{
		$payment = $this->repository->findOneBy(["paddleId" => $data["id"]]);

		if (!$payment) {
			$payment = new Payment();
		}

		$payment
			->setOwner($user)
			->setPaddleId($data["id"])
			->setAmount(round($data["amount"] * 100))
			->setCurrency($data["currency"])
			->setPayoutDate(new DateTime($data["payout_date"]))
			->setPaid($data["is_paid"])
			->setIsOneOff($data["is_one_off_charge"])
			->setSubscription($this->subscriptionRepository->findOneBy(["paddleId" => $data["subscription_id"]]));

		return $payment;
	}
}