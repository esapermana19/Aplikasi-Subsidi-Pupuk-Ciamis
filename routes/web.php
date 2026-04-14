<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\PupukController;
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
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::middleware(['role:admin,superadmin'])->group(function () {
        // Manajemen Pupuk
        Route::get('/admin/pupuk', [PupukController::class, 'index'])->name('admin.pupuk');
        Route::post('/admin/pupuk/store', [PupukController::class, 'store'])->name('admin.pupuk.store');
        Route::patch('/admin/pupuk/update/{id}', [PupukController::class, 'update'])->name('admin.pupuk.update');
        Route::delete('/admin/pupuk/delete/{id}', [PupukController::class, 'destroy'])->name('admin.pupuk.destroy');
        Route::get('/admin/petani', [AdminController::class, 'list_petani'])->name('admin.list_petani');
        // Manajemen Petani
        Route::patch('/admin/petani/update/{id}', [AdminController::class, 'update_petani'])->name('admin.petani.update');
        Route::patch('/admin/update_status_petani/{id}', [AdminController::class, 'update_status_petani'])->name('admin.petani.update_status');
        Route::get('/admin/mitra', [AdminController::class, 'list_mitra'])->name('admin.list_mitra');
        Route::patch('/admin/mitra/update/{id}', [AdminController::class, 'update_mitra'])->name('admin.mitra.update');
        Route::patch('/admin/update_status_mitra/{id}', [AdminController::class, 'update_status_mitra'])->name('admin.mitra.update_status');
        Route::get('/admin/approval-permintaan', function() { return "Halaman Req"; })->name('approval-permintaan');
        Route::get('/admin/approval-pencairan', function() { return "Halaman Cair"; })->name('approval-pencairan');
        Route::get('/admin/rekonsiliasi', function() { return "Halaman Rekon"; })->name('rekonsiliasi');
        Route::get('/admin/transaksi', function() { return "Halaman Transaksi"; })->name('transaksi');
        Route::get('/admin/laporan', function() { return "Halaman Laporan"; })->name('laporan');
        Route::get('/admin/verifikasi', [AdminController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/admin/approve_akun/{id}', [AdminController::class, 'approve_akun'])->name('admin.approve_akun');
        Route::delete('/admin/reject_akun/{id}', [AdminController::class, 'reject_akun'])->name('admin.reject_akun');
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
