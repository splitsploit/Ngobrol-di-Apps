<?php

use App\Http\Controllers\ExampleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExampleController::class, 'homePage']);
Route::get('/single-post', [ExampleController::class, 'singlePost']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);;

