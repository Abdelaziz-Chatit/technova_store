<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:show-user-roles', description: 'Show all user roles')]
class ShowUserRolesCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findAll();
        
        $output->writeln('Current User Roles:');
        $output->writeln('===================');
        
        foreach ($users as $user) {
            $roles = $user->getRoles();
            $output->writeln($user->getEmail() . ': ' . implode(', ', $roles));
        }
        
        return Command::SUCCESS;
    }
}
