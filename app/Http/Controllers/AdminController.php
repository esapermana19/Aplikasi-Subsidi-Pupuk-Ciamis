<?php

namespace App\Http\Controllers;

use App\Models\Pupuk;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Petani;
use App\Models\Mitra;
use App\Models\LogActivity; // Asumsi nama model Log yang kita buat tadi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_petani' => Petani::count(),
            'jenis_pupuk' => Pupuk::count(),
            'mitra_aktif' => User::where('role', 'Mitra')->where('status_akun', 'aktif')->count(),
            'pending_verifikasi' => User::where('status_akun', 'pending')->count(),
            'transaksi_hari_ini' => Transaksi::whereDate('tgl_transaksi', today())->count(),
            // total_harga di tabel_transaksi
            'total_subsidi' => Transaksi::whereMonth('tgl_transaksi', now()->month)->sum('total') ?? 0,
        ];

        // Mengambil 5 aktivitas terbaru dari tabel log yang kita buat
        $recentActivities = LogActivity::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'activeMenu' => 'dashboard'
        ]);
    }

    // Fungsi Verifikasi (Tampilan User Pending)
    public function verifikasi(Request $request)
    {
        // PERBAIKAN: Tambahkan relasi kecamatan dan desa untuk petani maupun mitra
        $query = User::with([
            'petani.kecamatan',
            'petani.desa',
            'mitra.kecamatan',
            'mitra.desa'
        ])->where('status_akun', 'pending');
        // 1. Filter Role (Petani / Mitra)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 2. Filter Kecamatan
        if ($request->filled('id_kecamatan')) {
            $id_kecamatan = $request->id_kecamatan;
            $query->where(function ($q) use ($id_kecamatan) {
                $q->whereHas('petani', function ($queryPetani) use ($id_kecamatan) {
                    $queryPetani->where('id_kecamatan', $id_kecamatan);
                })->orWhereHas('mitra', function ($queryMitra) use ($id_kecamatan) {
                    $queryMitra->where('id_kecamatan', $id_kecamatan);
                });
            });
        }

        // 3. Filter Desa
        if ($request->filled('id_desa')) {
            $id_desa = $request->id_desa;
            $query->where(function ($q) use ($id_desa) {
                $q->whereHas('petani', function ($queryPetani) use ($id_desa) {
                    $queryPetani->where('id_desa', $id_desa);
                })->orWhereHas('mitra', function ($queryMitra) use ($id_desa) {
                    $queryMitra->where('id_desa', $id_desa);
                });
            });
        }

        $pendingUsers = $query->latest()->get();
        $kecamatans = \App\Models\Kecamatan::all();

        return view('admin.verifikasi', [
            'users' => $pendingUsers,
            'activeMenu' => 'verifikasi',
            'currentFilter' => $request->role,
            'kecamatans' => $kecamatans
        ]);
    }

    public function approve_akun($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status_akun' => 'aktif',
            'verified_by' => Auth::user()->admin->id_admin, // Mencatat ID Admin dari profil admin
        ]);

        return back()->with('success', 'Akun berhasil diverifikasi!');
    }

    public function reject_akun($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status_akun' => 'ditolak',
            'verified_by' => Auth::user()->admin->id_admin
        ]);

        return back()->with('success', 'Pendaftaran akun telah ditolak.');
    }

    // List Petani dengan pencarian ke tabel_petani
    // AdminController.php

    // app/Http/Controllers/AdminController.php

    public function list_petani(Request $request)
    {
        $query = Petani::with(['user', 'kecamatan', 'desa']);

        // 1. Filter Status Akun
        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status_akun', $request->status);
            });
        }

        // 2. Filter Pencarian (Nama / NIK)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_petani', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 3. Filter Kecamatan
        if ($request->filled('id_kecamatan')) {
            $query->where('id_kecamatan', $request->id_kecamatan);
        }

        // 4. Filter Desa
        if ($request->filled('id_desa')) {
            $query->where('id_desa', $request->id_desa);
        }

        // Gunakan withQueryString() agar saat klik Next Page, filternya tetap terbawa!
        $list_petani = $query->paginate(10)->withQueryString();

        $kecamatans = \App\Models\Kecamatan::all();

        return view('admin.managepetani', compact('list_petani', 'kecamatans'));
    }

    // List Mitra dengan pencarian ke tabel_mitra
    public function list_mitra(Request $request)
    {
        // Query langsung dari model Mitra, persis seperti Petani
        $query = Mitra::with(['user', 'kecamatan', 'desa']);

        // 1. Filter Status Akun (Karena status ada di tabel users, kita pakai whereHas)
        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status_akun', $request->status);
            });
        }

        // 2. Filter Pencarian (Karena field ini ada di tabel mitra, langsung pakai where)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 3. Filter Kecamatan (Langsung ke tabel mitra)
        if ($request->filled('id_kecamatan')) {
            $query->where('id_kecamatan', $request->id_kecamatan);
        }

        // 4. Filter Desa (Langsung ke tabel mitra)
        if ($request->filled('id_desa')) {
            $query->where('id_desa', $request->id_desa);
        }

        // Gunakan withQueryString() agar filter tidak mereset saat pindah halaman
        $mitra = $query->latest('id_mitra')->paginate(10)->withQueryString();

        // Ambil data kecamatan untuk ditampilkan di dropdown filter
        $kecamatans = \App\Models\Kecamatan::all();

        return view('admin.managemitra', [
            'mitra' => $mitra,
            'kecamatans' => $kecamatans,
            'activeMenu' => 'mitra'
        ]);
    }

    // Update Data Petani (Update 2 Tabel)
    public function update_petani(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_petani' => 'required|string|max:50',
            'email' => 'required|email|unique:tabel_users,email,' . $id . ',id_user',
        ]);

        DB::transaction(function () use ($request, $user) {
            // Update User (Email & Password)
            $userData = ['email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            // Update Profil Petani
            $user->petani->update([
                'nama_petani' => $request->nama_petani,
                'nik' => $request->nik,
                'alamat_petani' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'id_kecamatan' => $request->id_kecamatan,
                'id_desa' => $request->id_desa,
            ]);
        });

        return back()->with('success', 'Data Petani berhasil diperbarui!');
    }

    // Update Data Mitra (Update 2 Tabel)
    public function update_mitra(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_mitra' => 'required|string|max:50',
            'email' => 'required|email|unique:tabel_users,email,' . $id . ',id_user',
        ]);

        DB::transaction(function () use ($request, $user) {
            // Update data Akun
            $userData = ['email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            // Update data Mitra
            $user->mitra->update([
                'nama_mitra' => $request->nama_mitra,
                'nama_pemilik' => $request->nama_pemilik,
                'no_rek' => $request->no_rek,
                'alamat_mitra' => $request->alamat, // Atau $request->alamat_mitra, sesuaikan dengan name di form HTML Anda

                // --- TAMBAHKAN DUA BARIS INI ---
                'id_kecamatan' => $request->id_kecamatan,
                'id_desa' => $request->id_desa,
            ]);
        });

        return back()->with('success', 'Data Mitra berhasil diperbarui!');
    }

    // Update Status Akun (Aktif/Nonaktif) untuk Petani dan Mitra
    public function update_status(Request $request, $id)
    {
        // Validasi input status
        $request->validate([
            'status' => 'required|in:aktif,nonaktif,pending'
        ]);

        try {
            $user = User::findOrFail($id);

            // Update status akun di tabel_users
            $user->update([
                'status_akun' => $request->status
            ]);

            return back()->with('success', 'Status akun ' . $user->email . ' berhasil diubah menjadi ' . $request->status);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function approval_permintaan()
    {
        // Ambil semua permintaan beserta nama mitra/tokonya
        $permintaans = DB::table('tabel_permintaan')
            // Asumsi: Anda punya tabel_mitra dan punya relasi ke sana
            ->join('tabel_mitra', 'tabel_permintaan.id_mitra', '=', 'tabel_mitra.id_mitra')
            ->select('tabel_permintaan.*', 'tabel_mitra.nama_mitra')
            ->orderBy('tabel_permintaan.created_at', 'desc')
            ->get();

        return view('admin.approval_permintaan', compact('permintaans'));
    }

    /**
     * API untuk mengambil detail isi pupuk yang diminta
     */
    public function detail_permintaan($id)
    {
        $details = DB::table('tabel_detail_permintaan')
            ->join('tabel_pupuk', 'tabel_detail_permintaan.id_pupuk', '=', 'tabel_pupuk.id_pupuk')
            ->where('id_permintaan', $id)
            ->select('tabel_detail_permintaan.*', 'tabel_pupuk.nama_pupuk')
            ->get();

        return response()->json($details);
    }

    /**
     * Approval Permintaan
     * Proses Update Status & Jumlah yang Disetujui
     */
    public function update_permintaan(Request $request, $id)
    {
        $action = $request->input('action');

        // 1. CARI id_admin yang benar dari tabel_admin
        $admin = DB::table('tabel_admin')
            ->where('id_user', Auth::user()->id_user)
            ->first();

        // Validasi jika user login ternyata tidak terdaftar di tabel_admin
        if (!$admin) {
            return redirect()->back()->with('error', 'Gagal: Akun Anda tidak terdaftar sebagai Admin di sistem.');
        }

        DB::beginTransaction();
        try {
            if ($action == 'setujui') {
                // 2. Update tabel_permintaan menggunakan $admin->id_admin
                DB::table('tabel_permintaan')->where('id_permintaan', $id)->update([
                    'status_permintaan' => 'disetujui',
                    'id_admin' => $admin->id_admin, // <-- Gunakan ini, bukan Auth::id()
                    'updated_at' => now()
                ]);

                $pupuk_disetujui = $request->input('pupuk_disetujui', []);
                foreach ($pupuk_disetujui as $id_detail => $jml) {
                    DB::table('tabel_detail_permintaan')
                        ->where('id_detail_permintaan', $id_detail)
                        ->update(['jml_disetujui' => $jml]);
                }

                $pesan = 'Permintaan berhasil disetujui.';
            } else {
                DB::table('tabel_permintaan')->where('id_permintaan', $id)->update([
                    'status_permintaan' => 'ditolak',
                    'id_admin' => $admin->id_admin, // <-- Gunakan ini juga di sini
                    'updated_at' => now()
                ]);

                $pesan = 'Permintaan telah ditolak.';
            }

            DB::commit();
            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
