<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\PupukController;
use App\Http\Controllers\TransaksiController;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Route;

// API untuk mendapatkan desa berdasarkan kecamatan
Route::get('/get-desa/{id_kecamatan}', function ($id_kecamatan) {
    $desa = \App\Models\Desa::where('id_kecamatan', $id_kecamatan)->get();
    return response()->json($desa);
});

// Midtrans Webhook (harus di luar middleware auth)
Route::post('/api/midtrans-callback', [TransaksiController::class, 'notificationHandler']);

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    //Proses Login
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    // Halaman Register
    // Pastikan route GET register mengarah ke method 'create'
    Route::get('/register', [AuthController::class, 'create'])->name('register');
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
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        // Gunakan URL ini agar konsisten untuk Petani dan Mitra
        Route::patch('/admin/status/update/{id}', [AdminController::class, 'update_status'])->name('admin.update_status');
        // Manajemen Pupuk
        Route::get('/admin/pupuk', [PupukController::class, 'index'])->name('admin.pupuk.index');
        Route::post('/admin/pupuk/store', [PupukController::class, 'store'])->name('admin.pupuk.store');
        Route::patch('/admin/pupuk/update/{id}', [PupukController::class, 'update'])->name('admin.pupuk.update');
        Route::delete('/admin/pupuk/delete/{id}', [PupukController::class, 'destroy'])->name('admin.pupuk.destroy');
        Route::get('/admin/petani', [AdminController::class, 'list_petani'])->name('admin.list_petani');
        Route::post('/admin/pupuk/{id}/tambah-stok', [PupukController::class, 'tambahStok'])->name('admin.pupuk.tambah_stok');
        // Manajemen Petani
        Route::patch('/admin/petani/update/{id}', [AdminController::class, 'update_petani'])->name('admin.petani.update');
        Route::patch('/admin/update_status_petani/{id}', [AdminController::class, 'update_status_petani'])->name('admin.petani.update_status');
        // Manajemen Mitra
        Route::get('/admin/mitra', [AdminController::class, 'list_mitra'])->name('admin.list_mitra');
        Route::patch('/admin/mitra/update/{id}', [AdminController::class, 'update_mitra'])->name('admin.mitra.update');
        Route::patch('/admin/user/update-status/{id}', [AdminController::class, 'update_status'])->name('admin.update_status');
        Route::patch('/admin/update_status_mitra/{id}', [AdminController::class, 'update_status_mitra'])->name('admin.mitra.update_status');
        // Verifikasi Akun
        Route::get('/admin/verifikasi', [AdminController::class, 'verifikasi'])->name('admin.verifikasi');
        Route::post('/admin/approve_akun/{id}', [AdminController::class, 'approve_akun'])->name('admin.approve_akun');
        Route::delete('/admin/reject_akun/{id}', [AdminController::class, 'reject_akun'])->name('admin.reject_akun');
        // Halaman List Approval Permintaan
        Route::get('/admin/approval-permintaan', [AdminController::class, 'approval_permintaan'])->name('admin.approval_permintaan');
        // API untuk mengambil detail permintaan (dipanggil lewat JS Modal)
        Route::get('/admin/permintaan/{id}/detail', [AdminController::class, 'detail_permintaan']);
        // Proses Simpan Approval (Setujui / Tolak)
        Route::post('/admin/permintaan/{id}/update', [AdminController::class, 'update_permintaan'])->name('admin.permintaan.update');

        Route::get('/admin/approval-pencairan', function () {
            return "Halaman Cair";
        })->name('approval-pencairan');
        Route::get('/admin/rekonsiliasi', function () {
            return "Halaman Rekon";
        })->name('rekonsiliasi');
        Route::get('/admin/transaksi', function () {
            return "Halaman Transaksi";
        })->name('transaksi');
        Route::get('/admin/laporan', function () {
            return "Halaman Laporan";
        })->name('laporan');
    });
    // Khusus Petani
    Route::middleware(['role:petani'])->group(function () {
        Route::get('/petani/dashboard', [PetaniController::class, 'index'])->name('petani.dashboard');
        Route::get('/petani/beli-pupuk', [PetaniController::class, 'beliPupuk'])->name('petani.beli_pupuk');
        Route::get('/petani/riwayat-transaksi', [TransaksiController::class, 'riwayat'])->name('petani.riwayat_transaksi');
        Route::get('/petani/transaksi/{id}', [PetaniController::class, 'detailTransaksi'])->name('petani.detail_transaksi');
        // routes/web.php
        Route::get('/api/mitra/{id_mitra}/pupuk', [TransaksiController::class, 'getPupukByMitra']);
        Route::post('/api/checkout', [TransaksiController::class, 'prosesCheckout']);
        Route::get('/api/transaksi/detail/{id}', [TransaksiController::class, 'detail']);
    });

    // Khusus Mitra
    Route::middleware(['role:mitra'])->group(function () {
        Route::get('/mitra/dashboard', [MitraController::class, 'index'])->name('mitra.dashboard');
        Route::get('/mitra/permintaan', [MitraController::class, 'permintaan'])->name('mitra.permintaan');

        Route::get('/mitra/riwayat_permintaan', [PermintaanController::class, 'index'])->name('mitra.riwayat_permintaan');
        Route::get('/mitra/permintaan/{id}/detail', [PermintaanController::class, 'detail']);
        Route::post('/mitra/simpan-permintaan', [PermintaanController::class, 'store'])->name('mitra.store_permintaan');
        Route::post('/mitra/riwayat_permintaan/{id}/terima', [PermintaanController::class, 'terimaPermintaan'])->name('mitra.permintaan.terima');

        Route::get('/mitra/pupuk_tersedia', [MitraController::class, 'pupuk_tersedia'])->name('mitra.pupuk_tersedia');
        Route::get('/mitra/pencairan', [MitraController::class, 'pencairan'])->name('mitra.pencairan');
        Route::get('/mitra/scan', [MitraController::class, 'scan'])->name('mitra.scan');
        Route::get('/mitra/transaksi', [MitraController::class, 'transaksi'])->name('mitra.transaksi');
        Route::get('/mitra/tarik_saldo', [MitraController::class, 'tarik_saldo'])->name('mitra.tarik_saldo');
        Route::get('/mitra/laporan', [MitraController::class, 'laporan'])->name('mitra.laporan');
    });
});
