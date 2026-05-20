<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Command;

use App\Shared\Infrastructure\Security\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:reset-user-password',
    description: 'Resets the password of an existing API user',
)]
final class ResetUserPasswordCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'New password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        $user = $this->userRepository->findByEmail($email);

        if (null === $user) {
            $output->writeln(sprintf('<error>User "%s" not found.</error>', $email));

            return Command::FAILURE;
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->userRepository->save($user);

        $output->writeln(sprintf('<info>Password updated for "%s".</info>', $email));

        return Command::SUCCESS;
    }
}
