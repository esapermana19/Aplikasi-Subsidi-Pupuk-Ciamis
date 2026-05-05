<?php

namespace App\Http\Controllers;

use App\Models\Pupuk;
use Illuminate\Http\Request;

class PupukController extends Controller
{
    use \App\Traits\ExcelExportTrait;
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
        $pupuk = Pupuk::create($validated);

        \App\Models\LogActivity::create([
            'id_user' => \Illuminate\Support\Facades\Auth::id(),
            'aktivitas' => 'Menambahkan Pupuk Baru',
            'fitur' => 'Manajemen Pupuk',
            'detail_perubahan' => json_encode(['nama_pupuk' => $validated['nama_pupuk'], 'kode' => $validated['kode_pupuk']]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

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

        \App\Models\LogActivity::create([
            'id_user' => \Illuminate\Support\Facades\Auth::id(),
            'aktivitas' => 'Mengubah Data Pupuk',
            'fitur' => 'Manajemen Pupuk',
            'detail_perubahan' => json_encode(['nama_pupuk' => $validated['nama_pupuk'], 'kode' => $validated['kode_pupuk']]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', 'Data pupuk berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $pupuk = Pupuk::findOrFail($id);
        $pupuk->delete();

        \App\Models\LogActivity::create([
            'id_user' => \Illuminate\Support\Facades\Auth::id(),
            'aktivitas' => 'Menghapus Pupuk',
            'fitur' => 'Manajemen Pupuk',
            'detail_perubahan' => json_encode(['nama_pupuk' => $pupuk->nama_pupuk, 'kode' => $pupuk->kode_pupuk]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

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

        \App\Models\LogActivity::create([
            'id_user' => \Illuminate\Support\Facades\Auth::id(),
            'aktivitas' => 'Menambah Stok Pusat Pupuk',
            'fitur' => 'Manajemen Pupuk',
            'detail_perubahan' => json_encode(['nama_pupuk' => $pupuk->nama_pupuk, 'tambahan' => $request->tambahan_stok]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', "Stok pusat untuk pupuk {$pupuk->nama_pupuk} berhasil ditambahkan!");
    }

    public function export(Request $request)
    {
        $query = Pupuk::query();

        if ($request->filled('search')) {
            $query->where('nama_pupuk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status_stok')) {
            switch ($request->status_stok) {
                case 'kritis': $query->where('stok', '<', 1000); break;
                case 'menipis': $query->whereBetween('stok', [1000, 5000]); break;
                case 'aman': $query->where('stok', '>', 5000); break;
            }
        }

        $pupuks = $query->get()->map(function ($item) {
            $item->stok_pusat = $item->stok;
            $item->stok_mitra = \DB::table('tabel_detail_stok')
                ->where('id_pupuk', $item->id_pupuk)
                ->sum('jml_perubahan');
            $item->sedang_diproses = \DB::table('tabel_detail_permintaan')
                ->join('tabel_permintaan', 'tabel_detail_permintaan.id_permintaan', '=', 'tabel_permintaan.id_permintaan')
                ->where('id_pupuk', $item->id_pupuk)
                ->where('status_permintaan', 'diproses')
                ->sum('jml_disetujui');
            $item->total_stok = $item->stok_pusat + $item->stok_mitra + $item->sedang_diproses;
            return $item;
        });

        $filename = "Data_Pupuk_" . date('Ymd_His') . ".xls";
        $columns = ['No', 'Kode', 'Nama Pupuk', 'Stok Pusat (Kg)', 'Stok Mitra (Kg)', 'Sedang Diproses (Kg)', 'Total Stok (Kg)', 'Harga Subsidi', 'Tanggal Update'];
        
        $data = [];
        foreach ($pupuks as $index => $p) {
            $data[] = [
                $index + 1,
                $p->kode_pupuk,
                $p->nama_pupuk,
                number_format($p->stok_pusat, 0, ',', '.'),
                number_format($p->stok_mitra, 0, ',', '.'),
                number_format($p->sedang_diproses, 0, ',', '.'),
                number_format($p->total_stok, 0, ',', '.'),
                'Rp ' . number_format($p->harga_subsidi, 0, ',', '.'),
                $p->updated_at->format('d/m/Y H:i')
            ];
        }

        return $this->exportToExcel($filename, 'Data Stok Pupuk Bersubsidi', $columns, $data, '#0284c7'); // Biru sky-600 untuk pupuk
    }
}
