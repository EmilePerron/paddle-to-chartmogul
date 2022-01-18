<?php

namespace App\Message;

class SyncRequest
{
	public function __construct(
		public int $userId
	)
	{
	}
}
