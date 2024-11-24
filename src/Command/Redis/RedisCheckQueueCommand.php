<?php

namespace App\Command\Redis;

use App\Service\Infrastructure\RedisKeys;
use App\Service\Infrastructure\RedisService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:redis:check-queue',
    description: 'Check queue in redis',
)]
class RedisCheckQueueCommand extends Command
{
    public function __construct(
        private RedisService $redisService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->redisService->isQueueEnable()) {
            $output->writeln('Redis queue is not enabled on project');
        }

        try {
            $message1 = 'check_queue1';
            $message2 = 'check_queue2';
            $this->redisService->pushToQueue(RedisKeys::QUEUE_MAIN, $message1);
            $this->redisService->pushToQueue(RedisKeys::QUEUE_MAIN, $message2);
            $result1 = $this->redisService->popFromQueue(RedisKeys::QUEUE_MAIN);
            $result2 = $this->redisService->popFromQueue(RedisKeys::QUEUE_MAIN);

            if ($message1 === $result1 && $message2 === $result2) {
                $output->writeln('<info>Success</info>');
            } else {
                $output->writeln('<error>Fail. Messages from queue dont match</error>');
            }
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>Failed to check redis queue</error>. Error: %s', $e->getMessage()));
        }

        return Command::SUCCESS;
    }
}
