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
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {{-- Card 1: Saldo Aktif --}}
            <div onclick="window.location.href='{{ route('mitra.saldo') }}'" class="bg-gradient-to-br from-green-600 to-green-500 text-white rounded-xl shadow-md p-5 border-0 hover:from-green-500 hover:to-green-400 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
                <div class="flex flex-row items-center justify-between pb-2">
                    <h3 class="text-sm font-medium text-white/90">Total Saldo Aktif</h3>
                    <i data-lucide="wallet" class="h-4 w-4 text-white/90"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">Rp {{ number_format($stats['saldo_aktif'], 0, ',', '.') }}</div>
                    <p class="text-xs text-white/80 mt-1">Siap ditarik</p>
                </div>
            </div>

            {{-- Card 3: Stok Pupuk --}}
            <div onclick="window.location.href='{{ route('mitra.pupuk_tersedia') }}'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-green-300 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
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
            <div onclick="window.location.href='{{ route('mitra.riwayat') }}'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-green-300 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
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
                    <p class="text-sm text-gray-500">Ringkasan transaksi, permintaan pupuk, dan penarikan saldo</p>
                </div>

                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-center gap-4 border-b border-gray-50 pb-3 last:border-0 hover:bg-gray-50/50 p-2 rounded-lg transition-colors">
                            {{-- Ikon Aktivitas --}}
                            <div class="h-10 w-10 rounded-full {{ $activity['icon_bg'] }} flex items-center justify-center">
                                <i data-lucide="{{ $activity['icon'] }}" class="h-5 w-5 {{ $activity['icon_color'] }}"></i>
                            </div>
                            <div class="flex-1 space-y-1">
                                <p class="text-sm font-medium leading-none text-gray-900">
                                    {{ $activity['title'] }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $activity['subtitle'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $activity['amount_color'] }}">
                                    {{ $activity['amount'] }}
                                </p>
                                <p class="text-[10px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($activity['date'])->format('d M, H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-sm">
                            <i data-lucide="inbox" class="h-10 w-10 mx-auto text-gray-300 mb-2"></i>
                            Belum ada aktivitas.
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- Kanan: Aksi Cepat & Chart --}}
            <div class="col-span-3 flex flex-col gap-4 h-full">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-t-4 border-t-purple-500">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
                        <p class="text-sm text-gray-500">Pintasan fungsi utama mitra</p>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('mitra.scan') }}"
                            class="w-full flex items-center justify-center gap-3 bg-green-600 hover:bg-green-500 text-white h-16 rounded-xl font-bold hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <i data-lucide="qr-code" class="h-6 w-6"></i>
                            Scan Barcode 
                        </a>
                    </div>
                </div>

                {{-- Chart Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex-1 flex flex-col">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Grafik Transaksi</h3>
                        <p class="text-sm text-gray-500">Jumlah transaksi 7 hari terakhir</p>
                    </div>
                    <div class="relative w-full flex-1 min-h-[200px]">
                        <canvas id="transaksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Memastikan icon lucide di-render di halaman ini
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Render Chart.js
        const ctx = document.getElementById('transaksiChart').getContext('2d');
        
        // Gradient for chart area
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(22, 163, 74, 0.2)'); // green-600 with opacity
        gradient.addColorStop(1, 'rgba(22, 163, 74, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Transaksi',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#16a34a', // green-600
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#16a34a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937', // gray-800
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 11, family: "'Inter', sans-serif" }
                        },
                        grid: {
                            color: '#f3f4f6', // gray-100
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11, family: "'Inter', sans-serif" }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    </script>
@endsection
