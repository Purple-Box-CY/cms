<?php

namespace App\Command\Redis;

use App\Service\Infrastructure\RedisService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'app:redis:keys',
    description: 'Keys in redis',
)]
//EXAMPLE: php bin/console app:redis:keys feed.contents:*
class RedisKeysCommand extends Command
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
            name: 'key_pattern',
            mode: InputArgument::OPTIONAL,
            description: 'Key pattern in redis. Default *. Example - feed.contents:*',
            default: '*'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->redisService->isEnable()) {
            $output->writeln('Redis is not enabled on project');
        }

        $pattern = $input->getArgument('key_pattern');

        $keys = array_map(static function(string $key): array {
            return [$key];
        }, $this->redisService->getKeys($pattern));

        $table = new Table($output);
        $table->setHeaders(['Key'])
            ->setRows($keys)
            ->render();

        return Command::SUCCESS;
    }
}
