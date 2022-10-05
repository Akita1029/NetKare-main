<?php

namespace App\Service;

use App\Entity\Asset;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;

    protected $objectStorage;

    public function __construct(SluggerInterface $slugger, ObjectStorage $objectStorage)
    {
        $this->slugger = $slugger;

        $this->objectStorage = $objectStorage;
    }

    public function uploadFile(File $file, bool $public = false, string $prefix = null, string $suffix = null): Asset
    {
        $originalFilename = $this->getUploadFileOriginalFilename($file);
        $safeFilename = $this->getSafeFilename($originalFilename);

        $filename = $this->getFilename($safeFilename, $file->guessExtension(), $prefix, $suffix);

        $this->objectStorage->putObject($filename, $file->getContent(), $file->getMimeType(), $public);

        $asset = new Asset;
        $asset->setName($filename);
        $asset->setSize(filesize($file));
        $asset->setMimeType($file->getMimeType());
        $asset->setPublic($public);

        return $asset;
    }

    protected function getUploadFileOriginalFilename(File $file): string
    {
        return pathinfo($file->getFilename(), PATHINFO_FILENAME);;
    }

    protected function getSafeFilename(string $filename): string
    {
        return $this->slugger->slug($filename);
    }

    protected function getFilename(string $safeFilename, string $guessExtension = null, string $prefix = null, string $suffix = null): string
    {
        $filename = '';

        if ($prefix) {
            $filename .= $prefix . '-';
        }

        $filename .= $safeFilename . '-' . uniqid();

        if ($suffix) {
            $filename .= '-' . $suffix;
        }

        if ($guessExtension) {
            $filename .= '.' . $guessExtension;
        }

        return $filename;
    }
}
