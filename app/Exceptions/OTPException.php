<?php

namespace App\Exceptions;

use Exception;

class OTPException extends Exception
{
    protected $message;

    public function __construct($message = "OTP Error")
    {
        $this->message = $message;
        parent::__construct($message);
    }
}
