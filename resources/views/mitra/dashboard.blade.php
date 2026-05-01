@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-green-700">Hallo!! {{ Auth::user()->mitra->nama_mitra ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali! Pantau dan kelola stok pupuk di kios Anda.</p>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Top Cards --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1: Saldo Aktif --}}
            <div class="bg-gradient-to-br from-green-600 to-green-500 text-white rounded-xl shadow-md p-5 border-0">
                <div class="flex flex-row items-center justify-between pb-2">
                    <h3 class="text-sm font-medium text-white/90">Total Saldo Aktif</h3>
                    <i data-lucide="wallet" class="h-4 w-4 text-white/90"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">Rp {{ number_format($stats['saldo_aktif'], 0, ',', '.') }}</div>
                    <p class="text-xs text-white/80 mt-1">Siap ditarik</p>
                </div>
            </div>

            {{-- Card 2: Saldo Tertunda --}}
            <div class="bg-gradient-to-br from-purple-200 to-purple-300 text-purple-900 rounded-xl shadow-md p-5 border-0">
                <div class="flex flex-row items-center justify-between pb-2">
                    <h3 class="text-sm font-medium text-purple-900/90">Saldo Tertunda</h3>
                    <i data-lucide="clock" class="h-4 w-4 text-purple-900/90"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">Rp {{ number_format($stats['saldo_tertunda'], 0, ',', '.') }}</div>
                    <p class="text-xs text-purple-900/80 mt-1">Menunggu pencairan admin</p>
                </div>
            </div>

            {{-- Card 3: Stok Pupuk --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex flex-row items-center justify-between pb-2">
                    <h3 class="text-sm font-medium text-gray-700">Total Stok Kios</h3>
                    <i data-lucide="package-check" class="h-4 w-4 text-gray-400"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['stok_pupuk'], 0, ',', '.') }}
                        <span class="text-base font-normal text-gray-500">Kg</span>
                    </div>
                    {{-- Ubah teks keterangan di bawah ini --}}
                    <p class="text-xs text-gray-500 mt-1">Sisa ketersediaan di kios Anda</p>
                </div>
            </div>

            {{-- Card 4: Transaksi Bulan Ini --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex flex-row items-center justify-between pb-2">
                    <h3 class="text-sm font-medium text-gray-700">Transaksi Bulan Ini</h3>
                    <i data-lucide="trending-up" class="h-4 w-4 text-gray-400"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['transaksi_bulan_ini'] }}</div>
                    <p class="text-xs text-green-500 font-medium mt-1">Transaksi Berhasil</p>
                </div>
            </div>
        </div>

        {{-- Main Content Split --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">

            {{-- Kiri: Aktivitas Terkini --}}
            <div class="col-span-4 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Aktivitas Terkini</h3>
                    <p class="text-sm text-gray-500">Transaksi pengambilan pupuk oleh petani</p>
                </div>

                <div class="space-y-4">
                    @forelse($recentTransactions as $t)
                        <div class="flex items-center gap-4 border-b border-gray-50 pb-3 last:border-0">
                            {{-- Inisial Nama --}}
                            <div
                                class="h-10 w-10 rounded-full bg-green-50 flex items-center justify-center text-green-700 font-bold uppercase">
                                {{ substr($t->petani->nama_petani ?? 'P', 0, 1) }}
                            </div>
                            <div class="flex-1 space-y-1">
                                <p class="text-sm font-medium leading-none text-gray-900">
                                    {{ $t->petani->nama_petani ?? 'Petani Tidak Diketahui' }}</p>
                                <p class="text-xs text-gray-500">Pengambilan {{ $t->jumlah }}kg
                                    {{ $t->pupuk->nama_pupuk ?? 'Pupuk' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">+Rp {{ number_format($t->total, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-gray-400">{{ $t->created_at ? $t->created_at->format('d M, H:i') : \Carbon\Carbon::parse($t->tgl_transaksi)->format('d M, H:i') }} WIB</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-sm">
                            Belum ada transaksi bulan ini.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('mitra.transaksi') }}"
                    class="mt-4 w-full block text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium py-2 rounded-lg border border-gray-200 transition-colors text-sm">
                    Lihat Semua Transaksi
                </a>
            </div>

            {{-- Kanan: Aksi Cepat --}}
            <div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-t-4 border-t-purple-500">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
                    <p class="text-sm text-gray-500">Pintasan fungsi utama mitra</p>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('mitra.scan') }}"
                        class="w-full flex items-center justify-center gap-3 bg-green-600 hover:bg-green-700 text-white h-16 rounded-xl font-bold transition-colors shadow-sm">
                        <i data-lucide="qr-code" class="h-6 w-6"></i>
                        Scan Barcode Petani
                    </a>

                    <a href="{{ route('mitra.transaksi') }}"
                        class="w-full flex items-center justify-center gap-2 border border-purple-200 text-purple-700 hover:bg-purple-50 h-12 rounded-xl font-medium transition-colors">
                        <i data-lucide="package-plus" class="h-5 w-5"></i>
                        Input Transaksi Manual
                    </a>

                    <a href="{{ route('mitra.tarik_saldo') }}"
                        class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-700 hover:bg-gray-50 h-12 rounded-xl font-medium transition-colors">
                        <i data-lucide="wallet" class="h-5 w-5"></i>
                        Tarik Saldo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Memastikan icon lucide di-render di halaman ini
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
@endsection
