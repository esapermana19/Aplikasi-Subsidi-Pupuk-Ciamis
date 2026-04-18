<?php

namespace App\Http\Controllers;

use App\Models\Pupuk;
use Illuminate\Http\Request;

class PupukController extends Controller
{
    public function index(Request $request)
    {
        //Mengambil semua data pupuk
        $query = Pupuk::query();
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_pupuk', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status stok
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
        $pupuk = $query->get();
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
}
