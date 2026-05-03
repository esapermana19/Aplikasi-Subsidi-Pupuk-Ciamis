<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class TransaksiController extends Controller
{
    public function getPupukByMitra($id_mitra)
    {
        // Ambil semua jenis pupuk
        $pupuks = \App\Models\Pupuk::all();

        $dataPupuk = $pupuks->map(function ($pupuk) use ($id_mitra) {
            // Hitung Sisa Stok Mitra dari tabel_detail_stok
            $stok_mitra = DB::table('tabel_detail_stok')
                ->where('id_mitra', $id_mitra)
                ->where('id_pupuk', $pupuk->id_pupuk)
                ->sum('jml_perubahan');

            // Dummy jatah petani (Silakan sesuaikan dengan logika kuota jatah petani nantinya)
            $sisa_jatah_petani = ($pupuk->nama_pupuk == 'Urea') ? 150 : 75;

            return [
                'id_pupuk'          => $pupuk->id_pupuk,
                'nama_pupuk'        => $pupuk->nama_pupuk,
                'harga_subsidi'     => $pupuk->harga_subsidi,
                'stok_mitra'        => (int)max(0, $stok_mitra),
                'sisa_jatah_petani' => $sisa_jatah_petani
            ];
        });

        // Filter: Hanya tampilkan pupuk yang stoknya > 0 di mitra tersebut
        $filteredData = $dataPupuk->filter(function ($item) {
            return $item['stok_mitra'] > 0;
        })->values();

        return response()->json($filteredData);
    }

    public function prosesCheckout(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Simpan Transaksi ke Database (Status: Pending)
            $total_harga = $request->total_pembayaran;

            // Generate custom ID: YYMMDDNNN (TahunBulanTanggalNoUrut)
            $prefix = now()->format('ymd'); // Contoh: 260501
            $lastTransaksi = DB::table('tabel_transaksi')
                ->where('id_transaksi', 'LIKE', $prefix . '%')
                ->orderBy('id_transaksi', 'desc')
                ->first();

            if ($lastTransaksi) {
                $lastId = (string)$lastTransaksi->id_transaksi;
                $sequence = (int)substr($lastId, -3);
                $newSequence = str_pad($sequence + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newSequence = '001';
            }

            $id_transaksi = (int)($prefix . $newSequence);

            DB::table('tabel_transaksi')->insert([
                'id_transaksi' => $id_transaksi,
                'id_petani' => Auth::user()->petani->id_petani,
                'id_mitra' => $request->id_mitra,
                'tgl_transaksi' => now(),
                'total' => $total_harga,
                'status_pembayaran' => 'pending',
                'status_pengambilan' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Looping data keranjang untuk disimpan ke tabel_detail_transaksi
            foreach ($request->keranjang as $item) {
                DB::table('tabel_detail_transaksi')->insert([
                    'id_transaksi' => $id_transaksi,
                    'id_pupuk' => $item['id'],
                    'jml_beli' => $item['jumlah'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            // 2. Konfigurasi Midtrans
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION') === 'true';
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // 3. Buat Parameter untuk Midtrans
            // Gunakan ID integer sebagai order_id (bisa ditambah prefix jika ingin string, tapi harus unik)
            // Karena Midtrans order_id bisa string, kita bisa buat 'INV-ID'
            $order_id = 'INV-' . $id_transaksi . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $order_id,
                    'gross_amount' => (int)$total_harga,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->petani->nama_petani,
                    'email' => Auth::user()->email,
                ],
                'enabled_payments' => ['gopay', 'qris'],
            ];

            // 4. Dapatkan Snap Token
            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            // Kembalikan token ke Frontend
            return response()->json(['snap_token' => $snapToken, 'id_transaksi' => $id_transaksi]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function notificationHandler(Request $request)
    {
        try {
            $payload = $request->getContent();
            $notification = json_decode($payload);

            if (!$notification) {
                Log::error('Midtrans Webhook Error: Payload is empty or invalid JSON');
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . env('MIDTRANS_SERVER_KEY'));

            if ($notification->signature_key != $validSignatureKey) {
                Log::error('Midtrans Webhook Error: Invalid signature key');
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id; // Dari Midtrans: INV-260501001-171454

            // PERBAIKAN: Ambil ID Transaksi dari Order ID
            // Format Order ID: INV-{id_transaksi}-{timestamp}
            $parts = explode('-', $orderId);
            $realIdTransaksi = $parts[1] ?? $orderId;

            Log::info('Webhook Midtrans Diterima | Order ID: ' . $orderId . ' | Status: ' . $transactionStatus . ' | Real ID: ' . $realIdTransaksi);

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                
                // 1. CARI TRANSAKSI
                $transaksi = DB::table('tabel_transaksi')->where('id_transaksi', $realIdTransaksi)->first();

                // Jika transaksi tidak ada di database
                if (!$transaksi) {
                    Log::error('GAGAL POTONG STOK: Transaksi ' . $realIdTransaksi . ' tidak ditemukan di database.');
                    return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
                }

                // 2. Update Status Pembayaran
                DB::table('tabel_transaksi')
                    ->where('id_transaksi', $realIdTransaksi)
                    ->update(['status_pembayaran' => 'success']);

                // 3. Ambil detail untuk potong jatah/stok
                $details = DB::table('tabel_detail_transaksi')->where('id_transaksi', $realIdTransaksi)->get();

                foreach ($details as $item) {
                    // Hitung stok sekarang di mitra tersebut
                    $stok_sekarang = DB::table('tabel_detail_stok')
                        ->where('id_mitra', $transaksi->id_mitra)
                        ->where('id_pupuk', $item->id_pupuk)
                        ->sum('jml_perubahan');

                    // Log pengurangan stok
                    DB::table('tabel_detail_stok')->insert([
                        'id_pupuk' => $item->id_pupuk,
                        'id_mitra' => $transaksi->id_mitra,
                        'id_detail_transaksi' => $item->id_detail ?? null, 
                        'stok_awal' => (int) $stok_sekarang,
                        'jml_perubahan' => -$item->jml_beli, 
                        'stok_akhir' => (int) $stok_sekarang - $item->jml_beli,
                        'ket' => 'Penjualan ke Petani (INV: ' . $orderId . ')',
                        'created_at' => now()
                    ]);
                }
                
                Log::info('SUKSES: Stok berhasil dipotong untuk transaksi ' . $realIdTransaksi);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook 500 Error: ' . $e->getMessage() . ' di baris ' . $e->getLine() . ' pada ' . $e->getFile());
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function riwayat()
    {
        $id_petani = Auth::user()->petani->id_petani;

        $transaksi = DB::table('tabel_transaksi')
            ->join('tabel_mitra', 'tabel_transaksi.id_mitra', '=', 'tabel_mitra.id_mitra')
            ->where('tabel_transaksi.id_petani', $id_petani)
            ->select('tabel_transaksi.*', 'tabel_mitra.nama_mitra as nama_kios', 'tabel_mitra.nomor_mitra', 'tabel_transaksi.total as total_harga')
            ->orderBy('tgl_transaksi', 'desc')
            ->get();

        return view('petani.riwayat', compact('transaksi'));
    }

    // API untuk ambil detail transaksi (untuk isi Modal)
    public function detail($id)
    {
        $transaksi = DB::table('tabel_transaksi')
            ->join('tabel_mitra', 'tabel_transaksi.id_mitra', '=', 'tabel_mitra.id_mitra')
            ->where('id_transaksi', $id)
            ->select('tabel_transaksi.*', 'tabel_mitra.nama_mitra as nama_kios', 'tabel_mitra.nomor_mitra', 'tabel_transaksi.total as total_harga')
            ->first();

        $details = DB::table('tabel_detail_transaksi')
            ->join('tabel_pupuk', 'tabel_detail_transaksi.id_pupuk', '=', 'tabel_pupuk.id_pupuk')
            ->where('id_transaksi', $id)
            ->get();

        return response()->json([
            'transaksi' => $transaksi,
            'details' => $details
        ]);
    }
}
