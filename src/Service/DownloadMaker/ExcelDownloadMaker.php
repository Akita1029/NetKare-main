<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelDownloadMaker implements DownloadMakerInterface
{
    public function make(School $school): string
    {
        $source = [];

        foreach ($school->getClassrooms() as $classroom) {
            foreach ($classroom->getStudents() as $student) {
                $source = [
                    $student->getName(),
                    $student->getSurname(),
                    $student->getSchoolNumber(),
                    $student->getClassroom()->getName()
                ];
            }
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($source);

        $xlsx = new Xlsx($spreadsheet);

        $name = tempnam(sys_get_temp_dir(), 'excel');
        $xlsx->save(tempnam(sys_get_temp_dir(), 'excel'));

        return $name;
    }
}
