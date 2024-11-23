<?php

namespace App\Command\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:users:set-unsubscribe',
    description: 'set users unsubscribe',
)]
class UserSetUnsubscribeCommand extends Command
{
    public function __construct(
        private readonly UserService    $userService,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $page = 1;
        $limit = 300;
        do {
            $offset = ($page++ - 1) * $limit;
            $qb = $this->userRepository->createQueryBuilder('c');
            $query = $qb
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('c.id', 'DESC');
            $users = $query
                ->getQuery()
                ->getResult();

            foreach ($users as $user) {
                /** @var User $user */
                if ($user->isAnonym()) {
                    $io->writeln('SKIP: '.$user->getId().' - '.$user->getUsername());
                    continue;
                }
                $user->getInfo()->setIsUnsubscribed(true);
                $this->userService->saveUser($user);
                $io->info($user->getId().' - '.$user->getUsername());
                usleep(10000);
            }
            usleep(200000);
        } while (count($users) > 0);

        return Command::SUCCESS;
    }
}
