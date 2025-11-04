<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // People routes
    Route::get('/people/recommended', [PeopleController::class, 'recommended']);
    Route::post('/people/{id}/like', [PeopleController::class, 'like']);
    Route::post('/people/{id}/dislike', [PeopleController::class, 'dislike']);
    Route::get('/people/liked', [PeopleController::class, 'liked']);
});
