<?php

namespace App\Message;

class DownloadPreparation
{
    private int $downloadId;

    public function __construct(int $downloadId)
    {
        $this->downloadId = $downloadId;
    }

    public function getDownloadId(): int
    {
        return $this->downloadId;
    }
}
