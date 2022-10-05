<?php

namespace App\Service;

use Exception;
use Imagine\Image\Box;

class ImageResizer
{
    private $imagine;

    public function __construct(string $library)
    {
        switch ($library) {
            case 'gd':
                $this->imagine = new \Imagine\Gd\Imagine;
                break;

            case 'gmagick':
                $this->imagine = new \Imagine\Gmagick\Imagine;
                break;

            case 'imagick':
                $this->imagine = new \Imagine\Imagick\Imagine;
                break;

            default:
                throw new Exception('Image library not initialized.');
        }
    }

    public function resize(string $path, int $width, int $height): string
    {
        $image = $this->imagine->open($path);

        $image = $image
            ->thumbnail(new Box($width, $height));

        return $image->get('jpg');
    }
}