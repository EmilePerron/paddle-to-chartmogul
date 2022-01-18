<?php

namespace App\Command;

use App\Message\SyncRequest;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ScheduleSyncsCommand extends Command
{
    protected static $defaultName = 'app:schedule-syncs';
	protected static $defaultDescription = 'Schedules data sync for the messenger to process.';

	public function __construct(
		private UserRepository $userRepository,
		private MessageBusInterface $bus
	)
	{
		parent::__construct();
	}

    protected function configure(): void
    {
        $this->setHelp(
			"This command adds a message to the queue for each user who is due to be synced. " .
			"The Messenger will consume these messages and sync the users data when it gets to it.\n\n" .
			"You don't have to provide any configuration or parameters for this command."
		);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$now = new DateTime();

		foreach ($this->userRepository->findAllConfigured() as $user) {
			/** @var DateTime $lastSyncDate */
			$lastSyncDate = $user->getLastSyncDate() ?: new DateTime("1 year ago");
			$syncDueDate = $lastSyncDate->modify("+ " . $user->getFrequency());
			$userId = $user->getId();

			$output->write("User #$userId: ");

			if ($now >= $syncDueDate) {
				$message = new SyncRequest($userId);
				$this->bus->dispatch($message);

				$output->writeLn("added to sync queue ⏱");
			} else {
				$output->writeLn("not due yet.");
			}
		}

        return Command::SUCCESS;
    }
}
