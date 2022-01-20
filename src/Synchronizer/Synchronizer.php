<?php

namespace App\Synchronizer;

use App\Entity\DataSource;
use App\Entity\SyncLog;
use App\Entity\User;
use ChartMogul\Configuration;
use ChartMogul\DataSource as ChartMogulDataSource;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Paddle\API;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

class Synchronizer
{
	private API $paddle;
	private DataSource $dataSource;
	private User $user;
	private SyncLog $log;

	public function __construct(
		private PlanSynchronizer $planSynchronizer,
		private SubscriptionSynchronizer $subscriptionSynchronizer,
		private CustomerSynchronizer $customerSynchronizer,
		private PaymentSynchronizer $paymentSynchronizer,
		private EntityManagerInterface $entityManager,
		private LoggerInterface $logger,
	)
	{
	}

	public function sync(User $user): bool
	{
		if (!$this->canSync($user)) {
			return false;
		}

		$this->user = $user;
		$this->initLog();

		try {
			$this->writeLog("Setting up connection to Paddle...");
			$this->initPaddle();
		} catch (Exception $e) {
			$this->writeLog("❌ ERROR: Could not initiate API connection to Paddle. Please check your API credentials in the settings page.");
			$this->logger->critical($e->getMessage(), $e->getTrace());
			return $this->endSyncProcess(true);
		}

		try {
			$this->writeLog("Setting up connection to ChartMogul...");
			$this->initChartMogul();
		} catch (Exception $e) {
			$this->writeLog("❌ ERROR: Could not initiate API connection to ChartMogul. Please check your API credentials in the settings page.");
			$this->logger->critical($e->getMessage(), $e->getTrace());
			return $this->endSyncProcess(true);
		}

		try {
			$this->syncPlans();
			$this->syncSubscriptions();
			$this->writeLog("ChartMogul sync completed.");
		} catch (Exception $e) {
			$this->writeLog("❌ ERROR: An error occured while fetching or syncing data. For more information, contact us and ask us about the error from log entry #" . $this->log->getId());
			$this->logger->critical($e->getMessage(), $e->getTrace());
			return $this->endSyncProcess(true);
		}

		try {
			$this->writeLog("Saving sync status locally...");
			$this->entityManager->flush();
		} catch (Exception $e) {
			$this->entityManager->clear();
			$this->entityManager->refresh($this->user);
			$this->writeLog("❌ ERROR: We failed to save the status of your data sync to our servers. Your data was likely synced correctly to ChartMogul, but subsequent syncs may fail or be incorrect. Please contact us for more information");
			$this->logger->critical($e->getMessage(), $e->getTrace());
			return $this->endSyncProcess(true);
		}

		$this->endSyncProcess(false);

		return true;
	}

	private function canSync(User $user): bool
	{
		return $user->getPaddleApiKey() &&
			$user->getPaddleVendorId() &&
			$user->getChartMogulApiKey();
	}

	private function initLog(): void
	{
		$this->log = new SyncLog($this->user);
		$this->writeLog("Starting synchronization process");

		$this->entityManager->persist($this->log);
		$this->entityManager->flush();
	}

	private function initPaddle(): void
	{
		$this->paddle = new API($this->user->getPaddleVendorId(), $this->user->getPaddleApiKey());

		// Sending a test request to detect API connection issues early
		$this->paddle->subscription()->listPlans();
	}

	private function initChartMogul(): void
	{
		// Initialize API connections
		Configuration::getDefaultConfiguration()->setApiKey($this->user->getChartMogulApiKey());

		// Try a basic request to ChartMogul to catch API connection issues early on.
		ChartMogulDataSource::all();

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
			"name" => "Paddle to ChartMogul synchronization"
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
		$this->writeLog("Fetching plans from Paddle...");
		$plans = $this->planSynchronizer->fetch($this->user, $this->paddle);

		$this->writeLog("Syncing plans to ChartMogul...");
		foreach ($plans as $plan) {
			$syncedPlan = $this->planSynchronizer->sync($plan, $this->dataSource);
			$this->entityManager->persist($syncedPlan);
		}

		$this->entityManager->flush();
	}

	private function syncSubscriptions()
	{
		$this->writeLog("Fetching subscriptions from Paddle...");
		$subscriptionsToCancel = [];
		foreach ($this->user->getSubscriptions()->toArray() as $existingSubscription) {
			$subscriptionsToCancel[$existingSubscription->getId()] = $existingSubscription;
		}

		// First, create/sync customers
		$this->writeLog("Syncing customers and subscriptions to ChartMogul...");
		$subscriptions = $this->subscriptionSynchronizer->fetch($this->user, $this->paddle);
		foreach ($subscriptions as $subscription) {
			$syncedCustomer = $this->customerSynchronizer->sync($subscription->getCustomer(), $this->dataSource);
			$this->entityManager->persist($syncedCustomer);
			$this->entityManager->persist($subscription);

			if ($subscription->getState() == "active" && isset($subscriptionsToCancel[$subscription->getId()])) {
				unset($subscriptionsToCancel[$subscription->getId()]);
			}
		}
		$this->entityManager->flush();

		// Then, process payments as invoices
		$this->writeLog("Fetching payments from Paddle...");
		$payments = $this->paymentSynchronizer->fetch($this->user, $this->paddle);

		$this->writeLog("Syncing payments to ChartMogul...");
		foreach ($payments as $payment) {
			// If a payment can't be associated to a subscription, we can't sync it.
			if (!$payment->getSubscription()) {
				$this->user->removePayment($payment);
				continue;
			}

			$syncedPayment = $this->paymentSynchronizer->sync($payment, $this->dataSource);
			$this->entityManager->persist($syncedPayment);
		}

		// Finally, process cancellations
		$this->writeLog("Syncing cancelled subscriptions to ChartMogul...");
		foreach ($subscriptionsToCancel as $subscriptionToCancel) {
			$this->subscriptionSynchronizer->cancel($subscriptionToCancel);
			$this->entityManager->persist($subscriptionToCancel);
		}
	}

	private function writeLog(string $line): self
	{
		$content = $this->log->getContent() ?: "";
		$content .= "[" . date("H:i:s") . "] $line\n";

		$this->log->setContent($content);

		return $this;
	}

	private function endSyncProcess(bool $failed = false)
	{
		if (!$failed) {
			$this->writeLog("All done - wrapping up...");
		}

		$this->user->setLastSyncDate(new DateTime());
		$this->log->setEndDate(new DateTime())
				->setHasFailed($failed);

		$this->entityManager->persist($this->user);
		$this->entityManager->persist($this->log);

		$this->entityManager->flush();

		return !$failed;
	}
}
