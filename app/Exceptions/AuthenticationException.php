<?php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected $message;

    public function __construct($message = "Authentication failed")
    {
        $this->message = $message;
        parent::__construct($message);
    }
}
