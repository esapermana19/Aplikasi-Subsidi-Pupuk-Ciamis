<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PetaniController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');
    //Proses Login
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    // Halaman Register
    Route::get('/register', function () {
        return view('auth.register'); // Pastikan Anda punya file resources/views/auth/register.blade.php
    })->name('register');
    // Proses Register
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware(['role:admin,superadmin'])->group(function () {
        Route::get('/admin/verifikasi', [AdminController::class, 'index'])->name('admin.verifikasi');
        Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    });
    // Khusus Petani
    Route::middleware(['role:petani'])->group(function () {
        Route::get('/petani/kuota', [PetaniController::class, 'index'])->name('petani.kuota');
    });

    // Khusus Mitra (Scan QR)
    Route::middleware(['role:mitra'])->group(function () {
        Route::get('/mitra/transaksi', [MitraController::class, 'index'])->name('mitra.transaksi');
    });
});
