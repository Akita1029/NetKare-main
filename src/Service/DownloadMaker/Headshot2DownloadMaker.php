<?php

namespace App\Service\DownloadMaker;

use App\Entity\AlbumPhoto;
use App\Entity\School;
use App\Repository\AlbumPhotoRepository;

class Headshot2DownloadMaker extends ZipMaker implements DownloadMakerInterface
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

            foreach (array_chunk($albumPhotos->toArray(), 4) as $albumPhotos) {
                $classroom = $albumPhotos[0]->getStudent()->getClassroom();

                $name = $school->getName() . '/' . $classroom->getName() . '/' . array_reduce($albumPhotos, function (string $carry, AlbumPhoto $albumPhoto) {
                    $student = $albumPhoto->getStudent();
                    return $carry . ' ' . $student->getSchoolNumber() . ' ' . $student->getName() . ' ' . $student->getSurname();
                }) . '.' . self::FORMAT;

                $content = $this->headShot2->create($albumPhotos);

                $this->addFromString($name, $content);
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
