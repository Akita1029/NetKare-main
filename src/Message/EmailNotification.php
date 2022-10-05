<?php

namespace App\Message;

use Symfony\Component\Mime\Email;

class EmailNotification
{
    private Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
