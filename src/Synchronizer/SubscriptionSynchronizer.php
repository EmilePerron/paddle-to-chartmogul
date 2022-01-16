<?php

namespace App\Synchronizer;

use App\Converter\PaymentConverter;
use App\Converter\SubscriptionConverter;
use App\Entity\DataSource;
use App\Entity\Subscription;
use App\Entity\User;
use ChartMogul\Subscription as ChartMogulSubscription;
use DateTime;
use Paddle\API;

class SubscriptionSynchronizer
{
	public function __construct(
		private SubscriptionConverter $subscriptionConverter,
		private PaymentConverter $paymentConverter,
		private PaymentSynchronizer $paymentSynchronizer,
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
}