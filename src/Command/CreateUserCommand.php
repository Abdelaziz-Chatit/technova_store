<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user with a specific role',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('name', InputArgument::REQUIRED, 'User full name')
            ->addOption('role', 'r', InputOption::VALUE_OPTIONAL, 'User role (ROLE_USER, ROLE_RESPONSABLE, ROLE_ADMIN)', 'ROLE_USER')
            ->addOption('phone', 'p', InputOption::VALUE_OPTIONAL, 'User phone number');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name');
        $role = $input->getOption('role');
        $phone = $input->getOption('phone');

        // Validate role
        $validRoles = ['ROLE_USER', 'ROLE_RESPONSABLE', 'ROLE_ADMIN'];
        if (!in_array($role, $validRoles)) {
            $io->error(sprintf('Invalid role. Must be one of: %s', implode(', ', $validRoles)));
            return Command::FAILURE;
        }

        // Check if user already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error(sprintf('User with email "%s" already exists!', $email));
            return Command::FAILURE;
        }

        // Create user
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setPhone($phone);

        // Assign roles: ensure ROLE_ADMIN also has RESPONSABLE and USER privileges
        if ($role === 'ROLE_ADMIN') {
            $user->setRoles(['ROLE_ADMIN', 'ROLE_RESPONSABLE', 'ROLE_USER']);
        } elseif ($role === 'ROLE_RESPONSABLE') {
            $user->setRoles(['ROLE_RESPONSABLE', 'ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }
        
        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Save user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf(
            'User created successfully!\nEmail: %s\nName: %s\nRole: %s',
            $email,
            $name,
            $role
        ));

        return Command::SUCCESS;
    }
}

