@extends('layouts.app')

@section('content')
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-green-700">Scan QR Code Petani</h2>
        <p class="text-sm text-gray-500 mt-1">Arahkan kamera ke QR Code faktur petani.</p>
    </div>

    <div class="max-w-md mx-auto">
        <div id="reader" class="rounded-2xl overflow-hidden shadow-lg border-2 border-green-500 mb-6 bg-black"></div>

        <div id="manual-input-container" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
            <p class="text-sm font-bold text-gray-700 mb-2">Atau masukkan ID Transaksi manual:</p>
            <div class="flex gap-2">
                <input type="text" id="manual-tx-id" placeholder="Contoh: 260503001" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-green-500 text-sm">
                <button onclick="processManualInput()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">Cari</button>
            </div>
        </div>

        <div id="result-card" class="hidden bg-white p-6 rounded-xl border border-gray-100 shadow-md">
            <div class="text-center mb-4 border-b pb-4">
                <h3 class="font-bold text-gray-800 text-lg">Hasil Scan Ditemukan!</h3>
                <p id="tx-id" class="text-sm text-gray-500 uppercase font-mono"></p>
            </div>

            <div class="mb-4">
                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Nama Petani</p>
                <p id="tx-petani" class="font-bold text-gray-800 text-lg mb-3"></p>

                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Status Pembayaran</p>
                <span id="tx-status" class="px-2 py-1 rounded text-xs font-bold"></span>

                <div class="mt-3">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Status Pengambilan</p>
                    <span id="tx-pengambilan" class="px-2 py-1 rounded text-xs font-bold"></span>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-xs text-gray-400 font-bold uppercase mb-2">Barang yang diambil:</p>
                <ul id="tx-items" class="text-sm text-gray-700 space-y-1 font-medium">
                </ul>
            </div>

            <div id="action-btn-container" class="mt-6">
            </div>
            <button onclick="resetScanner()"
                class="w-full mt-2 py-2 text-sm text-gray-500 font-bold hover:text-gray-700">Scan Ulang</button>
        </div>
    </div>

    <style>
        /* Mengubah warna teks biasa di dalam area scanner menjadi putih */
        #reader {
            color: white !important;
            background-color: #000000;
            /* Beri latar hitam agar kontras */
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e5e7eb !important;
            /* Border abu-abu tipis */
        }

        /* Mengubah gaya teks link (seperti "Scan an Image File") */
        #reader a {
            color: #60a5fa !important;
            /* Warna biru muda cerah */
            text-decoration: underline;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }

        #reader a:hover {
            color: #93c5fd !important;
        }

        /* Mengubah gaya tombol-tombol aksi (Request Permission, Stop Scanning, dll) */
        #reader button {
            background-color: #16a34a !important;
            /* Warna hijau Tailwind */
            color: white !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            border: none !important;
            margin: 10px 5px !important;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Efek saat tombol di-hover (disentuh mouse) */
        #reader button:hover {
            background-color: #15803d !important;
            /* Hijau lebih gelap */
        }

        /* Merapikan posisi badge/tulisan yang muncul saat kamera memuat */
        #reader span {
            display: block;
            margin-bottom: 10px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Gunakan var untuk menghindari bentrok deklarasi ganda
        var scannerApp;
        var isScanning = true;

        // Konfigurasi Scanner
        function initScanner() {
            scannerApp = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                false
            );
            scannerApp.render(onScanSuccess, onScanFailure);
        }

        // Fungsi dijalankan saat QR berhasil terbaca
        async function onScanSuccess(decodedText, decodedResult) {
            if (!isScanning) return;
            isScanning = false;

            // Jeda scanner sementara
            try {
                scannerApp.pause();
            } catch (e) {}

            try {
                // Ambil data dari server
                const response = await fetch(`/mitra/scan/detail/${decodedText}`);
                const data = await response.json();

                if (data.status === 'error') {
                    Swal.fire('Oops!', data.message, 'error');
                    resetScanner();
                    return;
                }

                const t = data.transaksi;

                // Isi data ke HTML
                document.getElementById('tx-id').innerText = t.id_transaksi;
                document.getElementById('tx-petani').innerText = t.nama_petani;

                // Set Badge Status Pembayaran
                const statusBadge = document.getElementById('tx-status');
                if (t.status_pembayaran === 'success') {
                    statusBadge.className = "px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700";
                    statusBadge.innerText = "SUDAH DIBAYAR";
                } else {
                    statusBadge.className = "px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700";
                    statusBadge.innerText = "BELUM DIBAYAR / GAGAL";
                }

                // Set Badge Status Pengambilan
                const pengambilanBadge = document.getElementById('tx-pengambilan');
                if (t.status_pengambilan === 'sudah') {
                    pengambilanBadge.className = "px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700";
                    pengambilanBadge.innerText = "SUDAH DIAMBIL";
                } else {
                    pengambilanBadge.className = "px-2 py-1 rounded text-xs font-bold bg-yellow-100 text-yellow-800";
                    pengambilanBadge.innerText = "BELUM DIAMBIL";
                }

                // Isi list pupuk
                let itemsHtml = '';
                data.details.forEach(item => {
                    itemsHtml += `<li>✔ ${item.nama_pupuk} (${item.jml_beli} Kg)</li>`;
                });
                document.getElementById('tx-items').innerHTML = itemsHtml;

                // Tombol Konfirmasi
                const btnContainer = document.getElementById('action-btn-container');
                if (t.status_pembayaran === 'success' && t.status_pengambilan !== 'sudah') {
                    btnContainer.innerHTML =
                        `<button onclick="konfirmasi('${t.id_transaksi}')" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700">Serahkan Pupuk</button>`;
                } else if (t.status_pengambilan === 'sudah') {
                    btnContainer.innerHTML =
                        `<button disabled class="w-full bg-gray-400 text-white font-bold py-3 rounded-xl cursor-not-allowed">Pupuk Sudah Diambil Sebelumnya</button>`;
                } else {
                    btnContainer.innerHTML =
                        `<div class="bg-red-100 text-red-700 text-center font-bold py-3 rounded-xl">Menunggu Pembayaran Petani</div>`;
                }

                // Tampilkan hasil, sembunyikan scanner
                document.getElementById('result-card').classList.remove('hidden');
                document.getElementById('reader').classList.add('hidden');
                document.getElementById('manual-input-container').classList.add('hidden');

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                resetScanner();
            }
        }

        function onScanFailure(error) {
            // Abaikan saja
        }

        // Fungsi Tombol Konfirmasi Serahkan Pupuk
        async function konfirmasi(id) {
            const confirmResult = await Swal.fire({
                title: 'Serahkan Pupuk?',
                text: "Pastikan jumlah pupuk sudah sesuai.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Serahkan!'
            });

            if (confirmResult.isConfirmed) {

                // 1. Tampilkan animasi loading agar tidak dikira "nge-hang"
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menyimpan data ke server',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Ambil token CSRF
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                if (!csrfToken) {
                    Swal.fire('Error', 'Meta tag CSRF-TOKEN tidak ditemukan di header HTML!', 'error');
                    return;
                }

                try {
                    const response = await fetch(`/mitra/scan/konfirmasi/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    // 2. Cek apakah server merespon dengan error HTTP (404, 500, 419, dll)
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error("Server Response Error:", errorText);
                        throw new Error(`Terjadi kesalahan di server. Status: ${response.status}`);
                    }

                    const data = await response.json();

                    // 3. Handle respon sukses dari controller
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Tampilkan Faktur Langsung
                            showInvoice(data.transaksi);
                        });
                    } else {
                        // Handle jika controller membalas dengan format status error
                        Swal.fire('Gagal!', data.message || 'Gagal memproses penyerahan.', 'warning');
                    }

                } catch (error) {
                    // 4. Tangkap error syntax / jaringan
                    console.error("Detail Error Catch:", error);
                    Swal.fire('Error Sistem', error.message + '<br>Coba cek tab Console (F12) untuk detailnya.',
                        'error');
                }
            }
        }

        function resetScanner() {
            document.getElementById('result-card').classList.add('hidden');
            document.getElementById('reader').classList.remove('hidden');
            document.getElementById('manual-input-container').classList.remove('hidden');
            document.getElementById('manual-tx-id').value = '';
            
            try {
                scannerApp.resume();
            } catch(e) {}
            
            isScanning = true;
        }

        async function processManualInput() {
            const inputId = document.getElementById('manual-tx-id').value.trim();
            if (!inputId) {
                Swal.fire('Peringatan', 'Silakan masukkan ID Transaksi terlebih dahulu!', 'warning');
                return;
            }
            
            isScanning = true;
            await onScanSuccess(inputId, null);
        }

        // Pastikan halaman HTML sudah di-load sepenuhnya sebelum menyalakan kamera
        document.addEventListener("DOMContentLoaded", function() {
            initScanner();
        });

        function showInvoice(t) {
            document.getElementById('inv-petani').innerText = t.petani.nama_petani;
            document.getElementById('inv-nik').innerText = 'NIK: ' + (t.petani.nik || '-');
            document.getElementById('inv-id').innerText = '#' + t.id_transaksi;
            
            const date = new Date(t.updated_at);
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('inv-tanggal').innerText = date.toLocaleDateString('id-ID', options).replace('.', ':') + ' WIB';
            
            let itemsHtml = '';
            t.rincian.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td class="px-4 py-3 text-gray-900 font-bold">${item.pupuk.nama_pupuk}</td>
                        <td class="px-4 py-3 text-gray-500">${new Intl.NumberFormat('id-ID').format(item.harga_satuan)}</td>
                        <td class="px-4 py-3 text-center text-gray-900 font-bold">${item.jumlah} Kg</td>
                        <td class="px-4 py-3 text-right text-gray-900 font-bold">${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                    </tr>
                `;
            });
            document.getElementById('inv-items').innerHTML = itemsHtml;
            document.getElementById('inv-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(t.total);
            document.getElementById('inv-qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${t.id_transaksi}`;
            
            // Set print button link
            document.getElementById('btn-cetak-inv').onclick = () => {
                window.open(`/mitra/cetak_transaksi/${t.id_transaksi}`, '_blank');
            };

            document.getElementById('invoice-modal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeInvoice() {
            document.getElementById('invoice-modal').classList.add('hidden');
            resetScanner();
        }
    </script>

    <!-- Modal Faktur -->
    <div id="invoice-modal" class="fixed inset-0 z-[999] flex items-center justify-center hidden bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in duration-300">
            <!-- Header Faktur -->
            <div class="p-6 pb-0 flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i data-lucide="leaf" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight uppercase leading-none">Faktur Pembelian</h2>
                        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider mt-1">Sistem Subsidi Pupuk Kab. Ciamis</p>
                    </div>
                </div>
                <button onclick="closeInvoice()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
                </button>
            </div>

            <div class="p-6 pt-8 space-y-6 overflow-y-auto max-h-[70vh]">
                <!-- Customer & Transaction ID -->
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Diterbitkan Untuk</p>
                        <h3 id="inv-petani" class="text-base font-bold text-gray-900 leading-tight">-</h3>
                        <p id="inv-nik" class="text-xs text-gray-500">NIK: -</p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID Transaksi</p>
                        <div id="inv-id" class="inline-block bg-gray-50 px-3 py-1.5 rounded-xl text-sm font-bold text-gray-900 border border-gray-100 tracking-tight">
                            #000
                        </div>
                    </div>
                </div>

                <!-- Mitra & Date -->
                <div class="flex justify-between items-start pt-2">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kios / Mitra Penyalur</p>
                        <h3 class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()->mitra->nama_mitra ?? '-' }}</h3>
                        <p class="text-[11px] text-gray-500">No: {{ Auth::user()->mitra->nomor_mitra ?? '-' }}</p>
                        <p class="text-[11px] text-gray-500 leading-tight max-w-[180px]">{{ Auth::user()->mitra->alamat_mitra ?? '-' }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tanggal Transaksi</p>
                        <h3 id="inv-tanggal" class="text-sm font-bold text-gray-900 leading-tight">-</h3>
                    </div>
                </div>

                <!-- Table -->
                <div class="space-y-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rincian Pembelian</p>
                    <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50/80 text-gray-500 font-bold">
                                <tr>
                                    <th class="px-4 py-3">Item</th>
                                    <th class="px-4 py-3">Harga</th>
                                    <th class="px-4 py-3 text-center">Qty</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="inv-items" class="divide-y divide-gray-100">
                                <!-- Items go here -->
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50/50">
                                    <td colspan="3" class="px-4 py-4 font-black text-gray-900 uppercase text-xs tracking-wider">Total Pembayaran</td>
                                    <td id="inv-total" class="px-4 py-4 text-right text-lg font-black text-[#8B5CF6]">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Status & Footer -->
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="bg-gray-50 rounded-2xl p-4 flex flex-col items-center justify-center border border-gray-100 text-center gap-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</p>
                        <div class="flex items-center gap-1.5 text-emerald-600 font-bold bg-emerald-50 px-3 py-1.5 rounded-lg text-xs">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Sudah Diambil
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4 flex flex-col items-center justify-center border border-gray-100 text-center gap-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Scan Pengambilan</p>
                        <div class="bg-white p-1.5 rounded-lg border border-gray-200">
                            <img id="inv-qr" src="" class="w-14 h-14" alt="QR">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Action -->
            <div class="p-6 bg-gray-50/80 border-t border-gray-100 flex gap-3">
                <button onclick="closeInvoice()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-colors text-sm">
                    Tutup
                </button>
                <button id="btn-cetak-inv" class="flex-[2] px-4 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-shadow shadow-lg shadow-green-200 flex items-center justify-center gap-2 text-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak Faktur
                </button>
            </div>
        </div>
    </div>
@endsection
