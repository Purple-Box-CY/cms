<?php

namespace App\Command\Redis;

use App\Service\Infrastructure\RedisService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:redis:remove',
    description: 'Remove data by key from redis',
)]
//EXAMPLE: php bin/console app:redis:remove challenges.top
class RedisRemoveCommand extends Command
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
        $io = new SymfonyStyle($input, $output);

        if (!$this->redisService->isEnable()) {
            $output->writeln('Redis is not enabled on project');
            return Command::FAILURE;
        }

        $key = $input->getArgument('key');
        if (!$key) {
            $io->error('Key is required');
            return Command::FAILURE;
        }

        $this->redisService->removeByPattern(
            pattern: $key,
            withPrefix: false
        );

        $io->writeln('Success');

        return Command::SUCCESS;
    }
}
