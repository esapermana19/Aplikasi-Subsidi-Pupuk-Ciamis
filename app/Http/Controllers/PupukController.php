<?php

namespace App\Http\Controllers;

use App\Models\Pupuk;
use Illuminate\Http\Request;

class PupukController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil query dasar data pupuk
        $query = Pupuk::query();

        // 1. Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_pupuk', 'like', '%' . $request->search . '%');
        }

        // 2. Filter berdasarkan status stok (Sekarang mengacu ke Stok Pusat)
        if ($request->has('status_stok') && $request->status_stok != '') {
            switch ($request->status_stok) {
                case 'kritis':
                    $query->where('stok', '<', 1000);
                    break;
                case 'menipis':
                    $query->whereBetween('stok', [1000, 5000]);
                    break;
                case 'aman':
                    $query->where('stok', '>', 5000);
                    break;
            }
        }

        // 3. Ambil data hasil filter, lalu hitung kalkulasi stok pecahannya
        $pupuk = \App\Models\Pupuk::all()->map(function ($item) {
            // 1. Stok Pusat (Stok yang ada di gudang pusat saat ini)
            $item->stok_pusat = $item->stok;

            // 2. Stok di Mitra (Total dari tabel_detail_stok)
            $item->stok_mitra = \DB::table('tabel_detail_stok')
                ->where('id_pupuk', $item->id_pupuk)
                ->sum('jml_perubahan');

            // 3. Sedang Diproses (Stok berkurang dari pusat tapi belum diterima mitra)
            $item->sedang_diproses = \DB::table('tabel_detail_permintaan')
                ->join('tabel_permintaan', 'tabel_detail_permintaan.id_permintaan', '=', 'tabel_permintaan.id_permintaan')
                ->where('id_pupuk', $item->id_pupuk)
                ->where('status_permintaan', 'diproses')
                ->sum('jml_disetujui');

            // 4. Total Stok Sistem (Gabungan stok fisik pusat + mitra + dalam perjalanan)
            $item->total_stok = $item->stok_pusat + $item->stok_mitra + $item->sedang_diproses;

            return $item;
        });

        return view('admin.managepupuk', [
            'pupuk' => $pupuk,
            'activeMenu' => 'pupuk', // Agar menu di sidebar menyala hijau
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pupuk' => 'required|string|max:255',
            'kode_pupuk' => 'required|string|size:5|unique:tabel_pupuk,kode_pupuk',
            'stok' => 'required|numeric|min:0',
            'harga_subsidi' => 'required|numeric|min:0',
            'img_pupuk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
        ]);

        // Proses Unggah Gambar
        if ($request->hasFile('img_pupuk')) {
            // Simpan file ke folder storage/app/public/pupuk
            $path = $request->file('img_pupuk')->store('pupuk', 'public');
            // Simpan path-nya ke array validated agar masuk ke DB
            $validated['img_pupuk'] = $path;
        }

        // Menggunakan data yang sudah tervalidasi lebih aman daripada $request->all()
        Pupuk::create($validated);

        return back()->with('success', 'Pupuk baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $pupuk = Pupuk::findOrFail($id);

        $validated = $request->validate([
            'nama_pupuk' => 'required|string|max:255',
            // Tambahkan ',id_pupuk' di akhir aturan unique
            'kode_pupuk' => 'required|string|size:5|unique:tabel_pupuk,kode_pupuk,' . $id . ',id_pupuk',
            'stok' => 'required|numeric|min:0',
            'harga_subsidi' => 'required|numeric|min:0',
            'img_pupuk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
        ]);

        // Proses Unggah Gambar
        if ($request->hasFile('img_pupuk')) {
            // Simpan file ke folder storage/app/public/pupuk
            $path = $request->file('img_pupuk')->store('pupuk', 'public');
            // Simpan path-nya ke array validated agar masuk ke DB
            $validated['img_pupuk'] = $path;
        }

        $pupuk->update($validated);

        return back()->with('success', 'Data pupuk berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $pupuk = Pupuk::findOrFail($id);
        $pupuk->delete();

        return back()->with('success', 'Pupuk berhasil dihapus!');
    }

    public function tambahStok(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tambahan_stok' => 'required|integer|min:1'
        ]);

        // Cari data pupuk
        $pupuk = Pupuk::findOrFail($id);

        // Tambahkan stok lama dengan stok baru
        $pupuk->increment('stok', $request->tambahan_stok);

        return back()->with('success', "Stok pusat untuk pupuk {$pupuk->nama_pupuk} berhasil ditambahkan!");
    }
}
