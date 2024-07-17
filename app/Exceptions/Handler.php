<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];



    public function render($request, Throwable $e)
    {
        if ($e instanceof MissingAbilityException){
            return $this->failedResponse('Unauthorized',403);
        }
        if ($e instanceof AuthenticationException){
            return $this->failedResponse('unAuthenticated',401);
        }elseif ($e instanceof ValidationException){
            return $this->convertExceptionToResponse($e);
        }
        return $this->prepareResponse($request,$e);
    }
}
