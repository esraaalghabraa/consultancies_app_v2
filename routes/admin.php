<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('category')->controller(CategoryController::class)->group(function (){
    Route::post('create','create');
    Route::post('update','update');
    Route::post('change_active','changeActive');
    Route::delete('delete','delete');
    Route::get('index','index');
});

Route::prefix('sub_category')->controller(SubCategoryController::class)->group(function (){
    Route::post('create','create');
    Route::post('update','update');
    Route::post('change_active','changeActive');
    Route::delete('delete','delete');
    Route::get('index','index');
});
require __DIR__.'/auth-admin.php';
