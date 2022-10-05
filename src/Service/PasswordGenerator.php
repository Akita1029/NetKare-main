<?php

namespace App\Service;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class PasswordGenerator
{
    private ComputerPasswordGenerator $passwordGenerator;

    public function __construct()
    {
        $this->passwordGenerator = new ComputerPasswordGenerator;
    }

    public function generatePassword(): string
    {
        return $this->passwordGenerator->generatePassword();
    }
}
