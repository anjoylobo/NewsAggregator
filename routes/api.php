<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password-reset', [AuthController::class, 'resetPassword']);
Route::put('/delete-user', [AuthController::class, 'deleteUser']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
    Route::get('/articles/search', [ArticleController::class, 'search']);

    Route::post('/preferences', [UserPreferenceController::class, 'setPreferences']);
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences']);
    Route::get('/personalized-feed', [UserPreferenceController::class, 'getPersonalizedFeed']);
});