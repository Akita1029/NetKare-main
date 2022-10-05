<?php

namespace App\Service\DownloadMaker;

use ZipArchive;

class ZipMaker
{
    private string $zipFilename;

    private ZipArchive $zip;

    public function __construct()
    {
        // $tempFilename = tempnam(sys_get_temp_dir(), 'download');
        // $lastFilename = $tempFilename . '.zip';

        // rename($tempFilename, $lastFilename);

        // $this->zipFilename = $lastFilename;

        $this->zipFilename = tempnam(sys_get_temp_dir(), 'download');

        $this->zip = new ZipArchive;
        $this->zip->open($this->zipFilename, ZipArchive::OVERWRITE);
    }

    public function getFilename(): string
    {
        return $this->zipFilename;
    }

    public function addFromString(string $name, string $content): bool
    {
        return $this->zip->addFromString($name, $content);
    }

    public function addFile(string $filepath, string $entryname = null): bool
    {
        return $this->zip->addFile($filepath, $entryname);
    }

    public function close(): bool
    {
        return $this->zip->close();
    }
}
