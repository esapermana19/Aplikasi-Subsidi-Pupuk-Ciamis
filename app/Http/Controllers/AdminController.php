<?php

namespace App\Http\Controllers;

use App\Models\Pupuk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_petani' => User::where('role', 'petani')->count(),
            'jenis_pupuk' => Pupuk::count(),
            'mitra_aktif' => User::where('role', 'mitra')->where('status_akun', 'aktif')->count(),
            'pending_verifikasi' => User::where('status_akun', 'pending')->count(),
            'transaksi_hari_ini' => Transaksi::whereDate('created_at', today())->count(),
            'total_subsidi' => Transaksi::whereMonth('created_at', now()->month)->sum('total_harga') ?? 0,
        ];

        // Mengambil 5 aktivitas terbaru
        $recentActivities = User::with('verifiedUsers')->orderBy('updated_at', 'desc')->take(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'activeMenu' => 'dashboard' // Memberikan nilai untuk variabel $activeMenu
        ]);
    }

    //Fungsi Verifikasi
    public function verifikasi(Request $request)
    {
        $query = User::where('status_akun', 'pending');

        // Filter berdasarkan role jika ada request 'role'
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $pendingUsers = $query->latest()->get();

        return view('admin.verifikasi', [
            'users' => $pendingUsers,
            'activeMenu' => 'verifikasi',
            'currentFilter' => $request->role // Mengirim filter aktif ke view
        ]);
    }

    // Fungsi Approve Akun (Sinkronisasi Timestamp)
    public function approve_akun($id)
    {
        $user = User::findOrFail($id);
        $now = now(); // Variabel waktu yang sama untuk kedua kolom

        $user->update([
            'status_akun' => 'aktif',
            'verified_by' => Auth::id(),
            'email_verified_at' => $now,
            'updated_at' => $now, // Paksa sama agar diffInSeconds = 0
        ]);

        return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi!');
    }

    //Fungsi Reject Akun
    public function reject_akun($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status_akun' => 'ditolak',
            'verified_by' => Auth::id()
        ]);

        return back()->with('success', 'Pendaftaran akun ' . $user->name . ' telah ditolak.');
    }

    //Fungsi Ambil Data Petani
    public function list_petani(Request $request)
    {
        // 1. Mulai query dengan filter role tetap sebagai petani
        $query = User::where('role', 'petani');

        // 2. Tambahkan Filter Status (Jika admin memilih status di dropdown)
        if ($request->has('status') && $request->status != '') {
            $query->where('status_akun', $request->status);
        }

        // 3. Tambahkan Filter Search (Nama atau Email)
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // 4. Ambil data dengan pagination agar tampilan tetap rapi
        $petani = $query->latest()->paginate(10);

        // 5. Kirim data ke view
        return view('admin.managepetani', [
            'petani' => $petani,
            'activeMenu' => 'petani' // Pastikan ini sesuai dengan ID di sidebar agar menu menyala violet
        ]);
    }
    //Fungsi Ambil Data Mitra
    public function list_mitra(Request $request)
    {
        // 1. Mulai query dengan filter role tetap sebagai mitra
        $query = User::where('role', 'mitra');

        // 2. Tambahkan Filter Status (Jika admin memilih status di dropdown)
        if ($request->has('status') && $request->status != '') {
            $query->where('status_akun', $request->status);
        }

        // 3. Tambahkan Filter Search (Nama atau Email)
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // 4. Ambil data dengan pagination agar tampilan tetap rapi
        $mitra = $query->latest()->paginate(10);

        // 5. Kirim data ke view
        return view('admin.managemitra', [
            'mitra' => $mitra,
            'activeMenu' => 'mitra' // Pastikan ini sesuai dengan ID di sidebar agar menu menyala violet
        ]);
    }

    //Update Status Akun Petani
    public function update_status_petani(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi agar hanya status yang diizinkan yang masuk
        $request->validate([
            'status' => 'required|in:aktif,nonaktif,ditolak'
        ]);

        $user->update([
            'status_akun' => $request->status,
            'verified_by' => Auth::id() // Mencatat siapa yang terakhir mengubah
        ]);

        return back()->with('success', 'Status akun ' . $user->name . ' berhasil diubah menjadi ' . $request->status);
    }

    //Update Status Akun Mitra
    public function update_status_mitra(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi agar hanya status yang diizinkan yang masuk
        $request->validate([
            'status' => 'required|in:aktif,nonaktif,ditolak'
        ]);

        $user->update([
            'status_akun' => $request->status,
            'verified_by' => Auth::id() // Mencatat siapa yang terakhir mengubah
        ]);

        return back()->with('success', 'Status akun ' . $user->name . ' berhasil diubah menjadi ' . $request->status);
    }

    //Fungsi Update Data Petani
    public function update_petani(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'verified_by' => Auth::id()
        ];

        if ($request->filled('nik_nip')) $data['nik_nip'] = $request->nik_nip;
        if ($request->filled('password')) $data['password'] = bcrypt($request->password);

        $user->update($data); // updated_at akan otomatis berubah, memicu label "Data Diperbarui"

        return back()->with('success', 'Data berhasil diperbarui!');
    }
    public function update_m(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'verified_by' => Auth::id()
        ];

        if ($request->filled('nik_nip')) $data['nik_nip'] = $request->nik_nip;
        if ($request->filled('password')) $data['password'] = bcrypt($request->password);

        $user->update($data); // updated_at akan otomatis berubah, memicu label "Data Diperbarui"

        return back()->with('success', 'Data berhasil diperbarui!');
    }

    // Fungsi Verifikasi Petani (Sinkronisasi Timestamp)
    public function verifikasi_petani($id)
    {
        $user = User::findOrFail($id);
        $now = now();

        $user->update([
            'status_akun' => 'aktif',
            'email_verified_at' => $now,
            'updated_at' => $now,
            'verified_by' => Auth::id()
        ]);
        return back();
    }
}
