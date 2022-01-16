<?php

namespace App\Synchronizer;

use App\Entity\DataSource;
use App\Entity\User;
use ChartMogul\Configuration;
use ChartMogul\DataSource as ChartMogulDataSource;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Paddle\API;

class Synchronizer
{
	private API $paddle;
	private DataSource $dataSource;
	private User $user;

	public function __construct(
		private PlanSynchronizer $planSynchronizer,
		private SubscriptionSynchronizer $subscriptionSynchronizer,
		private CustomerSynchronizer $customerSynchronizer,
		private PaymentSynchronizer $paymentSynchronizer,
		private EntityManagerInterface $entityManager,
	)
	{
	}
	
	public function sync(User $user): bool
	{
		if (!$this->canSync($user)) {
			return false;
		}

		$this->user = $user;
		$this->initPaddle();
		$this->initChartMogul();

		$this->syncPlans();
		$this->syncSubscriptions();


		$user->setLastSyncDate(new DateTime());
		$this->entityManager->persist($user);

		$this->entityManager->flush();

		return true;
	}

	private function canSync(User $user): bool
	{
		return $user->getPaddleApiKey() &&
			$user->getPaddleVendorId() &&
			$user->getChartMogulApiKey();
	}

	private function initPaddle(): void
	{
		$this->paddle = new API($this->user->getPaddleVendorId(), $this->user->getPaddleApiKey());
	}

	private function initChartMogul(): void
	{
		// Initialize API connections
		Configuration::getDefaultConfiguration()->setApiKey($this->user->getChartMogulApiKey());

		// Get (or create) the ChartMogul data source
		$this->dataSource = $this->initDataSource($this->user);
	}

	/**
	 * Fetch the ChartMogul data source (or create it if it doesn't exist yet).
	 */
	private function initDataSource(): DataSource
	{
		if ($this->user->getDataSource()) {
			return $this->user->getDataSource();
		}

		/** @var ChartMogulDataSource $rawDataSource */
		$rawDataSource = ChartMogulDataSource::create([
			"name" => "Paddle to ChartMogul Synchronization"
		]);

		$dataSource = (new DataSource())
			->setChartMogulId($rawDataSource->uuid)
			->setName($rawDataSource->name);
		$this->user->setDataSource($dataSource);

		$this->entityManager->persist($dataSource);
		$this->entityManager->persist($this->user);
		$this->entityManager->flush();

		return $dataSource;
	}

	private function syncPlans()
	{
		// Fetch and sync plans
		$plans = $this->planSynchronizer->fetch($this->user, $this->paddle);
		
		foreach ($plans as $plan) {
			$syncedPlan = $this->planSynchronizer->sync($plan, $this->dataSource);
			$this->entityManager->persist($syncedPlan);
		}

		$this->entityManager->flush();
	}

	private function syncSubscriptions()
	{
		// First, create/sync customers
		$subscriptions = $this->subscriptionSynchronizer->fetch($this->user, $this->paddle);
		foreach ($subscriptions as $subscription) {
			$syncedCustomer = $this->customerSynchronizer->sync($subscription->getCustomer(), $this->dataSource);
			$this->entityManager->persist($syncedCustomer);
			$this->entityManager->persist($subscription);
		}
		$this->entityManager->flush();
		
		// Then, process payments as invoices
		$payments = $this->paymentSynchronizer->fetch($this->user, $this->paddle);

		foreach ($payments as $payment) {
			// First, fetch & sync the actual subscription
			$syncedPayment = $this->paymentSynchronizer->sync($payment, $this->dataSource);

			if ($syncedPayment) {
				$this->entityManager->persist($syncedPayment);
			}
		}
	}
}