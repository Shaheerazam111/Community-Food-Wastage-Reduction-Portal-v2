<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\FoodListingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Community Food Wastage Reduction Portal API v2']);
});

// TASK 1: signup & login (session based)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

// Named "login" route: JSON 401 fallback when the "auth" middleware
// redirects an unauthenticated request instead of a normal HTML redirect.
Route::get('/login', [AuthController::class, 'unauthenticated'])->name('login');

// TASK 3 + Challenge 1 + Challenge 3: public nearby search
Route::get('/food/search', [FoodListingController::class, 'search']);

Route::middleware('auth')->group(function () {
    // TASK 2: donor food listing lifecycle
    Route::post('/food', [FoodListingController::class, 'store']);
    Route::get('/food/mine', [FoodListingController::class, 'myListings']);
    Route::post('/food/{id}/deliver', [FoodListingController::class, 'markDelivered']);

    // TASK 4 + Challenge 2: safe claiming
    Route::post('/food/{id}/claim', [ClaimController::class, 'claim']);
});
