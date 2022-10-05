<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private const EMAIL = 'admin@admin.com';
    private const PASSWORD = '123456';

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setEmail(self::EMAIL);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, self::PASSWORD));
        $manager->persist($admin);

        $manager->flush();
    }
}
