<?php

use App\Http\Controllers\PosAuthController;
use App\Http\Controllers\PosCashierController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::prefix('pos')->name('pos.')->group(function () {

    // ── Auth (guest only) ────────────────────────────────────────────────
    Route::middleware('guest:pos')->group(function () {
        Route::get('/login', [PosAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [PosAuthController::class, 'login'])->name('login.post');
    });

    // ── Pilih Toko (sudah login, belum pilih tenant) ─────────────────────
    // Tidak pakai pos.auth agar tidak redirect loop
    Route::middleware('auth:pos')->group(function () {
        Route::get('/select-tenant', [PosAuthController::class, 'showSelectTenant'])->name('select-tenant');
        Route::post('/select-tenant', [PosAuthController::class, 'selectTenant'])->name('select-tenant.post');
    });

    Route::post('/logout', [PosAuthController::class, 'logout'])->name('logout');

    // ── POS Interface (login + tenant wajib) ─────────────────────────────
    Route::middleware('pos.auth')->group(function () {

        Route::get('/', [PosCashierController::class, 'index'])->name('index');

        // Switch toko tanpa logout (untuk user multi-tenant)
        Route::post('/switch-tenant', [PosAuthController::class, 'switchTenant'])->name('switch-tenant');

        // JSON API
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/products', [PosCashierController::class, 'products'])->name('products');
            Route::post('/sale', [PosCashierController::class, 'createSale'])->name('sale.create');
            Route::post('/sale/{sale}/pay', [PosCashierController::class, 'processSale'])->name('sale.pay');
            Route::get('/sale/{sale}/receipt', [PosCashierController::class, 'receipt'])->name('sale.receipt');
        });
    });
});
