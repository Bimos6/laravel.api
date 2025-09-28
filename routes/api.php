<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class)->only(['store']);
    Route::get('posts', [PostController::class, 'index']);
    Route::get('my-posts', [PostController::class, 'myPosts']);
});