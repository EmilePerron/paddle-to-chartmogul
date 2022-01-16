<?php

namespace App\Converter;

use App\Entity\Plan;
use App\Entity\User;
use App\Repository\PlanRepository;

class PlanConverter
{
	public function __construct(
		private PlanRepository $repository
	)
	{
	}

	public function convert(Plan $plan): array
	{
		$unit = $plan->getBillingType();
		$count = $plan->getBillingPeriod();

		if ($unit == "week") {
			$unit = "day";
			$count *= 7;
		}

		return [
            "name" => $plan->getName(),
            "interval_count" => $count,
            "interval_unit" => $unit,
			"external_id" => $plan->getPaddleId(),
        ];
	}

	public function hydrate(array $data, User $user): Plan
	{
		$plan = $this->repository->findOneBy(["paddleId" => $data["id"]]);

		if (!$plan) {
			$plan = new Plan();
		}

		return $plan
			->setOwner($user)
			->setPaddleId($data["id"])
			->setName($data["name"])
			->setBillingType($data["billing_type"])
			->setBillingPeriod($data["billing_period"])
			->setTrialDays($data["trial_days"])
			->setInitialPrice($data["initial_price"]["USD"] ?? array_values($data["initial_price"])[0] ?? 0)
			->setRecurringPrice($data["recurring_price"]["USD"] ?? array_values($data["recurring_price"])[0]);
	}
}