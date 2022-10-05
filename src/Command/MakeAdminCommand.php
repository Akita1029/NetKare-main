<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'make:admin',
    description: 'Creates an admin account',
)]
class MakeAdminCommand extends Command
{
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email address for admin')
            ->addArgument('password', InputArgument::REQUIRED, 'Password for admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $admin = new Admin;
        $admin->setEmail($input->getArgument('email'));
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, $input->getArgument('password')));

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
