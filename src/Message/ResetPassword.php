<?php

namespace App\Message;

class ResetPassword
{
    private int $schoolId;

    public function __construct(int $schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function getSchoolId(): int
    {
        return $this->schoolId;
    }
}
