<?php

namespace App\DataFixtures;

use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $school = new School;
        $school->setOwner($this->getReference(DealerFixtures::DEALER_1));
        $school->setName("School 1");

        $manager->persist($school);

        $school = new School;
        $school->setOwner($this->getReference(DealerFixtures::DEALER_2));
        $school->setName("School 2");

        $manager->persist($school);

        $school = new School;
        $school->setOwner($this->getReference(DealerFixtures::DEALER_3));
        $school->setName("School 3");

        $manager->persist($school);

        $manager->flush();
    }
}
