<?php

namespace App\DataFixtures;

use App\Entity\Dealer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DealerFixtures extends Fixture
{
    public const DEALER_1 = 'dealer_1';
    public const DEALER_2 = 'dealer_2';
    public const DEALER_3 = 'dealer_3';

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $dealer = new Dealer;
        $dealer->setEmail("dealer1@dealer.com");
        $dealer->setPassword($this->passwordHasher->hashPassword($dealer, '123456'));
        $dealer->setCompanyName("Dealer 1");
        $dealer->setAuthorizedPersonName("Dealer 1 Person");
        $dealer->setAddress("Dealer 1 Address");
        $dealer->setPhone("905555555555");

        $manager->persist($dealer);

        $this->addReference(self::DEALER_1, $dealer);

        $dealer = new Dealer;
        $dealer->setEmail("dealer2@dealer.com");
        $dealer->setPassword($this->passwordHasher->hashPassword($dealer, '123456'));
        $dealer->setCompanyName("Dealer 2");
        $dealer->setAuthorizedPersonName("Dealer 2 Person");
        $dealer->setAddress("Dealer 2 Address");
        $dealer->setPhone("905555555555");

        $manager->persist($dealer);

        $this->addReference(self::DEALER_2, $dealer);

        $dealer = new Dealer;
        $dealer->setEmail("dealer3@dealer.com");
        $dealer->setPassword($this->passwordHasher->hashPassword($dealer, '123456'));
        $dealer->setCompanyName("Dealer 3");
        $dealer->setAuthorizedPersonName("Dealer 3 Person");
        $dealer->setAddress("Dealer 3 Address");
        $dealer->setPhone("905555555555");

        $manager->persist($dealer);

        $this->addReference(self::DEALER_3, $dealer);

        $manager->flush();
    }
}
