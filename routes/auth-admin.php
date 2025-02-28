<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('send_code', 'reSendCode');
    Route::post('verified_email', 'verifiedEmail');
    Route::post('reset_password', 'resetPassword');
    Route::get('refresh_token', 'refreshToken');
    Route::get('logout', 'logout');
});
