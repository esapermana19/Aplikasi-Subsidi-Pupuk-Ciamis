<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Pupuk;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Petani;
use App\Models\Mitra;
use App\Models\Penarikan;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use \App\Traits\ExcelExportTrait;

    public function index()
    {
        $total_petani = Petani::count();
        $total_mitra = Mitra::count();
        $transaksi_berhasil = Transaksi::where('status_pembayaran', 'success')->count();
        
        $pupukTersalurKg = \App\Models\DetailTransaksi::whereHas('transaksi', function($q) {
            $q->where('status_pembayaran', 'success');
        })->sum('jml_beli');
        $pupuk_tersalur = $pupukTersalurKg / 1000;

        // Trend Penyaluran (7 Hari Terakhir)
        $trendDates = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendDates[] = $date->translatedFormat('D'); // Rab, Kam, Jum, Sab, Min, Sen, Sel
            
            $kg = \App\Models\DetailTransaksi::whereHas('transaksi', function($q) use ($date) {
                $q->where('status_pembayaran', 'success')
                  ->whereDate('tgl_transaksi', $date);
            })->sum('jml_beli');
            
            $trendData[] = round($kg / 1000, 1);
        }

        // Aktivitas Terbaru (5 Transaksi Terakhir)
        $recentTransactions = Transaksi::with(['petani', 'mitra'])
            ->latest('tgl_transaksi')
            ->take(5)
            ->get();

        // Distribusi Pupuk
        $distribusi = \App\Models\DetailTransaksi::selectRaw('id_pupuk, SUM(jml_beli) as total')
            ->whereHas('transaksi', function($q) {
                $q->where('status_pembayaran', 'success');
            })
            ->groupBy('id_pupuk')
            ->with('pupuk')
            ->get();
            
        $pieLabels = [];
        $pieSeries = [];
        
        if($distribusi->isEmpty()) {
            $pieLabels = ['Urea', 'NPK'];
            $pieSeries = [0, 0];
        } else {
            foreach ($distribusi as $dist) {
                $pieLabels[] = $dist->pupuk ? $dist->pupuk->nama_pupuk : 'Unknown';
                $pieSeries[] = (float)$dist->total;
            }
        }

        // Akun Pending Terkini (Menggantikan Peringatan Stok Mitra & Transaksi Pending)
        $akunPending = User::with(['petani', 'mitra'])
            ->where('status_akun', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'activeMenu' => 'dashboard',
            'total_petani' => $total_petani,
            'total_mitra' => $total_mitra,
            'transaksi_berhasil' => $transaksi_berhasil,
            'pupuk_tersalur' => $pupuk_tersalur,
            'trendDates' => json_encode($trendDates),
            'trendData' => json_encode($trendData),
            'recentTransactions' => $recentTransactions,
            'akunPending' => $akunPending,
            'pieLabels' => json_encode($pieLabels),
            'pieSeries' => json_encode($pieSeries)
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

        $pendingUsers = $query->latest()->paginate(10)->withQueryString();
        $kecamatans = \App\Models\Kecamatan::all();

        return view('admin.verifikasi', [
            'users' => $pendingUsers,
            'activeMenu' => 'verifikasi',
            'currentFilter' => $request->role,
            'kecamatans' => $kecamatans
        ]);
    }

    public function log_activity(Request $request)
    {
        $query = LogActivity::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('fitur', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.log_activity', [
            'logs' => $logs,
            'activeMenu' => 'log-activity'
        ]);
    }

    private function logActivity($aktivitas, $fitur, $detailPerubahan = null)
    {
        LogActivity::create([
            'id_user' => Auth::id(),
            'aktivitas' => $aktivitas,
            'fitur' => $fitur,
            'detail_perubahan' => $detailPerubahan ? json_encode($detailPerubahan) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function approve_akun($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status_akun' => 'aktif',
            'verified_by' => Auth::user()->admin->id_admin, // Mencatat ID Admin dari profil admin
        ]);
        
        $this->logActivity(
            'Menerima Verifikasi Akun',
            'Verifikasi Akun',
            ['username' => $user->username, 'role' => $user->role, 'status' => 'Diterima']
        );

        return back()->with('success', 'Akun berhasil diverifikasi!');
    }

    public function reject_akun($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status_akun' => 'ditolak',
            'verified_by' => Auth::user()->admin->id_admin
        ]);
        
        $this->logActivity(
            'Menolak Verifikasi Akun',
            'Verifikasi Akun',
            ['username' => $user->username, 'role' => $user->role, 'status' => 'Ditolak']
        );

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

        $kecamatans = Kecamatan::all();
        return view('admin.managepetani', compact('list_petani', 'kecamatans'));
    }

    public function export_petani(Request $request)
    {
        $query = Petani::with(['user', 'kecamatan', 'desa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_petani', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_kecamatan')) {
            $query->where('id_kecamatan', $request->id_kecamatan);
        }

        if ($request->filled('id_desa')) {
            $query->where('id_desa', $request->id_desa);
        }

        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status_akun', $request->status);
            });
        }

        $petanis = $query->latest()->get();
        $filename = "Data_Petani_" . date('Ymd_His') . ".xls";
        $columns = ['No', 'NIK', 'Nama Petani', 'Email', 'Jenis Kelamin', 'Kecamatan', 'Desa', 'Alamat', 'Status Akun', 'Tanggal Daftar'];
        
        $data = [];
        foreach ($petanis as $index => $p) {
            $data[] = [
                $index + 1,
                $p->nik,
                $p->nama_petani,
                $p->user->email ?? '-',
                $p->jenis_kelamin,
                $p->kecamatan->nama_kecamatan ?? '-',
                $p->desa->nama_desa ?? '-',
                $p->alamat_petani,
                strtoupper($p->user->status_akun ?? 'PENDING'),
                $p->created_at->format('d/m/Y')
            ];
        }

        return $this->exportToExcel($filename, 'Data Petani ASUP Ciamis', $columns, $data, '#10b981');
    }

    public function export_mitra(Request $request)
    {
        $query = \App\Models\Mitra::with(['user', 'kecamatan', 'desa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_kecamatan')) {
            $query->where('id_kecamatan', $request->id_kecamatan);
        }

        if ($request->filled('id_desa')) {
            $query->where('id_desa', $request->id_desa);
        }

        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status_akun', $request->status);
            });
        }

        $mitras = $query->latest('id_mitra')->get();
        $filename = "Data_Mitra_" . date('Ymd_His') . ".xls";
        $columns = ['No', 'Nomor Mitra', 'Nama Toko', 'Nama Pemilik', 'Email', 'NIK', 'No Rekening', 'Kecamatan', 'Desa', 'Alamat', 'Status Akun', 'Tanggal Daftar'];
        
        $data = [];
        foreach ($mitras as $index => $m) {
            $data[] = [
                $index + 1,
                $m->nomor_mitra ?? '-',
                $m->nama_mitra ?? '-',
                $m->nama_pemilik ?? '-',
                $m->user->email ?? '-',
                $m->nik,
                $m->no_rek,
                $m->kecamatan->nama_kecamatan ?? '-',
                $m->desa->nama_desa ?? '-',
                $m->alamat_mitra ?? '-',
                strtoupper($m->user->status_akun ?? 'PENDING'),
                $m->created_at->format('d/m/Y')
            ];
        }

        return $this->exportToExcel($filename, 'Data Mitra ASUP Ciamis', $columns, $data, '#7c3aed');
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
            
            $this->logActivity(
                'Mengubah Data Petani',
                'Manajemen Petani',
                ['username' => $user->username, 'nama_petani' => $request->nama_petani, 'nik' => $request->nik]
            );
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
            
            $this->logActivity(
                'Mengubah Data Mitra',
                'Manajemen Mitra',
                ['username' => $user->username, 'nama_mitra' => $request->nama_mitra, 'nama_pemilik' => $request->nama_pemilik]
            );
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
            
            $this->logActivity(
                'Mengubah Status Akun',
                'Manajemen Akun',
                ['username' => $user->username, 'status_baru' => $request->status]
            );

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
            ->paginate(10)->withQueryString();

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

        // 1. Cari id_admin yang benar
        $admin = DB::table('tabel_admin')
            ->where('id_user', Auth::user()->id_user)
            ->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Gagal: Anda bukan Admin.');
        }

        DB::beginTransaction();
        try {
            if ($action == 'setujui') {
                // Update status menjadi 'diproses'
                DB::table('tabel_permintaan')->where('id_permintaan', $id)->update([
                    'status_permintaan' => 'diproses', // Menggunakan enum baru Anda
                    'id_admin' => $admin->id_admin,
                    'updated_at' => now()
                ]);

                $pupuk_disetujui = $request->input('pupuk_disetujui', []);
                foreach ($pupuk_disetujui as $id_detail => $jml) {
                    $detail = DB::table('tabel_detail_permintaan')->where('id_detail_permintaan', $id_detail)->first();

                    // Simpan jumlah yang disetujui
                    DB::table('tabel_detail_permintaan')
                        ->where('id_detail_permintaan', $id_detail)
                        ->update(['jml_disetujui' => $jml]);

                    // KURANGI STOK PUSAT
                    DB::table('tabel_pupuk')
                        ->where('id_pupuk', $detail->id_pupuk)
                        ->decrement('stok', $jml);
                }

                $pesan = 'Permintaan berhasil disetujui dan sedang diproses.';
            } else {
                // Jika ditolak
                DB::table('tabel_permintaan')->where('id_permintaan', $id)->update([
                    'status_permintaan' => 'ditolak',
                    'id_admin' => $admin->id_admin,
                    'updated_at' => now()
                ]);
                $pesan = 'Permintaan telah ditolak.';
            }

            DB::commit();
            
            $this->logActivity(
                'Mengubah Status Permintaan Pupuk',
                'Permintaan Pupuk',
                ['id_permintaan' => $id, 'status' => $action]
            );
            
            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function transaksi(Request $request)
    {
        $query = \App\Models\Transaksi::with(['petani', 'mitra.kecamatan', 'mitra.desa', 'rincian.pupuk']);

        if ($request->filled('kecamatan')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_kecamatan', $request->kecamatan);
            });
        }

        if ($request->filled('desa')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_desa', $request->desa);
            });
        }

        if ($request->filled('mitra')) {
            $query->where('id_mitra', $request->mitra);
        }
        if ($request->filled('filter_bulan')) {
            $parts = explode('-', $request->filter_bulan);
            $query->whereYear('tgl_transaksi', $parts[0])
                  ->whereMonth('tgl_transaksi', $parts[1]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('petani', function ($q2) use ($search) {
                      $q2->where('nama_petani', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $transaksis = $query->orderBy('tgl_transaksi', 'desc')->paginate(10)->withQueryString();
        
        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desas = \App\Models\Desa::orderBy('nama_desa')->get();
        $mitras = \App\Models\Mitra::orderBy('nama_mitra')->get();

        return view('admin.transaksi', [
            'transaksis' => $transaksis,
            'kecamatans' => $kecamatans,
            'desas' => $desas,
            'mitras' => $mitras,
            'activeMenu' => 'transaksi'
        ]);
    }

    public function export_transaksi(Request $request)
    {
        $query = \App\Models\Transaksi::with(['petani', 'mitra', 'rincian.pupuk']);
        
        if ($request->filled('kecamatan')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_kecamatan', $request->kecamatan);
            });
        }

        if ($request->filled('desa')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_desa', $request->desa);
            });
        }

        if ($request->filled('mitra')) {
            $query->where('id_mitra', $request->mitra);
        }

        if ($request->filled('filter_bulan')) {
            $parts = explode('-', $request->filter_bulan);
            $query->whereYear('tgl_transaksi', $parts[0])
                  ->whereMonth('tgl_transaksi', $parts[1]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('petani', function ($q2) use ($search) {
                      $q2->where('nama_petani', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $transaksis = $query->orderBy('tgl_transaksi', 'desc')->get();
        
        $filename = "Data_Transaksi_" . date('Ymd_His') . ".xls";
        $columns = ['No', 'ID Transaksi', 'Tanggal', 'Nama Petani', 'NIK Petani', 'Nama Kios', 'Total Bayar', 'Metode', 'Status Bayar', 'Status Ambil'];
        
        $data = [];
        foreach ($transaksis as $index => $t) {
            $data[] = [
                $index + 1,
                $t->id_transaksi,
                \Carbon\Carbon::parse($t->tgl_transaksi)->format('d/m/Y H:i'),
                $t->petani->nama_petani ?? '-',
                $t->petani->nik ?? '-',
                $t->mitra->nama_mitra ?? '-',
                'Rp ' . number_format($t->total, 0, ',', '.'),
                strtoupper($t->metode_pembayaran),
                strtoupper($t->status_pembayaran),
                strtoupper($t->status_pengambilan)
            ];
        }

        return $this->exportToExcel($filename, 'Data Transaksi ASUP Ciamis', $columns, $data, '#ea580c');
    }

    public function permintaan_penarikan(Request $request)
    {
        $query = Penarikan::with('mitra');

        if ($request->filled('kecamatan')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_kecamatan', $request->kecamatan);
            });
        }

        if ($request->filled('desa')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_desa', $request->desa);
            });
        }

        if ($request->filled('mitra')) {
            $query->where('id_mitra', $request->mitra);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_penarikan', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($q2) use ($search) {
                      $q2->where('nama_mitra', 'like', "%{$search}%")
                         ->orWhere('nama_pemilik', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('filter_bulan')) {
            $parts = explode('-', $request->filter_bulan);
            $query->whereYear('created_at', $parts[0])
                  ->whereMonth('created_at', $parts[1]);
        }

        $penarikans = $query->orderByRaw("FIELD(status, 'pending') DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();
            
        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desas = \App\Models\Desa::orderBy('nama_desa')->get();
        $mitras = \App\Models\Mitra::orderBy('nama_mitra')->get();

        return view('admin.permintaan_penarikan', [
            'penarikans' => $penarikans,
            'kecamatans' => $kecamatans,
            'desas' => $desas,
            'mitras' => $mitras,
            'activeMenu' => 'permintaan-penarikan'
        ]);
    }

    public function export_penarikan(Request $request)
    {
        $query = Penarikan::with('mitra');

        if ($request->filled('kecamatan')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_kecamatan', $request->kecamatan);
            });
        }

        if ($request->filled('desa')) {
            $query->whereHas('mitra', function ($q) use ($request) {
                $q->where('id_desa', $request->desa);
            });
        }

        if ($request->filled('mitra')) {
            $query->where('id_mitra', $request->mitra);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_penarikan', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($q2) use ($search) {
                      $q2->where('nama_mitra', 'like', "%{$search}%")
                         ->orWhere('nama_pemilik', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('filter_bulan')) {
            $parts = explode('-', $request->filter_bulan);
            $query->whereYear('created_at', $parts[0])
                  ->whereMonth('created_at', $parts[1]);
        }

        $penarikans = $query->orderBy('created_at', 'desc')->get();
        
        $filename = "Data_Penarikan_" . date('Ymd_His') . ".xls";
        $columns = ['No', 'ID Penarikan', 'Tanggal', 'Nama Mitra', 'Pemilik', 'Bank/No Rek', 'Jumlah Transfer', 'Status'];
        
        $data = [];
        foreach ($penarikans as $index => $p) {
            $data[] = [
                $index + 1,
                $p->id_penarikan,
                $p->created_at->format('d/m/Y H:i'),
                $p->mitra->nama_mitra ?? '-',
                $p->mitra->nama_pemilik ?? '-',
                ($p->mitra->no_rek ?? '-'),
                'Rp ' . number_format($p->jml_transfer, 0, ',', '.'),
                strtoupper($p->status)
            ];
        }

        return $this->exportToExcel($filename, 'Data Penarikan Saldo Mitra', $columns, $data, '#2563eb');
    }

    public function update_penarikan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:success,failed'
        ]);

        DB::beginTransaction();
        try {
            $penarikan = \App\Models\Penarikan::findOrFail($id);
            
            // Hanya update jika statusnya masih pending
            if ($penarikan->status !== 'pending') {
                return back()->with('error', 'Status penarikan sudah diproses sebelumnya.');
            }

            $penarikan->status = $request->status;
            $penarikan->save();

            // Jika gagal (ditolak), kembalikan saldo ke mitra
            if ($request->status === 'failed') {
                $penarikan->mitra->increment('saldo_app', $penarikan->jml_transfer);
            }

            DB::commit();
            $status_text = $request->status === 'success' ? 'disetujui (Berhasil)' : 'ditolak (Gagal)';
            
            $this->logActivity(
                'Memproses Penarikan Saldo',
                'Penarikan Saldo',
                ['id_penarikan' => $penarikan->id_penarikan, 'status' => $request->status, 'jumlah' => $penarikan->jml_transfer]
            );
            
            return back()->with('success', "Penarikan sebesar Rp " . number_format($penarikan->jml_transfer, 0, ',', '.') . " untuk mitra " . ($penarikan->mitra->nama_mitra ?? 'Unknown') . " telah $status_text.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function cetak_penarikan($id)
    {
        $penarikan = \App\Models\Penarikan::with('mitra')->findOrFail($id);

        return view('admin.cetak_penarikan', compact('penarikan'));
    }

    public function rekonsiliasi(Request $request)
    {
        $bulanTahun = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $bulanTahun);

        $kecamatan = $request->input('kecamatan');
        $desa = $request->input('desa');
        $status = $request->input('status');
        $search = $request->input('search');

        // Stats Periode
        $transaksiPeriode = Transaksi::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->where('status_pembayaran', 'success');
            
        $totalTransaksiCount = $transaksiPeriode->count();
        $totalNominal = $transaksiPeriode->sum('total');

        // Query Mitra
        $mitrasQuery = Mitra::with(['kecamatan', 'desa']);

        if ($kecamatan) {
            $mitrasQuery->where('id_kecamatan', $kecamatan);
        }
        if ($desa) {
            $mitrasQuery->where('id_desa', $desa);
        }
        if ($search) {
            $mitrasQuery->where(function($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%");
            });
        }

        $mitras = $mitrasQuery->paginate(10)->withQueryString();

        $dataSesuai = 0;
        $dataSelisih = 0;

        $rekonsiliasiList = [];

        foreach ($mitras as $mitra) {
            // Hitung total transaksi sukses untuk mitra ini (All time)
            $totalMasuk = Transaksi::where('id_mitra', $mitra->id_mitra)
                ->where('status_pembayaran', 'success')
                ->sum('total');
            
            // Hitung total penarikan sukses untuk mitra ini (All time)
            $totalPenarikan = \App\Models\Penarikan::where('id_mitra', $mitra->id_mitra)
                ->where('status', 'success')
                ->sum('jml_transfer');

            $saldoHitungan = $totalMasuk - $totalPenarikan;
            $saldoApp = $mitra->saldo_app;
            
            $isMatch = ($saldoHitungan == $saldoApp);

            if ($isMatch) {
                $dataSesuai++;
            } else {
                $dataSelisih++;
            }

            // Apply filter status match/mismatch if requested
            if ($status == 'match' && !$isMatch) continue;
            if ($status == 'mismatch' && $isMatch) continue;

            $rekonsiliasiList[] = [
                'mitra' => $mitra,
                'total_masuk' => $totalMasuk,
                'total_penarikan' => $totalPenarikan,
                'saldo_hitungan' => $saldoHitungan,
                'saldo_app' => $saldoApp,
                'is_match' => $isMatch
            ];
        }

        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desas = \App\Models\Desa::orderBy('nama_desa')->get();

        return view('admin.rekonsiliasi', [
            'rekonsiliasiList' => $rekonsiliasiList,
            'totalTransaksiCount' => $totalTransaksiCount,
            'totalNominal' => $totalNominal,
            'dataSesuai' => $dataSesuai,
            'dataSelisih' => $dataSelisih,
            'kecamatans' => $kecamatans,
            'desas' => $desas,
            'bulanTahun' => $bulanTahun,
            'mitras' => $mitras,
            'activeMenu' => 'rekonsiliasi'
        ]);
    }

    public function store_rekonsiliasi(Request $request, $id_transaksi)
    {
        $request->validate([
            'status' => 'required|in:match,mismatch'
        ]);

        $admin = DB::table('tabel_admin')
            ->where('id_user', Auth::user()->id_user)
            ->first();

        if (!$admin) {
            return back()->with('error', 'Akses ditolak. Anda bukan admin.');
        }

        DB::beginTransaction();
        try {
            \App\Models\Rekonsiliasi::updateOrCreate(
                ['id_transaksi' => $id_transaksi],
                [
                    'id_admin' => $admin->id_admin,
                    'tgl_verifikasi' => now(),
                    'status' => $request->status
                ]
            );

            DB::commit();
            
            $this->logActivity(
                'Menyimpan Rekonsiliasi',
                'Rekonsiliasi',
                ['id_transaksi' => $id_transaksi, 'status' => $request->status]
            );
            
            return back()->with('success', 'Data rekonsiliasi transaksi #' . $id_transaksi . ' berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan rekonsiliasi: ' . $e->getMessage());
        }
    }

    public function laporan(Request $request)
    {
        $bulanTahun = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $bulanTahun);

        // Closure untuk filter lokasi
        $applyFilters = function($query) use ($request) {
            if ($request->filled('kecamatan')) {
                $query->whereHas('mitra', function ($q) use ($request) {
                    $q->where('id_kecamatan', $request->kecamatan);
                });
            }
            if ($request->filled('desa')) {
                $query->whereHas('mitra', function ($q) use ($request) {
                    $q->where('id_desa', $request->desa);
                });
            }
            if ($request->filled('mitra')) {
                $query->where('id_mitra', $request->mitra);
            }
        };

        $transaksiPeriode = Transaksi::where('status_pembayaran', 'success')
            ->whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan);
        $applyFilters($transaksiPeriode);

        $totalSubsidi = (clone $transaksiPeriode)->sum('total');
        
        $totalPenerima = (clone $transaksiPeriode)->distinct('id_petani')->count('id_petani');
        
        $pupukTersalurKg = \App\Models\DetailTransaksi::whereHas('transaksi', function($q) use ($tahun, $bulan, $applyFilters) {
            $q->where('status_pembayaran', 'success')
              ->whereYear('tgl_transaksi', $tahun)
              ->whereMonth('tgl_transaksi', $bulan);
            $applyFilters($q);
        })->sum('jml_beli');
        
        $pupukTersalurTon = $pupukTersalurKg / 1000;

        // Mitra Aktif (Bisa difilter per wilayah)
        $mitraQuery = User::where('role', 'Mitra')->where('status_akun', 'aktif');
        if ($request->filled('kecamatan') || $request->filled('desa') || $request->filled('mitra')) {
            $mitraQuery->whereHas('mitra', function($q) use ($request) {
                if ($request->filled('kecamatan')) $q->where('id_kecamatan', $request->kecamatan);
                if ($request->filled('desa')) $q->where('id_desa', $request->desa);
                if ($request->filled('mitra')) $q->where('id_mitra', $request->mitra);
            });
        }
        $mitraAktif = $mitraQuery->count();

        // Trend Penyaluran (6 Bulan Terakhir dari bulan terpilih)
        $trendBulan = [];
        $trendData = [];
        $selectedDate = \Carbon\Carbon::createFromFormat('Y-m-d', "$tahun-$bulan-01");

        for ($i = 5; $i >= 0; $i--) {
            $date = clone $selectedDate;
            $date->subMonths($i);
            
            $bulanStr = $date->translatedFormat('M');
            if ($bulanStr == 'Sep') $bulanStr = 'Sept';
            if ($bulanStr == 'Oct') $bulanStr = 'Okt';
            if ($bulanStr == 'Dec') $bulanStr = 'Des';
            
            $trendBulan[] = $bulanStr;
            
            $kg = \App\Models\DetailTransaksi::whereHas('transaksi', function($q) use ($date, $applyFilters) {
                $q->where('status_pembayaran', 'success')
                  ->whereYear('tgl_transaksi', $date->year)
                  ->whereMonth('tgl_transaksi', $date->month);
                $applyFilters($q);
            })->sum('jml_beli');
            
            $trendData[] = round($kg / 1000, 1);
        }

        // Distribusi Jenis Pupuk (di bulan terpilih)
        $distribusi = \App\Models\DetailTransaksi::selectRaw('id_pupuk, SUM(jml_beli) as total')
            ->whereHas('transaksi', function($q) use ($tahun, $bulan, $applyFilters) {
                $q->where('status_pembayaran', 'success')
                  ->whereYear('tgl_transaksi', $tahun)
                  ->whereMonth('tgl_transaksi', $bulan);
                $applyFilters($q);
            })
            ->groupBy('id_pupuk')
            ->with('pupuk')
            ->get();
            
        $pieLabels = [];
        $pieSeries = [];
        
        if($distribusi->isEmpty()) {
            // Jika kosong, berikan label default dengan nilai 0 agar grafik tetap muncul kosong
            $pieLabels = ['Urea', 'NPK Phonska', 'SP-36'];
            $pieSeries = [0, 0, 0];
        } else {
            foreach ($distribusi as $dist) {
                $pieLabels[] = $dist->pupuk ? $dist->pupuk->nama_pupuk : 'Unknown';
                $pieSeries[] = (float)$dist->total;
            }
        }

        // Pemerataan Penerima per Kecamatan
        $penerimaKecQuery = DB::table('tabel_transaksi')
            ->join('tabel_petani', 'tabel_transaksi.id_petani', '=', 'tabel_petani.id_petani')
            ->join('tabel_kecamatan', 'tabel_petani.id_kecamatan', '=', 'tabel_kecamatan.id_kecamatan')
            ->where('tabel_transaksi.status_pembayaran', 'success')
            ->whereYear('tabel_transaksi.tgl_transaksi', $tahun)
            ->whereMonth('tabel_transaksi.tgl_transaksi', $bulan);
        
        // Filter untuk breakdown kecamatan
        if ($request->filled('kecamatan')) {
            $penerimaKecQuery->where('tabel_petani.id_kecamatan', $request->kecamatan);
        }
        if ($request->filled('desa')) {
            $penerimaKecQuery->where('tabel_petani.id_desa', $request->desa);
        }
        if ($request->filled('mitra')) {
            $penerimaKecQuery->where('tabel_transaksi.id_mitra', $request->mitra);
        }

        $penerimaKecamatan = $penerimaKecQuery->select('tabel_kecamatan.nama_kecamatan', DB::raw('COUNT(DISTINCT tabel_transaksi.id_petani) as total'))
            ->groupBy('tabel_kecamatan.id_kecamatan', 'tabel_kecamatan.nama_kecamatan')
            ->get();

        $kecamatanLabels = [];
        $kecamatanSeries = [];

        if($penerimaKecamatan->isEmpty()) {
            $kecamatanLabels = ['Belum ada data'];
            $kecamatanSeries = [0];
        } else {
            foreach ($penerimaKecamatan as $kec) {
                $kecamatanLabels[] = $kec->nama_kecamatan;
                $kecamatanSeries[] = (int) $kec->total;
            }
        }

        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desas = \App\Models\Desa::orderBy('nama_desa')->get();
        $mitras = \App\Models\Mitra::orderBy('nama_mitra')->get();

        return view('admin.laporan', [
            'activeMenu' => 'laporan',
            'periode' => $bulanTahun,
            'totalSubsidi' => $totalSubsidi,
            'totalPenerima' => $totalPenerima,
            'pupukTersalurTon' => $pupukTersalurTon,
            'mitraAktif' => $mitraAktif,
            'trendBulan' => json_encode($trendBulan),
            'trendData' => json_encode($trendData),
            'pieLabels' => json_encode($pieLabels),
            'pieSeries' => json_encode($pieSeries),
            'kecamatanLabels' => json_encode($kecamatanLabels),
            'kecamatanSeries' => json_encode($kecamatanSeries),
            'kecamatans' => $kecamatans,
            'desas' => $desas,
            'mitras' => $mitras
        ]);
    }

    public function cetak_transaksi($id)
    {
        $transaksi = \App\Models\Transaksi::with(['petani', 'mitra', 'rincian.pupuk'])->where('id_transaksi', $id)->firstOrFail();
        return view('admin.cetak_transaksi', compact('transaksi'));
    }
}
