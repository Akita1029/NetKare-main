<?php

namespace App\Service\DownloadMaker;

use App\Entity\AlbumPhoto;
use App\Entity\School;
use App\Repository\AlbumPhotoRepository;
use App\Repository\StudentRepository;

class ExecutiveDownloadMaker extends ZipMaker implements DownloadMakerInterface
{
    const FORMAT = 'jpeg';

    private StudentRepository $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;

        parent::__construct();
    }

    public function make(School $school): string
    {
        foreach ($school->getClassrooms() as $classroom) {
            $students = $this->studentRepository->findByClassroomWithMainAlbumPhoto($classroom);

            $studentsLength = count($students);

            $chunkLength = 30;
            $service = $this->executiveAlbum30;

            if ($studentsLength > 30) {
                $chunkLength = 42;
                $service = $this->executiveAlbum42;
            }

            if ($studentsLength > 42) {
                $chunkLength = 56;
                $service = $this->executiveAlbum56;
            }

            foreach (array_chunk($students, $chunkLength) as $students) {
                $name = $school->getName() . '/' . $classroom->getName() .  '-1.' . self::FORMAT;

                $content = $service->create($classroom, $students);

                $this->addFromString($name, $content);
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
