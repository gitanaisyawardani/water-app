<?php

use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TruckController as AdminTruckController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    if (in_array($role, ['admin', 'owner'])) {
        return redirect()->route('admin.drivers.index');
    }
    return redirect()->route('customer.orders.create');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin & Owner Routes (Sprint 1: Master Data & Konfigurasi)
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    // PBI-01: User Management & Hak Akses
    Route::resource('users', AdminUserController::class);

    // PBI-02 & PBI-03: Master Pelanggan & Kategori Bisnis
    Route::resource('customers', AdminCustomerController::class);

    // PBI-04: Master Driver & Ketersediaan
    Route::resource('drivers', AdminDriverController::class);

    // PBI-05: Master Armada Truk Tangki
    Route::resource('trucks', AdminTruckController::class);

    // PBI-06: Konfigurasi Harga Dasar Air & Tarif KM
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

// Customer Routes (Sprint 1: Order Mandiri & Kalkulasi Biaya)
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // PBI-07 & PBI-08: Order Air Tangki Mandiri & Kalkulasi Otomatis
    Route::get('orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [CustomerOrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [CustomerOrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
});

require __DIR__.'/auth.php';
