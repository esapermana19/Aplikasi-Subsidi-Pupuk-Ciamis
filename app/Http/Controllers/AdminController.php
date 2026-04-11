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
        $recentActivities = User::latest()->take(5)->get();

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

    //Fungsi Approve Akun
    public function approve_akun($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->update([
            'status_akun' => 'aktif',
            'verified_by' => Auth::id(),
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

    //Update Status Akun
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

    //Fungsi Update Data Petani
    public function update_petani(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ];

        // Jika password diisi, tambahkan validasi
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Update password hanya jika diinputkan
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data petani ' . $user->name . ' berhasil diperbarui!');
    }
}
