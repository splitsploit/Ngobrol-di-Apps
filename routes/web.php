<?php

use App\Events\ChatMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;


Route::get('admins-only', function() {
    return "You Are Admin!";
})->middleware('can:visitAdminPages');

// User Routes
Route::get('/', [UserController::class, 'checkHomePage'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('mustBeLoggedIn');
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar']);

// Blog Post Routes
Route::get('/create-post', [PostController::class, 'showCreatePost'])->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'edit'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'update'])->middleware('can:update,post');
Route::get('/search/{query}', [PostController::class, 'search']);

// Profile Routes

// lookup user by username, not an id
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']);
Route::get('/profile/{user:username}/followings', [UserController::class, 'profileFollowings']);

// profile routes, return JSON
Route::get('/profile/{user:username}/raw', [UserController::class, 'profileRaw']);
Route::get('/profile/{user:username}/followers/raw', [UserController::class, 'profileFollowersRaw']);
Route::get('/profile/{user:username}/followings/raw', [UserController::class, 'profileFollowingsRaw']);

// Follow / Following Routes

// lookup user by username, not an id
Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');

// Chat Routes
Route::post('/send-chat-message', function(Request $request) {
    $formFields = $request->validate([
        'textvalue' => 'required',
    ]);

    if (!trim(strip_tags($formFields['textvalue']))) {
        return response()->noContent();
    }

    broadcast(new ChatMessage(
        [
            'username' => auth()->user()->username, 
            'textvalue' => strip_tags($request->textvalue), 
            'avatar' => auth()->user()->avatar,
        ]))->toOthers();
    
    return response()->noContent();

})->middleware('mustBeLoggedIn');