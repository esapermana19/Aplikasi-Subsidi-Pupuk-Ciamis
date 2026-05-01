<head>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pupuk Tersedia</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Ketersediaan stok pupuk subsidi di kios Anda.
                </p>
            </div>
            <div>
                <button id="btnOpenModal"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Buat Permintaan Baru
                </button>
            </div>
        </div>

        {{-- Grid Card Stok Pupuk --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($pupukList as $pupuk)
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Bagian Atas / Gambar (Opsional jika ada img_pupuk ) --}}
                    <div class="h-32 bg-green-50 rounded-t-xl flex items-center justify-center overflow-hidden">
                        @if ($pupuk->img_pupuk)
                            {{-- Tampilkan Gambar dari folder storage --}}
                            <img src="{{ asset('storage/' . $pupuk->img_pupuk) }}" alt="{{ $pupuk->nama_pupuk }}"
                                class="w-full h-full object-cover">
                        @else
                            {{-- Fallback ke Icon Box jika img_pupuk tidak ada --}}
                            <div class="p-4 bg-white rounded-lg shadow-sm">
                                <i data-lucide="package" class="h-10 w-10 text-green-500"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Bagian Detail --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $pupuk->nama_pupuk }}</h3>

                            {{-- Status Indikator --}}
                            @if ($pupuk->stok_mitra > 50)
                                <span
                                    class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Aman</span>
                            @elseif ($pupuk->stok_mitra > 0)
                                <span
                                    class="bg-orange-100 text-orange-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Menipis</span>
                            @else
                                <span
                                    class="bg-red-100 text-red-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Habis</span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 mb-4">Harga Eceran: Rp
                            {{ number_format($pupuk->harga_subsidi, 0, ',', '.') }} / {{ $pupuk->satuan ?? 'Kg' }}</p>

                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-600">Sisa Stok</span>
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-2xl font-black {{ $pupuk->stok_mitra > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $pupuk->stok_mitra }}
                                </span>
                                <span class="text-xs font-bold text-gray-500">{{ $pupuk->satuan ?? 'Kg' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-200 border-dashed p-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 mb-3">
                        <i data-lucide="package-x" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Belum Ada Data Pupuk</h3>
                    <p class="mt-1 text-sm text-gray-500">Data master pupuk belum tersedia dari pusat.</p>
                </div>
            @endforelse
        </div>
    </div>
    {{-- Modal Permintaan --}}
    <div id="modalPermintaan" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">

        <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6 relative max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Form Permintaan Pupuk</h2>
                <button id="btnCloseModal" class="text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('mitra.store_permintaan') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Permintaan</label>
                    <input type="date" name="tgl_permintaan" required value="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Diminta (Kosongkan jika tidak
                        perlu)</label>
                    <div class="space-y-3 bg-gray-50 p-4 rounded border border-gray-200">

                        @foreach ($pupukList as $pupuk)
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-700">{{ $pupuk->nama_pupuk }}</span>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="pupuk[{{ $pupuk->id_pupuk }}]" min="0"
                                        placeholder="0"
                                        class="w-24 border border-gray-300 rounded px-2 py-1 text-right focus:outline-none focus:border-green-500">
                                    <span class="text-sm text-gray-500">Kg/Zak</span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Tuliskan pesan atau catatan untuk admin..."
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" id="btnCancelModal"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Script untuk Modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalPermintaan');
            const btnOpen = document.getElementById('btnOpenModal');
            const btnClose = document.getElementById('btnCloseModal');
            const btnCancel = document.getElementById('btnCancelModal');

            // Fungsi buka modal
            if (btnOpen) {
                btnOpen.addEventListener('click', function() {
                    modal.classList.remove('hidden');
                });
            }

            // Fungsi tutup modal
            function closeModal() {
                modal.classList.add('hidden');
            }

            if (btnClose) btnClose.addEventListener('click', closeModal);
            if (btnCancel) btnCancel.addEventListener('click', closeModal);

            // Tutup modal jika user klik area gelap di luar box putih
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
        });

        async function bayarSekarang() {
            // Kumpulkan data
            const payload = {
                id_mitra: selectedMitraId,
                total_pembayaran: keranjang.reduce((sum, item) => sum + item.subtotal, 0),
                keranjang: keranjang,
                _token: '{{ csrf_token() }}' // Penting untuk Laravel POST
            };

            try {
                // Tampilkan loading di tombol...

                // Kirim request ke backend
                const response = await fetch('/api/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                // Panggil Midtrans Snap Popup
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        alert("Pembayaran berhasil!");
                        goToStep(4); // Pindah ke halaman sukses
                    },
                    onPending: function(result) {
                        alert("Menunggu pembayaran Anda.");
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });

            } catch (error) {
                console.error('Checkout error:', error);
                alert('Gagal memproses pembayaran');
            }
        }
    </script>
@endsection
