<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Permintaan;
use App\Models\Transaksi;
use App\Models\Pupuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $id_user = $user->id_user;

        // 1. Hitung Sisa Stok Keseluruhan Mitra dari tabel_detail_stok
        $total_stok_mitra = \DB::table('tabel_detail_stok')
            ->where('id_mitra', $user->mitra->id_mitra)
            ->sum('jml_perubahan');

        // Siapkan data statistik untuk dashboard
        $stats = [
            // Mengambil saldo dari tabel profil mitra
            'saldo_aktif' => $user->mitra->saldo_app ?? 0,

            // Asumsi saldo tertunda (jika ada sistem pencairan pending), jika belum ada set 0
            'saldo_tertunda' => 0,

            // Mengambil total stok pupuk yang tersedia (bisa disesuaikan jika mitra punya tabel stok sendiri)
            'stok_pupuk' => $total_stok_mitra,

            // Jumlah transaksi yang dilakukan mitra di bulan ini
            'transaksi_bulan_ini' => Transaksi::where('id_mitra', $user->mitra->id_mitra)
                ->whereMonth('created_at', date('m'))
                ->count(),
        ];

        // Ambil 5 transaksi terakhir milik mitra ini beserta relasi petani dan pupuknya
        $recentTransactions = Transaksi::with(['petani', 'pupuk'])
            ->where('id_mitra', $user->mitra->id_mitra)
            ->latest()
            ->take(5)
            ->get();

        return view('mitra.dashboard', [
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'activeMenu' => 'dashboard'
        ]);
    }

    public function pupuk_tersedia()
    {
        $id_user_login = Auth::user()->id_user; // id_user dari tabel_users
        $mitra = Mitra::where('id_user', $id_user_login)->first();

        if (!$mitra) {
            abort(403, 'Akses ditolak. Data Mitra tidak ditemukan.');
        }

        // Ambil semua jenis pupuk dari tabel_pupuk
        $pupukList = Pupuk::all()->map(function ($pupuk) use ($mitra) {

            // Hitung Sisa Stok dari tabel_detail_stok
            $pupuk->stok_mitra = \DB::table('tabel_detail_stok')
                ->where('id_mitra', $mitra->id_mitra)
                ->where('id_pupuk', $pupuk->id_pupuk)
                ->sum('jml_perubahan');

            return $pupuk;
        });

        return view('mitra.pupuk_tersedia', compact('pupukList'));
    }
}
