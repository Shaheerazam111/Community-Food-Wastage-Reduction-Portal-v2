<?php

use App\Http\Controllers\AdminFoodCategoryController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FoodListingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Public HTML pages (logged-in users go to their dashboard) ---
Route::middleware('guest')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/login', [PortalController::class, 'showLogin'])->name('login');
    Route::get('/register', [PortalController::class, 'showRegister'])->name('register');
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);

    Route::middleware('role:donor,recipient')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::post('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/document', [ProfileController::class, 'uploadDocument']);
    });

    Route::middleware('role:donor')->group(function () {
        Route::get('/donor/dashboard', [PortalController::class, 'donorDashboard']);
        Route::get('/donor/donations/create', [PortalController::class, 'donorCreateForm']);
        Route::get('/donor/donations/{id}/edit', [PortalController::class, 'donorEditForm']);
        Route::redirect('/donor/donations', '/food/mine');

        Route::middleware('verified.account')->group(function () {
            Route::post('/food', [FoodListingController::class, 'store']);
            Route::post('/food/{id}', [FoodListingController::class, 'update']);
            Route::post('/food/{id}/cancel', [FoodListingController::class, 'cancel']);
        });

        Route::get('/food/mine', [FoodListingController::class, 'myListings']);
    });

    Route::middleware('role:recipient')->group(function () {
        Route::get('/recipient/dashboard', [PortalController::class, 'recipientDashboard']);
        Route::get('/recipient/food', [PortalController::class, 'recipientFoodForm']);
        Route::redirect('/recipient/claims', '/food/claims');

        Route::get('/food/search', [FoodListingController::class, 'search']);
        Route::get('/recipient/food/{id}', [FoodListingController::class, 'showForRecipient']);
        Route::get('/food/claims', [FoodListingController::class, 'myClaims']);

        Route::middleware('verified.account')->group(function () {
            Route::post('/food/{id}/claim', [ClaimController::class, 'claim']);
            Route::post('/food/{id}/feedback', [FeedbackController::class, 'store']);
        });
    });

    Route::middleware('role:donor,recipient')->group(function () {
        Route::post('/food/{id}/deliver', [FoodListingController::class, 'markDelivered']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [PortalController::class, 'adminDashboard']);
        Route::get('/admin/reports', [AdminReportController::class, 'reports']);
        Route::get('/admin/listings', [AdminReportController::class, 'listings']);
        Route::get('/admin/categories', [AdminFoodCategoryController::class, 'index']);
        Route::post('/admin/categories', [AdminFoodCategoryController::class, 'store']);
        Route::post('/admin/categories/{category}', [AdminFoodCategoryController::class, 'update']);
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users/{user}', [AdminUserController::class, 'update']);
        Route::get('/admin/users/{user}/document', [AdminUserController::class, 'document']);
    });
});

// --- API tester (dev only; not linked from home) ---
Route::get('/test-apis', [ApiTestController::class, 'index']);
Route::get('/test-apis/snapshot', [ApiTestController::class, 'snapshot']);
Route::get('/test-apis/reverse-geocode', [ApiTestController::class, 'reverseGeocode']);
