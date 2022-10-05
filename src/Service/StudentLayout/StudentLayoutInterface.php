<?php

namespace App\Service\StudentLayout;

use App\Entity\Classroom;
use App\Entity\Student;

interface StudentLayoutInterface
{
    /**
     * @param Student[] $students
     */
    public function create(Classroom $classroom, array $students): string;
}
