<?php

namespace App\Synchronizer;

use App\Converter\CustomerConverter;
use App\Entity\DataSource;
use App\Entity\Customer;
use ChartMogul\Customer as ChartMogulCustomer;
use DateTime;

class CustomerSynchronizer
{
	public function __construct(
		private CustomerConverter $customerConverter
	)
	{
	}
	
	public function sync(Customer $customer, DataSource $dataSource): Customer
	{
		$customer->setLastSyncDate(new DateTime());
		$chartMogulData = $this->customerConverter->convert($customer);
		
		// Create the customer if it doesn't exist
		if (!$customer->getChartMogulId()) {
			$chartMogulData["data_source_uuid"] = $dataSource->getChartMogulId();
			$chartMogulCustomer = ChartMogulCustomer::create($chartMogulData);
			
			$customer->setChartMogulId($chartMogulCustomer->uuid);
			
			return $customer;
		}
		
		/** @var ChartMogulCustomer $chartMogulCustomer */
		ChartMogulCustomer::update(
			["customer_uuid" => $customer->getChartMogulId()], 
			$chartMogulData
		);
		
		return $customer;
	}
}