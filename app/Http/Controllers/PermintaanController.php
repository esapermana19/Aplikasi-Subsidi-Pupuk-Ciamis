<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Menyimpan data permintaan dari modal form ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form modal
        $request->validate([
            'tgl_permintaan' => 'required|date',
            'pupuk' => 'required|array',
            'catatan' => 'nullable|string'
        ]);

        // 2. Pastikan mitra mengisi minimal 1 pupuk (totalnya tidak 0)
        $total_diminta = array_sum($request->pupuk);
        if ($total_diminta <= 0) {
            return redirect()->back()->with('error', 'Gagal: Silakan isi jumlah pada minimal satu jenis pupuk.');
        }

        // Ambil data user/mitra yang sedang login
        $user = Auth::user();
        $id_mitra = $user->mitra->id_mitra;

        // 3. Mulai proses simpan ke 2 tabel menggunakan Transaction
        DB::beginTransaction();
        try {
            // A. Simpan ke tabel_permintaan dan ambil ID barunya
            $id_permintaan = DB::table('tabel_permintaan')->insertGetId([
                'id_mitra' => $id_mitra,
                'tgl_permintaan' => $request->tgl_permintaan,
                'status_permintaan' => 'pending', // Default saat baru dibuat
                'catatan' => $request->catatan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // B. Siapkan data untuk tabel_detail_permintaan
            $detail_data = [];
            foreach ($request->pupuk as $id_pupuk => $jml_diminta) {
                // Hanya simpan pupuk yang jumlahnya lebih dari 0
                if ($jml_diminta > 0) {
                    $detail_data[] = [
                        'id_permintaan' => $id_permintaan,
                        'id_pupuk' => $id_pupuk,
                        'jml_diminta' => $jml_diminta,
                        'jml_disetujui' => 0, // Admin yang akan mengubah ini nanti
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // C. Simpan semua array detail sekaligus ke database
            DB::table('tabel_detail_permintaan')->insert($detail_data);

            // Jika semua aman, Commit (sahkan) perubahan ke database
            DB::commit();

            return redirect()->back()->with('success', 'Permintaan pupuk berhasil dikirim dan menunggu persetujuan Admin.');
        } catch (\Exception $e) {
            // Jika ada error, Rollback (batalkan) semua perubahan
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman Riwayat Permintaan
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil data mitra berdasarkan user yang sedang login
        $mitra = DB::table('tabel_mitra')->where('id_user', $user->id_user)->first();

        // Ambil riwayat permintaan milik mitra tersebut
        $riwayat = DB::table('tabel_permintaan')
            ->where('id_mitra', $mitra->id_mitra)
            ->orderBy('created_at', 'desc')
            ->get();

        // Coba ganti titik jadi slash untuk memastikan path
        return view('mitra/riwayat_permintaan', [
            'riwayat' => $riwayat,
            'activeMenu' => 'mitra.riwayat_permintaan'
        ]);
    }

    /**
     * API untuk mengambil detail isi pupuk yang diminta (Dipanggil oleh JS)
     */
    public function detail($id)
    {
        $user = Auth::user();
        $mitra = DB::table('tabel_mitra')->where('id_user', $user->id_user)->first();

        // Cek keamanan: Pastikan permintaan ini benar-benar milik mitra yang sedang login
        $permintaan = DB::table('tabel_permintaan')->where('id_permintaan', $id)->first();
        if (!$permintaan || $permintaan->id_mitra != $mitra->id_mitra) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $details = DB::table('tabel_detail_permintaan')
            ->join('tabel_pupuk', 'tabel_detail_permintaan.id_pupuk', '=', 'tabel_pupuk.id_pupuk')
            ->where('id_permintaan', $id)
            ->select('tabel_detail_permintaan.*', 'tabel_pupuk.nama_pupuk')
            ->get();

        return response()->json($details);
    }
}
