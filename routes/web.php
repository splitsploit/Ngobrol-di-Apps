<?php

use App\Http\Controllers\ExampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExampleController::class, 'homePage']);
Route::get('/single-post', [ExampleController::class, 'singlePost']);
Route::post('/register', [ExampleController::class, 'register']);


