<?php

namespace App\Exceptions;

use Exception;

class ExistObjectException extends Exception
{
    protected $message;

    public function __construct($message = "Object not found")
    {
        $this->message = $message;
        parent::__construct($message);
    }
}
