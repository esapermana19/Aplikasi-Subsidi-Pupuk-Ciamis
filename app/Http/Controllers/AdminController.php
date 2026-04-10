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

    public function approve_akun($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->update([
            'status_akun' => 'aktif',
            'verified_by' => Auth::id(),
        ]);
        return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi!');
    }

    public function reject_akun($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Menghapus data user dari database

        return back()->with('success', 'Pendaftaran akun ' . $user->name . ' telah ditolak.');
    }
}
