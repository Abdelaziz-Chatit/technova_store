<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fix-manager-roles', description: 'Fix manager user roles')]
class FixManagerRolesCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $manager = $this->userRepository->findOneBy(['email' => 'manager@example.com']);
        
        if (!$manager) {
            $output->writeln('Manager user not found');
            return Command::FAILURE;
        }
        
        // Ensure manager has only ROLE_RESPONSABLE and ROLE_USER
        $manager->setRoles(['ROLE_RESPONSABLE', 'ROLE_USER']);
        $this->em->flush();
        
        $output->writeln('Manager roles updated to: ' . implode(', ', $manager->getRoles()));
        
        return Command::SUCCESS;
    }
}
