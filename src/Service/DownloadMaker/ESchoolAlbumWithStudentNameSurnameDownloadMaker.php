<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;
use App\Repository\AlbumPhotoRepository;
use App\Service\ObjectStorage;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;

class ESchoolAlbumWithStudentNameSurnameDownloadMaker extends ZipMaker implements DownloadMakerInterface
{
    const FORMAT = 'jpeg';

    private ObjectStorage $objectStorage;
    private AlbumPhotoRepository $albumPhotoRepository;
    private Imagine $imagine;

    public function __construct(ObjectStorage $objectStorage, AlbumPhotoRepository $albumPhotoRepository)
    {
        $this->objectStorage = $objectStorage;
        $this->albumPhotoRepository = $albumPhotoRepository;
        $this->imagine = new Imagine;

        parent::__construct();
    }

    public function make(School $school): string
    {
        foreach ($school->getClassrooms() as $classroom) {
            $albumPhotos = $this->albumPhotoRepository->getMainAlbumPhotoByClassroom($classroom);

            foreach ($albumPhotos as $albumPhoto) {
                $object = $this->objectStorage->getObject($albumPhoto->getImage()->getAsset()->getName());

                $image = $this->imagine->load($object['Body']->getContents());
                $image = $image->thumbnail(new Box(133, 171));

                $student = $albumPhoto->getStudent();

                $name = $school->getName() . '/' . $classroom->getName() . '/' . $student->getSchoolNumber() . ' ' . $student->getName() . ' ' . $student->getSurname() . '.' . self::FORMAT;
                $content = $image->get(self::FORMAT);

                $this->addFromString($name, $content);
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
