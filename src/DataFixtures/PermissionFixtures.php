<?php

namespace App\DataFixtures;

use App\Entity\Download;
use App\Entity\Permission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PermissionFixtures extends Fixture
{
    const PERMISSION_IDS = [
        Download::TYPE_ALL_ALBUMS,
        Download::TYPE_BIOMETRIC_2,
        Download::TYPE_BIOMETRIC_4,
        Download::TYPE_E_SCHOOL_ALBUM,
        Download::TYPE_E_SCHOOL_ALBUM_WITH_STUDENT_NAME_SURNAME,
        Download::TYPE_EXCEL,
        Download::TYPE_EXECUTIVE,
        Download::TYPE_HEADSHOT_2,
        Download::TYPE_HEADSHOT_4,
        Download::TYPE_HEADSHOT_8,
        Download::TYPE_TRANSCRIPT,
        Download::TYPE_YEARBOOK,
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PERMISSION_IDS as $permissionId) {
            $permission = new Permission($permissionId);

            $manager->persist($permission);
        }

        $manager->flush();
    }
}
