<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\DailyLimitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TradingSummaryController;
use App\Http\Controllers\SignalAdminController;

// Redirect admin to dashboard
Route::redirect('/admin', '/');

// Redirect root for guests to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Trade Log
    Route::resource('trades', TradeController::class);

    // Trading Summary/Recap
    Route::get('/trading-summary', [TradingSummaryController::class, 'index'])->name('trading-summary');
    Route::get('/trading-summary/exchange-rate', [TradingSummaryController::class, 'getExchangeRate'])->name('trading-summary.exchange-rate');

    // Daily Loss Limit
    Route::resource('daily-limits', DailyLimitController::class);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');
    Route::post('/settings/trading', [SettingsController::class, 'updateTrading'])->name('settings.update-trading');
    Route::post('/settings/fetch-rate', [SettingsController::class, 'fetchExchangeRate'])->name('settings.fetch-rate');

    // Admin - User Management (with role check)
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::resource('users', UserController::class)->except('create', 'store', 'show');
        Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');

        // Admin - Role Management
        Route::resource('roles', RoleController::class);
    });

    // Admin Signals
    Route::get('/admin/signals', [SignalAdminController::class, 'index'])->name('admin.signals');
});
