<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function getPupukByMitra($id_mitra)
    {
        // Ambil semua jenis pupuk
        $pupuks = \App\Models\Pupuk::all();

        $dataPupuk = $pupuks->map(function ($pupuk) use ($id_mitra) {
            // 1. Hitung TOTAL MASUK (dari detail permintaan yang disetujui)
            $total_masuk = DB::table('tabel_detail_permintaan')
                ->join('tabel_permintaan', 'tabel_detail_permintaan.id_permintaan', '=', 'tabel_permintaan.id_permintaan')
                ->where('tabel_permintaan.id_mitra', $id_mitra)
                ->where('tabel_detail_permintaan.id_pupuk', $pupuk->id_pupuk)
                ->where('tabel_permintaan.status_permintaan', 'diterima')
                ->sum('tabel_detail_permintaan.jml_disetujui');

            // 2. Hitung TOTAL KELUAR (dari detail transaksi penjualan ke petani)
            $total_keluar = DB::table('tabel_detail_transaksi')
                ->join('tabel_transaksi', 'tabel_detail_transaksi.id_transaksi', '=', 'tabel_transaksi.id_transaksi')
                ->where('tabel_transaksi.id_mitra', $id_mitra)
                ->where('tabel_detail_transaksi.id_pupuk', $pupuk->id_pupuk)
                ->where('tabel_transaksi.status_pengambilan', 'sudah')
                ->sum('tabel_detail_transaksi.jml_beli');

            // 3. Hitung Sisa Stok Mitra
            $stok_mitra = $total_masuk - $total_keluar;

            // Dummy jatah petani (Silakan sesuaikan dengan logika kuota jatah petani nantinya)
            $sisa_jatah_petani = ($pupuk->nama_pupuk == 'Urea') ? 150 : 75;

            return [
                'id_pupuk'          => $pupuk->id_pupuk,
                'nama_pupuk'        => $pupuk->nama_pupuk,
                'harga_subsidi'     => $pupuk->harga_subsidi,
                'stok_mitra'        => (int)max(0, $stok_mitra),
                'sisa_jatah_petani' => $sisa_jatah_petani
            ];
        });

        // Filter: Hanya tampilkan pupuk yang stoknya > 0 di mitra tersebut
        $filteredData = $dataPupuk->filter(function($item) {
            return $item['stok_mitra'] > 0;
        })->values();

        return response()->json($filteredData);
    }
}
