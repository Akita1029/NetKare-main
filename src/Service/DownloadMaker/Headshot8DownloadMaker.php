<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;
use App\Repository\AlbumPhotoRepository;

class Headshot8DownloadMaker extends ZipMaker implements DownloadMakerInterface
{
    const FORMAT = 'jpeg';

    private AlbumPhotoRepository $albumPhotoRepository;

    public function __construct(AlbumPhotoRepository $albumPhotoRepository)
    {
        $this->albumPhotoRepository = $albumPhotoRepository;

        parent::__construct();
    }

    public function make(School $school): string
    {
        foreach ($school->getClassrooms() as $classroom) {
            $albumPhotos = $this->albumPhotoRepository->getMainAlbumPhotoByClassroom($classroom);

            foreach ($albumPhotos as $albumPhoto) {
                $student = $albumPhoto->getStudent();
                $name = $school->getName() . '/' . $student->getClassroom()->getName() . '/' . $student->getSchoolNumber() . ' ' . $student->getName() . ' ' . $student->getSurname() . '.' . self::FORMAT;

                $content = $this->headShot8->create([$albumPhoto]);

                $this->addFromString($name, $content);
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
