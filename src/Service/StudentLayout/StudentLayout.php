<?php

namespace App\Service\StudentLayout;

use App\Service\ObjectStorage;
use Imagine\Image\FontInterface;

class StudentLayout
{
    const FORMAT = 'jpeg';

    protected ObjectStorage $objectStorage;
    protected string $projectDirectory;

    public function __construct(ObjectStorage $objectStorage, string $projectDirectory)
    {
        $this->objectStorage = $objectStorage;
        $this->projectDirectory = $projectDirectory;
    }

    protected function wrapText(FontInterface $font, string $text, int $width): string
    {
        return explode("\n", $font->wrapText($text, $width))[0];
    }
}
