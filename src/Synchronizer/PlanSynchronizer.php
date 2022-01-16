<?php

namespace App\Synchronizer;

use App\Converter\PlanConverter;
use App\Entity\DataSource;
use App\Entity\Plan;
use App\Entity\User;
use ChartMogul\Plan as ChartMogulPlan;
use DateTime;
use Paddle\API;

class PlanSynchronizer
{
	public function __construct(
		private PlanConverter $planConverter
	)
	{
	}

	/**
	 * @return array<mixed,Plan>
	 */
	public function fetch(User $user, API $paddle)
	{
		$availablePlansData = $paddle->subscription()->listPlans();
		$plans = array_map(function ($planData) use ($user) {
			return $this->planConverter->hydrate($planData, $user);
		}, $availablePlansData);
		
		return $plans;
	}
	
	public function sync(Plan $plan, DataSource $dataSource): Plan
	{
		$plan->setLastSyncDate(new DateTime());
		$chartMogulData = $this->planConverter->convert($plan);
		
		// Create the plan if it doesn't exist
		if (!$plan->getChartMogulId()) {
			$chartMogulData["data_source_uuid"] = $dataSource->getChartMogulId();
			$chartMogulPlan = ChartMogulPlan::create($chartMogulData);

			$plan->setChartMogulId($chartMogulPlan->uuid);
			
			return $plan;
		}
		
		/** @var ChartMogulPlan $chartMogulPlan */
		ChartMogulPlan::update(
			["plan_uuid" => $plan->getChartMogulId()], 
			$chartMogulData
		);
		
		return $plan;
	}
}