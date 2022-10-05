<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;
use App\Service\StudentLayout\Transcript;

class TranscriptDownloadMaker extends ZipMaker implements DownloadMakerInterface
{
    const FORMAT = 'jpeg';

    private Transcript $transcript;

    public function __construct(Transcript $transcript)
    {
        $this->transcript = $transcript;

        parent::__construct();
    }

    public function make(School $school): string
    {
        foreach ($school->getClassrooms() as $classroom) {
            foreach (array_chunk($classroom->getStudents()->toArray(), 44) as $key => $students) {
                $name = $students[0]->getClassroom()->getName() .  '-' . $key . '.' . self::FORMAT;
                $content = $this->transcript->create($classroom, $students);

                $this->addFromString($name, $content);
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
