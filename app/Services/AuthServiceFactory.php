<?php

namespace App\Services;


class AuthServiceFactory
{
    public static function create(string $guard, string $model): AuthService
    {
        return new AuthService($guard, $model);
    }
}
