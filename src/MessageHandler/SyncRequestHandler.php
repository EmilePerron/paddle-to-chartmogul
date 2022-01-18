<?php

namespace App\MessageHandler;

use App\Message\SyncRequest;
use App\Repository\UserRepository;
use App\Synchronizer\Synchronizer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SyncRequestHandler implements MessageHandlerInterface
{
	public function __construct(
		private UserRepository $userRepository,
		private Synchronizer $synchronizer
	)
    {
    }

    public function __invoke(SyncRequest $syncRequest)
    {
        $user = $this->userRepository->find($syncRequest->userId);

        if (!$user) {
			return;
		}

		$this->synchronizer->sync($user);
    }
}
