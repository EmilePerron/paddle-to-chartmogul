<?php

namespace App\Converter;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;

class CustomerConverter
{
	public function __construct(
		private CustomerRepository $repository
	)
	{
	}

	public function convert(Customer $customer): array
	{
		return [
            "name" => $customer->getEmail(),
			"email" => $customer->getEmail(),
			"external_id" => $customer->getPaddleId(),
        ];
	}

	/**
	 * This one takes in the data of a subscription, seeing as 
	 * there is no specific endpoint for customers in Paddle's API.
	 */
	public function hydrate(array $subscriptionData, User $user): Customer
	{
		$customer = $this->repository->findOneBy(["paddleId" => $subscriptionData["user_id"]]);

		if (!$customer) {
			$customer = new Customer();
		}

		return $customer
			->setPaddleId($subscriptionData["user_id"])
			->setEmail($subscriptionData["user_email"])
			->setOwner($user);
	}
}