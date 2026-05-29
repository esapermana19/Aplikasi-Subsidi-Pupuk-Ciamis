<?php

namespace App\Http\Controllers;

use App\Models\Musim;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MusimController extends Controller
{
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

    public function index()
    {
        $musims = Musim::latest()->paginate(10);
        return view('superadmin.regulasi', [
            'musims' => $musims,
            'activeMenu' => 'regulasi'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_musim' => 'required|string|max:50',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'kuota_global' => 'required|numeric|min:0',
            'limit_per_petani' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Jika musim baru diset aktif, nonaktifkan yang lain
            if ($request->has('is_active')) {
                Musim::where('is_active', true)->update(['is_active' => false]);
            }

            $musim = Musim::create([
                'nama_musim' => $request->nama_musim,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
                'kuota_global' => $request->kuota_global,
                'limit_per_petani' => $request->limit_per_petani,
                'is_active' => $request->has('is_active')
            ]);

            $this->logActivity(
                "Menambah Musim Baru: {$musim->nama_musim}",
                'Regulasi Musim',
                $musim->toArray()
            );
        });

        return back()->with('success', 'Musim berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $musim = Musim::findOrFail($id);

        $request->validate([
            'nama_musim' => 'required|string|max:50',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'kuota_global' => 'required|numeric|min:0',
            'limit_per_petani' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $musim) {
            // Jika musim ini diset aktif, nonaktifkan yang lain
            if ($request->has('is_active') && !$musim->is_active) {
                Musim::where('is_active', true)->update(['is_active' => false]);
            }

            $musim->update([
                'nama_musim' => $request->nama_musim,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
                'kuota_global' => $request->kuota_global,
                'limit_per_petani' => $request->limit_per_petani,
                'is_active' => $request->has('is_active')
            ]);

            $this->logActivity(
                "Mengubah Data Musim: {$musim->nama_musim}",
                'Regulasi Musim',
                $musim->toArray()
            );
        });

        return back()->with('success', 'Musim berhasil diperbarui!');
    }

    public function toggle_status($id)
    {
        $musim = Musim::findOrFail($id);
        
        DB::transaction(function () use ($musim) {
            if (!$musim->is_active) {
                // Mengaktifkan: nonaktifkan yang lain dulu
                Musim::where('is_active', true)->update(['is_active' => false]);
                $musim->update(['is_active' => true]);
                $status = 'diaktifkan';
            } else {
                // Menonaktifkan
                $musim->update(['is_active' => false]);
                $status = 'dinonaktifkan';
            }

            $this->logActivity(
                "Mengubah Status Musim {$musim->nama_musim} menjadi " . ($musim->is_active ? 'Aktif' : 'Non-aktif'),
                'Regulasi Musim',
                ['id_musim' => $musim->id_musim, 'status' => $musim->is_active]
            );
        });

        return back()->with('success', 'Status musim berhasil diubah!');
    }

    public function destroy($id)
    {
        $musim = Musim::findOrFail($id);
        
        // Cek apakah sudah ada transaksi yang menggunakan musim ini
        if ($musim->transaksi()->exists()) {
            return back()->with('error', 'Musim tidak dapat dihapus karena sudah memiliki data transaksi.');
        }

        $nama = $musim->nama_musim;
        $musim->delete();

        $this->logActivity(
            "Menghapus Musim: {$nama}",
            'Regulasi Musim',
            ['id_musim' => $id, 'nama_musim' => $nama]
        );

        return back()->with('success', 'Musim berhasil dihapus!');
    }
}
