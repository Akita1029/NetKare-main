<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;

interface DownloadMakerInterface
{
    public function make(School $school): string;
}
