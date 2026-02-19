<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TopicController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    Route::apiResource('topics', TopicController::class);

    Route::scopeBindings()->group(function () {
        Route::apiResource('topics.posts', PostController::class);
        Route::apiResource('/topics.posts.comments', CommentController::class);
    });

    Route::post('/posts/{post}/like', [LikeController::class, 'togglePost']);
    Route::post('/comments/{comment}/like', [LikeController::class, 'toggleComment']);
});
Route::get('/topics', [TopicController::class, 'index']);
Route::get('/topics/{topic}', [TopicController::class, 'show']);
Route::get('/topics/{topic}/posts', [PostController::class, 'index']);
Route::get('/topics/{topic}/posts/{post}', [PostController::class, 'show']);
