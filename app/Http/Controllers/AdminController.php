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
        // Gunakan Eager Loading agar tidak berat saat load nama dari profil
        $query = User::with(['petani', 'mitra'])->where('status_akun', 'pending');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $pendingUsers = $query->latest()->get();

        return view('admin.verifikasi', [
            'users' => $pendingUsers,
            'activeMenu' => 'verifikasi',
            'currentFilter' => $request->role
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
    public function list_petani(Request $request)
    {
        $query = User::where('role', 'Petani')->with('petani');

        if ($request->filled('status')) {
            $query->where('status_akun', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('petani', function ($q) use ($search) {
                $q->where('nama_petani', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%");
            });
        }

        $petani = $query->latest()->paginate(10);

        return view('admin.managepetani', [
            'petani' => $petani,
            'activeMenu' => 'petani'
        ]);
    }

    // List Mitra dengan pencarian ke tabel_mitra
    public function list_mitra(Request $request)
    {
        $query = User::where('role', 'Mitra')->with('mitra');

        if ($request->filled('status')) {
            $query->where('status_akun', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mitra', function ($q) use ($search) {
                $q->where('nama_mitra', 'like', "%$search%")
                    ->orWhere('nama_pemilik', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%");
            });
        }

        $mitra = $query->latest()->paginate(10);

        return view('admin.managemitra', [
            'mitra' => $mitra,
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
            $userData = ['email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $user->mitra->update([
                'nama_mitra' => $request->nama_mitra,
                'nama_pemilik' => $request->nama_pemilik,
                'no_rek' => $request->no_rek,
                'alamat_mitra' => $request->alamat,
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
}
