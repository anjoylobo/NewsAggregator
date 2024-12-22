<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

/**
 * Public routes for user authentication and password management.
 */
Route::post('/register', [AuthController::class, 'register']);  // Register a new user.
Route::post('/login', [AuthController::class, 'login']);  // Log in an existing user.
Route::post('/password-reset', [AuthController::class, 'resetPassword']);  // Request password reset.
Route::put('/delete-user', [AuthController::class, 'deleteUser']);  // Delete the user account.

/**
 * Routes that require authentication.
 * All routes inside this group are protected by the 'auth:sanctum' middleware.
 */
Route::middleware('auth:sanctum')->group(function () {

    /**
     * User actions for managing authenticated sessions.
     */
    Route::post('/logout', [AuthController::class, 'logout']);  // Log out the authenticated user.

    /**
     * Routes for handling articles.
     */
    Route::get('/articles', [ArticleController::class, 'index']);  // Get a list of all articles.
    Route::get('/articles/{id}', [ArticleController::class, 'show']);  // Get a specific article by ID.
    Route::get('/articles/search', [ArticleController::class, 'search']);  // Search articles based on query parameters.

    /**
     * Routes for managing user preferences.
     */
    Route::post('/preferences', [UserPreferenceController::class, 'setPreferences']);  // Set user preferences.
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences']);  // Get the current user's preferences.
    Route::get('/personalized-feed', [UserPreferenceController::class, 'getPersonalizedFeed']);  // Get a personalized feed based on user preferences.

});
