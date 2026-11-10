<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\AdminController;

Route::get('/', [PageController::class, 'landing'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AccountController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])->name('register.post');
    Route::get('/login', [AccountController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login'])->name('login.post');
});

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{id}/download', [ReceiptController::class, 'download'])->name('receipts.download');
    Route::get('/receipts/{id}/edit', [ReceiptController::class, 'edit'])->name('receipts.edit');
    Route::put('/receipts/{id}', [ReceiptController::class, 'update'])->name('receipts.update');
    Route::delete('/receipts/{id}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');

    Route::post('/receipts/{receipt}/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/warranties', [WarrantyController::class, 'index'])->name('warranties.index');

    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile/notifications', [AccountController::class, 'updateNotifications'])->name('profile.notifications');
    Route::post('/receipts/recognize', [App\Http\Controllers\ReceiptController::class, 'recognize'])->name('receipts.recognize');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::patch('/users/{user}/role', [AdminController::class, 'changeRole'])->name('admin.users.role');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

