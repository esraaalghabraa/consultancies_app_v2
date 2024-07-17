<?php

namespace App\Services;


class OTPServiceFactory
{
    public static function create(): OTPService
    {
        return new OTPService();
    }
}
