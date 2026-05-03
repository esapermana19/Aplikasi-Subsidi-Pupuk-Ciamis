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
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            // Reset tampilan
                            document.getElementById('result-card').classList.add('hidden');
                            document.getElementById('reader').classList.remove('hidden');
                            document.getElementById('manual-input-container').classList.remove('hidden');
                            document.getElementById('manual-tx-id').value = '';

                            // Lanjutkan scan
                            scannerApp.resume();
                            isScanning = true;
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
    </script>
@endsection
