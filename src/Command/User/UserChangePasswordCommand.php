<?php

namespace App\Command\User;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use App\Service\Infrastructure\LogService;
use App\Validator\UserValidator as Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:user:change-password',
    description: 'Change password user'
)]
final class UserChangePasswordCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly Validator                   $validator,
        private readonly AdminUserRepository         $users,
        private readonly LogService                  $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of an existing user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain new password of the user')
            ->setHelp(<<<'HELP'
                The <info>%command.name%</info> command change password user from the database:

                  <info>php %command.full_name%</info> <comment>email</comment>

                If you omit the argument, the command will ask you to
                provide the missing value:

                  <info>php %command.full_name%</info>
                HELP,
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('email') && null !== $input->getArgument('password')) {
            return;
        }

        $this->io->title('Change password User Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:user:change-password email',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);

        $email = $this->io->ask('Email', null, $this->validator->validateEmail(...));
        $input->setArgument('email', $email);


        // Ask for the password if it's not defined
        /** @var string|null $password */
        $password = $input->getArgument('password');

        if (null !== $password) {
            $this->io->text(' > <info>Password</info>: '.u('*')->repeat(u($password)->length()));
        } else {
            $password = $this->io->askHidden('Password (your type will be hidden)',
                $this->validator->validatePassword(...));
            $input->setArgument('password', $password);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $email */
        $email = $input->getArgument('email');
        $email = $this->validator->validateEmail($email);

        /** @var string $plainPassword */
        $plainPassword = $input->getArgument('password');

        /** @var AdminUser|null $user */
        $user = $this->users->findOneByEmail($email);

        if (null === $user) {
            throw new RuntimeException(sprintf('User with email "%s" not found.', $email));
        }

        $userId = $user->getId();

        // See https://symfony.com/doc/5.4/security.html#registering-the-user-hashing-passwords
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userUsername = $user->getUsername();
        $userEmail = $user->getEmail();

        $this->io->success(sprintf('User "%s" (ID: %d, email: %s) was successfully changed password.',
            $userUsername,
            $userId,
            $userEmail));

        // Logging is helpful and important to keep a trace of what happened in the software runtime flow.
        // See https://symfony.com/doc/current/logging.html
        $this->logger->info('User "{username}" (ID: {id}, email: {email}) was successfully edit.',
            ['username' => $userUsername, 'id' => $userId, 'email' => $userEmail]);

        return Command::SUCCESS;
    }
}
