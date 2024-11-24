<?php

namespace App\Command\Redis;

use App\Service\Infrastructure\RedisService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'app:redis:key-value',
    description: 'Value by key in redis',
)]
//EXAMPLE: php bin/console app:redis:key-value challenges.top
class RedisKeyValueCommand extends Command
{
    public function __construct(
        private RedisService $redisService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            name: 'key',
            mode: InputArgument::REQUIRED,
            description: 'Key in redis',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->redisService->isEnable()) {
            $output->writeln('Redis is not enabled on project');
            return Command::FAILURE;
        }

        $key = $input->getArgument('key');

        $value = $this->redisService->get($key, false);
        dump($value);

        return Command::SUCCESS;
    }
}
