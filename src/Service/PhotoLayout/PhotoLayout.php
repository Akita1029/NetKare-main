<?php

namespace App\Service\PhotoLayout;

use App\Service\ObjectStorage;
use Imagine\Image\FontInterface;

class PhotoLayout
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
