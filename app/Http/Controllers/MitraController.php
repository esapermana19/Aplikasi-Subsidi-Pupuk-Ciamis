<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Penarikan;
use App\Models\Permintaan;
use App\Models\Transaksi;
use App\Models\Pupuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $id_user = $user->id_user;

        // 1. Hitung Sisa Stok Keseluruhan Mitra dari tabel_detail_stok
        $total_stok_mitra = \DB::table('tabel_detail_stok')
            ->where('id_mitra', $user->mitra->id_mitra)
            ->sum('jml_perubahan');

        // Siapkan data statistik untuk dashboard
        $stats = [
            // Mengambil saldo dari tabel profil mitra
            'saldo_aktif' => $user->mitra->saldo_app ?? 0,

            // Mengambil total stok pupuk yang tersedia (bisa disesuaikan jika mitra punya tabel stok sendiri)
            'stok_pupuk' => $total_stok_mitra,

            // Jumlah transaksi yang dilakukan mitra di bulan ini
            'transaksi_bulan_ini' => Transaksi::where('id_mitra', $user->mitra->id_mitra)
                ->whereMonth('created_at', date('m'))
                ->count(),
        ];

        // Ambil aktivitas terkini (Transaksi, Permintaan, Pencairan)
        $id_mitra = $user->mitra->id_mitra;

        $transaksi = Transaksi::with(['petani', 'pupuk'])
            ->where('id_mitra', $id_mitra)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'transaksi',
                    'title' => 'Transaksi ' . ($item->petani->nama_petani ?? 'Petani'),
                    'subtitle' => 'Pengambilan ' . ($item->jumlah ?? 0) . 'kg ' . ($item->pupuk->nama_pupuk ?? 'Pupuk'),
                    'amount' => '+Rp ' . number_format($item->total ?? 0, 0, ',', '.'),
                    'amount_color' => 'text-green-600',
                    'date' => $item->created_at,
                    'icon' => 'receipt',
                    'icon_color' => 'text-green-700',
                    'icon_bg' => 'bg-green-50',
                ];
            });

        $permintaan = Permintaan::where('id_mitra', $id_mitra)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $statusColor = match ($item->status_permintaan) {
                    'disetujui' => 'text-blue-600',
                    'diterima' => 'text-green-600',
                    'ditolak' => 'text-red-600',
                    default => 'text-orange-600'
                };
                return [
                    'type' => 'permintaan',
                    'title' => 'Permintaan Pupuk',
                    'subtitle' => 'Status: ' . ucfirst($item->status_permintaan),
                    'amount' => '',
                    'amount_color' => $statusColor,
                    'date' => $item->created_at,
                    'icon' => 'truck',
                    'icon_color' => 'text-blue-700',
                    'icon_bg' => 'bg-blue-50',
                ];
            });

        $penarikan = Penarikan::where('id_mitra', $id_mitra)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'penarikan',
                    'title' => 'Penarikan Saldo',
                    'subtitle' => 'Status: ' . ucfirst($item->status),
                    'amount' => '-Rp ' . number_format($item->jml_transfer ?? 0, 0, ',', '.'),
                    'amount_color' => 'text-red-600',
                    'date' => $item->created_at,
                    'icon' => 'wallet',
                    'icon_color' => 'text-purple-700',
                    'icon_bg' => 'bg-purple-50',
                ];
            });

        $recentActivities = collect($transaksi)
            ->merge($permintaan)
            ->merge($penarikan)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        $chartDataRaw = Transaksi::where('id_mitra', $id_mitra)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->translatedFormat('D'); // Hari (Sen, Sel, dll)
            $chartValues[] = $chartDataRaw[$date] ?? 0;
        }

        return view('mitra.dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'activeMenu' => 'dashboard'
        ]);
    }

    public function pupuk_tersedia()
    {
        $id_user_login = Auth::user()->id_user; // id_user dari tabel_users
        $mitra = Mitra::where('id_user', $id_user_login)->first();

        if (!$mitra) {
            abort(403, 'Akses ditolak. Data Mitra tidak ditemukan.');
        }

        // Ambil semua jenis pupuk dari tabel_pupuk
        $pupukList = Pupuk::all()->map(function ($pupuk) use ($mitra) {

            // Hitung Sisa Stok dari tabel_detail_stok
            $pupuk->stok_mitra = \DB::table('tabel_detail_stok')
                ->where('id_mitra', $mitra->id_mitra)
                ->where('id_pupuk', $pupuk->id_pupuk)
                ->sum('jml_perubahan');

            return $pupuk;
        });

        return view('mitra.pupuk_tersedia', compact('pupukList'));
    }

    public function scanPage()
    {
        return view('mitra.scan');
    }

    public function scanDetail($id)
    {
        // Ambil id mitra yang sedang login
        $id_mitra = Auth::user()->mitra->id_mitra;

        // Cari transaksi berdasarkan ID QR Code dan pastikan itu milik Mitra ini
        $transaksi = DB::table('tabel_transaksi')
            ->join('tabel_petani', 'tabel_transaksi.id_petani', '=', 'tabel_petani.id_petani')
            ->where('tabel_transaksi.id_transaksi', $id)
            ->where('tabel_transaksi.id_mitra', $id_mitra)
            ->select('tabel_transaksi.*', 'tabel_petani.nama_petani')
            ->first();

        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Data transaksi tidak ditemukan atau bukan milik mitra ini!']);
        }

        // Ambil detail pupuk
        $details = DB::table('tabel_detail_transaksi')
            ->join('tabel_pupuk', 'tabel_detail_transaksi.id_pupuk', '=', 'tabel_pupuk.id_pupuk')
            ->where('id_transaksi', $id)
            ->get();

        return response()->json([
            'status' => 'success',
            'transaksi' => $transaksi,
            'details' => $details
        ]);
    }

    public function konfirmasiPengambilan($id)
    {
        $transaksi = DB::table('tabel_transaksi')->where('id_transaksi', $id)->first();

        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan!']);
        }

        if ($transaksi->status_pengambilan === 'sudah') {
            return response()->json(['status' => 'error', 'message' => 'Pupuk sudah diambil sebelumnya!']);
        }

        DB::table('tabel_transaksi')
            ->where('id_transaksi', $id)
            ->update(['status_pengambilan' => 'sudah']);

        // Tambah saldo app mitra
        DB::table('tabel_mitra')
            ->where('id_mitra', $transaksi->id_mitra)
            ->increment('saldo_app', $transaksi->total);

        return response()->json(['status' => 'success', 'message' => 'Pupuk berhasil diserahkan kepada petani!']);
    }

    public function riwayat()
    {
        // Mengambil transaksi yang statusnya sudah diambil, diurutkan dari terbaru
        $riwayat = Transaksi::with('petani')->where('status_pengambilan', 'sudah')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('mitra.riwayat_transaksi', compact('riwayat'));
    }

    public function cetak_transaksi($id)
    {
        $id_mitra = Auth::user()->mitra->id_mitra;
        $transaksi = Transaksi::with(['petani', 'rincian.pupuk'])
            ->where('id_transaksi', $id)
            ->where('id_mitra', $id_mitra)
            ->firstOrFail();

        return view('mitra.cetak_transaksi', compact('transaksi'));
    }

    public function saldo()
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        if (!$mitra) {
            abort(403, 'Akses ditolak. Data Mitra tidak ditemukan.');
        }

        $saldo = $mitra->saldo_app;

        $riwayat_transaksi = Transaksi::with(['petani'])
            ->where('id_mitra', $mitra->id_mitra)
            ->where('status_pengambilan', 'sudah')
            ->orderBy('updated_at', 'desc')
            ->get();
        $riwayat_penarikan = Penarikan::where('id_mitra', $mitra->id_mitra)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('mitra.saldo', compact('saldo', 'riwayat_transaksi', 'riwayat_penarikan'));
    }

    public function proses_tarik_saldo(Request $request)
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        $request->validate([
            'jml_transfer' => 'required|numeric|min:10000',
        ], [
            'jml_transfer.min' => 'Nominal penarikan minimal Rp 10.000'
        ]);

        $jml_transfer = $request->jml_transfer;

        if ($mitra->saldo_app < $jml_transfer) {
            return back()->with('error', 'Saldo tidak mencukupi untuk melakukan penarikan ini.');
        }

        // Cek apakah hari ini sudah melakukan penarikan
        $hasWithdrawnToday = Penarikan::where('id_mitra', $mitra->id_mitra)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($hasWithdrawnToday) {
            return back()->with('error', 'Anda hanya dapat melakukan penarikan saldo 1 kali dalam sehari.');
        }

        // Generate ID: 4 digit nomor_mitra + YYMMDD
        // Jika nomor mitra tidak ada atau kurang, ambil dari id_mitra
        $nomor_mitra = $mitra->nomor_mitra ? substr(str_pad($mitra->nomor_mitra, 4, '0', STR_PAD_LEFT), 0, 4) : substr(str_pad($mitra->id_mitra, 4, '0', STR_PAD_LEFT), 0, 4);
        $dateStr = now()->format('ymd'); // 6 digit (contoh: 260503)
        $id_penarikan = $nomor_mitra . $dateStr;

        // Pastikan unik
        if (Penarikan::where('id_penarikan', $id_penarikan)->exists()) {
            return back()->with('error', 'ID Penarikan sudah terdaftar hari ini.');
        }

        DB::beginTransaction();
        try {
            // Kurangi saldo
            $mitra->decrement('saldo_app', $jml_transfer);

            // Insert tabel_penarikan
            Penarikan::create([
                'id_penarikan' => $id_penarikan,
                'id_mitra' => $mitra->id_mitra,
                'jml_transfer' => $jml_transfer,
                'tgl_transfer' => now()->toDateString(),
                'status' => 'pending'
            ]);

            DB::commit();
            return back()->with('success', 'Permintaan penarikan saldo berhasil diajukan dan sedang diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Laporan Mitra dengan Grafik
     */
    public function laporan(Request $request)
    {
        $user = Auth::user();
        $id_mitra = $user->mitra->id_mitra;
        
        // Filter Periode (Bulan-Tahun)
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        // 1. Ringkasan Statistik Periode Ini
        $transaksiPeriode = Transaksi::where('id_mitra', $id_mitra)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('status_pembayaran', 'success');

        $totalNominal = $transaksiPeriode->sum('total');
        
        $totalVolume = DB::table('tabel_detail_transaksi')
            ->join('tabel_transaksi', 'tabel_detail_transaksi.id_transaksi', '=', 'tabel_transaksi.id_transaksi')
            ->where('tabel_transaksi.id_mitra', $id_mitra)
            ->whereYear('tabel_transaksi.created_at', $tahun)
            ->whereMonth('tabel_transaksi.created_at', $bulan)
            ->where('tabel_transaksi.status_pembayaran', 'success')
            ->sum('tabel_detail_transaksi.jml_beli');

        $totalTransaksi = $transaksiPeriode->count();
        $petaniUnik = $transaksiPeriode->distinct('id_petani')->count('id_petani');

        // 2. Data Grafik Penjualan Harian (Line Chart)
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $chartLabels = [];
        $chartData = [];

        $salesDaily = Transaksi::where('id_mitra', $id_mitra)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('status_pembayaran', 'success')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%s-%s-%02d', $tahun, $bulan, $d);
            $chartLabels[] = $d;
            $chartData[] = (int)($salesDaily[$date] ?? 0);
        }

        // 3. Distribusi Pupuk Terjual (Pie/Donut Chart)
        $pupukDist = DB::table('tabel_detail_transaksi')
            ->join('tabel_transaksi', 'tabel_detail_transaksi.id_transaksi', '=', 'tabel_transaksi.id_transaksi')
            ->join('tabel_pupuk', 'tabel_detail_transaksi.id_pupuk', '=', 'tabel_pupuk.id_pupuk')
            ->where('tabel_transaksi.id_mitra', $id_mitra)
            ->whereYear('tabel_transaksi.created_at', $tahun)
            ->whereMonth('tabel_transaksi.created_at', $bulan)
            ->where('tabel_transaksi.status_pembayaran', 'success')
            ->select('tabel_pupuk.nama_pupuk', DB::raw('SUM(tabel_detail_transaksi.jml_beli) as total'))
            ->groupBy('tabel_pupuk.nama_pupuk')
            ->get();

        $pieLabels = $pupukDist->pluck('nama_pupuk')->toArray();
        $pieSeries = $pupukDist->pluck('total')->map(fn($val) => (int)$val)->toArray();

        // 4. Riwayat Transaksi Terakhir di Periode Ini
        $recentTransactions = Transaksi::with(['petani'])
            ->where('id_mitra', $id_mitra)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('status_pembayaran', 'success')
            ->latest()
            ->take(10)
            ->get();

        return view('mitra.laporan', [
            'totalNominal' => $totalNominal,
            'totalVolume' => $totalVolume,
            'totalTransaksi' => $totalTransaksi,
            'petaniUnik' => $petaniUnik,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'pieLabels' => $pieLabels,
            'pieSeries' => $pieSeries,
            'recentTransactions' => $recentTransactions,
            'periode' => $periode,
            'activeMenu' => 'mitra.laporan'
        ]);
    }
}
