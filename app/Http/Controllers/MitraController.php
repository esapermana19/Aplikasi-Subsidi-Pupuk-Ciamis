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

        // 1. Hitung Total Keseluruhan Pupuk Masuk (Permintaan Disetujui)
        $total_masuk = \DB::table('tabel_detail_permintaan')
            ->join('tabel_permintaan', 'tabel_detail_permintaan.id_permintaan', '=', 'tabel_permintaan.id_permintaan')
            ->where('tabel_permintaan.id_mitra', $user->mitra->id_mitra)
            ->where('tabel_permintaan.status_permintaan', 'disetujui')
            ->sum('tabel_detail_permintaan.jml_disetujui');

        // 2. Hitung Total Keseluruhan Pupuk Keluar (Penjualan ke Petani)
        $total_keluar = \DB::table('tabel_detail_transaksi')
            ->join('tabel_transaksi', 'tabel_detail_transaksi.id_transaksi', '=', 'tabel_transaksi.id_transaksi')
            ->where('tabel_transaksi.id_mitra', $user->mitra->id_mitra)
            ->where('tabel_transaksi.status_pengambilan', 'sudah')
            ->sum('tabel_detail_transaksi.jml_beli');

        // 3. Hitung Sisa Stok Keseluruhan Mitra
        $total_stok_mitra = $total_masuk - $total_keluar;

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

            // 1. Hitung TOTAL MASUK (dari detail permintaan yang disetujui)
            // Join antara tabel_permintaan dan tabel_detail_permintaan
            $total_masuk = \DB::table('tabel_detail_permintaan')
                ->join('tabel_permintaan', 'tabel_detail_permintaan.id_permintaan', '=', 'tabel_permintaan.id_permintaan')
                ->where('tabel_permintaan.id_mitra', $mitra->id_mitra)
                ->where('tabel_detail_permintaan.id_pupuk', $pupuk->id_pupuk)
                ->where('tabel_permintaan.status_permintaan', 'disetujui')
                ->sum('tabel_detail_permintaan.jml_disetujui');

            // 2. Hitung TOTAL KELUAR (dari detail transaksi penjualan ke petani)
            // Join antara tabel_transaksi dan tabel_detail_transaksi
            $total_keluar = \DB::table('tabel_detail_transaksi')
                ->join('tabel_transaksi', 'tabel_detail_transaksi.id_transaksi', '=', 'tabel_transaksi.id_transaksi')
                ->where('tabel_transaksi.id_mitra', $mitra->id_mitra)
                ->where('tabel_detail_transaksi.id_pupuk', $pupuk->id_pupuk)
                // Asumsi: status_pengambilan 'sudah' berarti barang sudah keluar dari gudang mitra
                ->where('tabel_transaksi.status_pengambilan', 'sudah')
                ->sum('tabel_detail_transaksi.jml_beli');

            // 3. Hitung Sisa Stok
            $pupuk->stok_mitra = $total_masuk - $total_keluar;

            return $pupuk;
        });

        return view('mitra.pupuk_tersedia', compact('pupukList'));
    }
}
