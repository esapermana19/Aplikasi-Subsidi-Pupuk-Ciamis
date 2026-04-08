<?php

use App\Http\Controllers\AuthController;
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

// --- AUTH ROUTES (Hanya bisa diakses jika SUDAH login & AKTIF) ---
// Route::middleware('auth')->group(function () {

//     // Dashboard Utama
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');

//     // Proses Logout
//     Route::post('/logout', function () {
//         auth()->logout();
//         request()->session()->invalidate();
//         request()->session()->regenerateToken();
//         return redirect('/');
//     })->name('logout');
// });
