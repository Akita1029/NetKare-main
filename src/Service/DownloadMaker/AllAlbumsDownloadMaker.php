<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;
use App\Service\ObjectStorage;

class AllAlbumsDownloadMaker extends ZipMaker implements DownloadMakerInterface
{
    const FORMAT = 'jpeg';

    private ObjectStorage $objectStorage;

    public function __construct(ObjectStorage $objectStorage)
    {
        $this->objectStorage = $objectStorage;

        parent::__construct();
    }

    public function make(School $school): string
    {
        foreach ($school->getAlbums() as $a) {
            foreach ($a->getPhotos() as $p) {
                $s = $p->getStudent();
                $object = $this->objectStorage->getObject($p->getImage()->getAsset()->getName());

                $name = $a->getName() . '/' . $s->getClassroom()->getName() . '/' . $s->getSchoolNumber() . ' ' . $s->getName() . ' ' . $s->getSurname() . '.' . self::FORMAT;

                $this->addFromString($name, $object['Body']->getContents());
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
