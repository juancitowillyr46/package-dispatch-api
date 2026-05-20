<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Command;

use App\Shared\Infrastructure\Security\Entity\User;
use App\Shared\Infrastructure\Security\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates an API user for JWT authentication',
)]
final class CreateUserCommand extends Command
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
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create the user with ROLE_ADMIN');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');
        $roles = $input->getOption('admin') ? ['ROLE_ADMIN'] : ['ROLE_USER'];

        if (null !== $this->userRepository->findByEmail($email)) {
            $output->writeln(sprintf('<error>User "%s" already exists.</error>', $email));

            return Command::FAILURE;
        }

        $user = new User($email, $roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->userRepository->save($user);

        $output->writeln(sprintf('<info>User "%s" created.</info>', $email));

        return Command::SUCCESS;
    }
}
