<?php

namespace App\Http\Controllers;

use App\Models\Pupuk;
use Illuminate\Http\Request;

class PupukController extends Controller
{
    public function index()
    {
        //Mengambil semua data pupuk
        $pupuk = Pupuk::all();
        return view('admin.managepupuk', [
            'pupuk' => $pupuk,
            'activeMenu' => 'pupuk', // Agar menu di sidebar menyala hijau
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pupuk' => 'required|string|max:255',
            'kode_pupuk' => 'required|string|size:5|unique:pupuk,kode_pupuk',
            'stok' => 'required|numeric|min:0',
            'harga_subsidi' => 'required|numeric|min:0',
        ]);

        Pupuk::create($request->all());

        return back()->with('success', 'Pupuk baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $pupuk = Pupuk::findOrFail($id);

        $request->validate([
            'nama_pupuk' => 'required|string|max:255',
            // Tambahkan ',id_pupuk' di akhir aturan unique
            'kode_pupuk' => 'required|string|size:5|unique:pupuk,kode_pupuk,' . $id . ',id_pupuk',
            'stok' => 'required|numeric|min:0',
            'harga_subsidi' => 'required|numeric|min:0',
        ]);

        $pupuk->update($request->all());

        return back()->with('success', 'Data pupuk berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $pupuk = Pupuk::findOrFail($id);
        $pupuk->delete();

        return back()->with('success', 'Pupuk berhasil dihapus!');
    }
}
