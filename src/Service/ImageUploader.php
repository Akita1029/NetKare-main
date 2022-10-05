<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader extends FileUploader
{
    private $imageResizer;

    public function __construct(SluggerInterface $slugger, ObjectStorage $objectStorage, ImageResizer $imageResizer)
    {
        parent::__construct($slugger, $objectStorage);

        $this->imageResizer = $imageResizer;
    }

    public function uploadImage(File $file, $public = false, $sizes = []): Image
    {
        $asset = parent::uploadFile($file, $public);

        list($width, $height) = getimagesize($file);

        $image = new Image;
        $image->setAsset($asset);
        $image->setWidth($width);
        $image->setHeight($height);

        if (!empty($sizes)) {
            $originalFilename = $this->getUploadFileOriginalFilename($file);
            $safeFilename = $this->getSafeFilename($originalFilename);
            $guessExtension = $file->guessExtension();

            foreach ($sizes as $sizeKey => $size) {
                $filename = $this->getFilename($safeFilename, $guessExtension, null, $sizeKey);
                $content = $this->imageResizer->resize($file->getPathname(), $size['width'], $size['height']);

                $this->objectStorage->putObject($filename, $content, 'image/jpeg', $public);

                $asset = new Asset;
                $asset->setName($filename);
                $asset->setSize(strlen($content));
                $asset->setMimeType('image/jpeg');
                $asset->setPublic($public);

                $childImage = new Image;
                $childImage->setAsset($asset);
                $childImage->setWidth($size['width']);
                $childImage->setHeight($size['height']);

                $image->addChild($childImage);
            }
        }

        return $image;
    }
}
