<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// User Routes
Route::get('/', [UserController::class, 'checkHomePage']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);

// Blog Post Routes
Route::get('create-post', [PostController::class, 'showCreatePost']);

