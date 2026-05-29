<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Petani;
use App\Models\Transaksi;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetaniController extends Controller
{
    // Dashboard Petani
    public function index()
    {
        $user = Auth::user();
        $petani = $user->petani;

        // Ambil data transaksi terbaru (max 5)
        $transaksiTerbaru = Transaksi::where('id_petani', $petani->id_petani)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung statistik
        $totalTransaksi = Transaksi::where('id_petani', $petani->id_petani)->count();
        $totalPembelian = Transaksi::where('id_petani', $petani->id_petani)
            ->whereIn('status_pembayaran', ['success'])
            ->count();
        $totalNilai = Transaksi::where('id_petani', $petani->id_petani)
            ->whereIn('status_pembayaran', ['success'])
            ->sum('total');

        // Ambil daftar kecamatan untuk filter
        $kecamatans = Kecamatan::all();

        return view('petani.dashboard', [
            'petani' => $petani,
            'transaksiTerbaru' => $transaksiTerbaru,
            'totalTransaksi' => $totalTransaksi,
            'totalPembelian' => $totalPembelian,
            'totalNilai' => $totalNilai,
            'kecamatans' => $kecamatans,
            'activeMenu' => 'petani.dashboard'
        ]);
    }

    // Beli Pupuk
    public function beliPupuk()
    {
        $kecamatans = Kecamatan::all();
        $desas = Desa::all();
        $mitras = Mitra::with(['kecamatan', 'desa'])->get();

        return view('petani.beli_pupuk', [
            'kecamatans' => $kecamatans,
            'desas' => $desas,
            'mitras' => $mitras,
            'activeMenu' => 'petani.beli_pupuk'
        ]);
    }

    // Riwayat Transaksi
    public function riwayatTransaksi()
    {
        $user = Auth::user();
        $petani = $user->petani;

        $transaksis = Transaksi::where('id_petani', $petani->id_petani)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('petani.riwayat_transaksi', [
            'transaksis' => $transaksis,
            'activeMenu' => 'petani.riwayat_transaksi'
        ]);
    }

    // Detail Transaksi
    public function detailTransaksi($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $user = Auth::user();
        $petani = $user->petani;

        // Pastikan transaksi milik petani yang login
        if ($transaksi->id_petani !== $petani->id_petani) {
            abort(403, 'Unauthorized access');
        }

        return view('petani.detail_transaksi', compact('transaksi'));
    }
}
