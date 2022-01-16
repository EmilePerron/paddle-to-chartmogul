<?php

namespace App\Util;

use App\Entity\Subscription;
use DateTimeImmutable;
use DateTimeInterface;

class PeriodCalculator
{
	public function getStartDate(Subscription $subscription): DateTimeInterface
	{
		$endDate = DateTimeImmutable::createFromMutable($subscription->getNextPaymentDate());
		$plan = $subscription->getPlan();
		$unit = $plan->getBillingType();
		$period = $plan->getBillingPeriod();

		return $endDate->modify("-$period $unit");
	}
}