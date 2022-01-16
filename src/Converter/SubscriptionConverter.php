<?php

namespace App\Converter;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Util\PeriodCalculator;
use DateTime;

class SubscriptionConverter
{
	public function __construct(
		private SubscriptionRepository $repository,
		private PlanRepository $planRepository,
		private CustomerConverter $customerConverter,
	)
	{
	}

	public function convert(Subscription $subscription): array
	{
		// @TODO: Add transaction fees and tax amount
		return [
			"type" => "subscription",
			"subscription_external_id" => $subscription->getPaddleId(),
			"customer_uuid" => $subscription->getCustomer()->getChartMogulId(),
			"plan_uuid" => $subscription->getPlan()->getChartMogulId(),
			"service_period_start" => (new PeriodCalculator())->getStartDate($subscription)->format("Y-m-d"), 
			"service_period_end" => $subscription->getNextPaymentDate()->format("Y-m-d"),
			"amount_in_cents" => $subscription->getPlan()->getRecurringPrice() * 100,
			"quantity" => $subscription->getQuantity(),
        ];
	}

	public function hydrate(array $data, User $user): Subscription
	{
		$subscription = $this->repository->findOneBy(["paddleId" => $data["subscription_id"]]);

		if (!$subscription) {
			$subscription = new Subscription();
		}

		$subscription
			->setOwner($user)
			->setPaddleId($data["subscription_id"])
			->setQuantity($data["quantity"] ?? 1)
			->setState($data["state"])
			->setSignUpDate(new DateTime($data["signup_date"]))
			->setCustomer($this->customerConverter->hydrate($data, $user))
			->setPlan($this->planRepository->findOneBy(["paddleId" => $data["plan_id"]]));

		if ($data["next_payment"]["date"] ?? null) {
			$subscription->setNextPaymentDate(new DateTime($data["next_payment"]["date"]));
		}

		return $subscription;
	}
}