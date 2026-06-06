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
use Illuminate\Support\Facades\DB;
use App\Models\Musim;

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

        // Hitung Statistik Kuota Berdasarkan Musim Aktif
        $musimAktif = Musim::where('is_active', true)->first();
        
        $kuota_keseluruhan = 0;
        $sudah_dibeli = 0;
        $sisa_kuota = 0;
        $periode = 'Belum Ada Musim Aktif';
        $persentase_sisa = 0;
        $persentase_terpakai = 0;

        if ($musimAktif) {
            $periode = $musimAktif->nama_musim;
            $kuota_keseluruhan = $musimAktif->limit_per_petani;
            
            $sudah_dibeli = DB::table('tabel_detail_transaksi')
                ->join('tabel_transaksi', 'tabel_detail_transaksi.id_transaksi', '=', 'tabel_transaksi.id_transaksi')
                ->where('tabel_transaksi.id_petani', $petani->id_petani)
                ->where('tabel_transaksi.id_musim', $musimAktif->id_musim)
                ->where('tabel_transaksi.status_pembayaran', 'success')
                ->sum('tabel_detail_transaksi.jml_beli');
            
            $sisa_kuota = max(0, $kuota_keseluruhan - $sudah_dibeli);
            
            if ($kuota_keseluruhan > 0) {
                $persentase_sisa = ($sisa_kuota / $kuota_keseluruhan) * 100;
                $persentase_terpakai = ($sudah_dibeli / $kuota_keseluruhan) * 100;
            }
        }

        // Transaksi Terakhir (untuk card ke-3)
        $transaksiTerakhir = Transaksi::where('id_petani', $petani->id_petani)
            ->whereIn('status_pembayaran', ['success'])
            ->orderBy('created_at', 'desc')
            ->first();

        return view('petani.dashboard', [
            'petani' => $petani,
            'transaksiTerbaru' => $transaksiTerbaru,
            'totalTransaksi' => $totalTransaksi,
            'totalPembelian' => $totalPembelian,
            'totalNilai' => $totalNilai,
            'kecamatans' => $kecamatans,
            'kuota_keseluruhan' => $kuota_keseluruhan,
            'sudah_dibeli' => $sudah_dibeli,
            'sisa_kuota' => $sisa_kuota,
            'periode' => $periode,
            'persentase_sisa' => $persentase_sisa,
            'persentase_terpakai' => $persentase_terpakai,
            'transaksiTerakhir' => $transaksiTerakhir,
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
