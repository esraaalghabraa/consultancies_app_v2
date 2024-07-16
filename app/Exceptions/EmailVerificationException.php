<?php

namespace App\Exceptions;

use Exception;

class EmailVerificationException extends Exception
{
    protected $message;

    public function __construct($message = "Email Verification Error")
    {
        $this->message = $message;
        parent::__construct($message);
    }
}
