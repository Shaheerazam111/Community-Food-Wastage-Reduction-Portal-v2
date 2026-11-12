<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminListingModerationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\RecipientVerificationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\FoodListingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Public HTML pages ---
Route::get('/', [HomeController::class, 'index']);
Route::middleware('guest')->group(function () {
    Route::get('/login', [PortalController::class, 'showLogin'])->name('login');
    Route::get('/register', [PortalController::class, 'showRegister'])->name('register');
});

// --- Portal pages (GET only, session required) ---
Route::middleware('auth')->group(function () {
    Route::get('/donor/dashboard', [PortalController::class, 'donorDashboard']);
    Route::get('/donor/donations/create', [PortalController::class, 'donorCreateForm']);
    Route::get('/recipient/food', [PortalController::class, 'recipientFoodForm']);
    Route::get('/recipient/verification', [RecipientVerificationController::class, 'show']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::redirect('/donor/donations', '/food/mine');
Route::redirect('/recipient/claims', '/food/claims');

// --- API (JSON + HTML forms use the same URLs) ---
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/food/search', [FoodListingController::class, 'search']);

Route::middleware('auth')->group(function () {
    Route::post('/food', [FoodListingController::class, 'store']);
    Route::get('/food/mine', [FoodListingController::class, 'myListings']);
    Route::get('/food/claims', [FoodListingController::class, 'myClaims']);
    Route::post('/food/{id}/deliver', [FoodListingController::class, 'markDelivered']);
    Route::post('/food/{id}/cancel', [FoodListingController::class, 'cancel']);
    Route::post('/food/{id}/claim', [ClaimController::class, 'claim']);
    Route::post('/food/{listing}/feedback', [FeedbackController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/recipient/verification', [RecipientVerificationController::class, 'store']);
    Route::get('/volunteer/tasks', [VolunteerController::class, 'index']);
    Route::post('/volunteer/tasks/{id}/complete', [VolunteerController::class, 'complete']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('admin.reports.export');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::get('/verification', [AdminVerificationController::class, 'index'])->name('admin.verification');
    Route::get('/verification/{id}/download', [AdminVerificationController::class, 'download'])->name('admin.verification.download');
    Route::post('/verification/{id}', [AdminVerificationController::class, 'update'])->name('admin.verification.update');
    Route::get('/listings', [AdminListingModerationController::class, 'index'])->name('admin.listings');
    Route::post('/listings/{id}', [AdminListingModerationController::class, 'update'])->name('admin.listings.update');
});

// --- API tester (browser JSON client) ---
Route::get('/test-apis', [ApiTestController::class, 'index']);
Route::get('/test-apis/snapshot', [ApiTestController::class, 'snapshot']);
Route::get('/test-apis/reverse-geocode', [ApiTestController::class, 'reverseGeocode']);
