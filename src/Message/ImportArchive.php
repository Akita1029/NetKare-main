<?php

namespace App\Message;

class ImportArchive
{
    private int $dealerId;
    private int $schoolId;
    private string $archivePathName;
    private mixed $formData;

    public function __construct(int $dealerId, int $schoolId, string $archivePathName, mixed $formData)
    {
        $this->dealerId = $dealerId;
        $this->schoolId = $schoolId;
        $this->archivePathName = $archivePathName;
        $this->formData = $formData;
    }

    public function getDealerId(): int
    {
        return $this->dealerId;
    }

    public function getSchoolId(): int
    {
        return $this->schoolId;
    }

    public function getArchivePathname(): string
    {
        return $this->archivePathName;
    }

    public function getFormData(): mixed
    {
        return $this->formData;
    }
}
