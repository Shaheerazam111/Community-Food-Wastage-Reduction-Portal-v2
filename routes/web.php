<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\FoodListingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

// --- Public HTML pages ---
Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [PortalController::class, 'showLogin'])->name('login');
Route::get('/register', [PortalController::class, 'showRegister'])->name('register');

// --- Portal pages (GET only) ---
Route::get('/donor/dashboard', [PortalController::class, 'donorDashboard']);
Route::get('/donor/donations/create', [PortalController::class, 'donorCreateForm']);
Route::get('/recipient/food', [PortalController::class, 'recipientFoodForm']);
Route::redirect('/donor/donations', '/food/mine');
Route::redirect('/recipient/claims', '/food/claims');

// --- API (JSON + HTML forms use the same URLs) ---
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::get('/food/search', [FoodListingController::class, 'search']);

Route::middleware('auth')->group(function () {
    Route::post('/food', [FoodListingController::class, 'store']);
    Route::get('/food/mine', [FoodListingController::class, 'myListings']);
    Route::get('/food/claims', [FoodListingController::class, 'myClaims']);
    Route::post('/food/{id}/deliver', [FoodListingController::class, 'markDelivered']);
    Route::post('/food/{id}/claim', [ClaimController::class, 'claim']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('admin.reports.export');
});

// --- API tester (browser JSON client) ---
Route::get('/test-apis', [ApiTestController::class, 'index']);
Route::get('/test-apis/snapshot', [ApiTestController::class, 'snapshot']);
Route::get('/test-apis/reverse-geocode', [ApiTestController::class, 'reverseGeocode']);
