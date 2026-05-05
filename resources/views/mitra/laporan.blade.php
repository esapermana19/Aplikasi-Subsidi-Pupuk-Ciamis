@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 md:space-y-6 pb-12 px-2 md:px-0">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Laporan Penjualan</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">Analisis performa penjualan kios Anda.</p>
        </div>
        
        <form action="{{ route('mitra.laporan') }}" method="GET" class="flex items-center gap-2 bg-white p-1.5 md:p-2 rounded-xl shadow-sm border border-gray-100 w-full md:w-auto">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                </div>
                <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
                    class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border-none rounded-lg focus:ring-2 focus:ring-green-500 transition-all font-medium text-gray-700">
            </div>
            @if(request('periode'))
            <a href="{{ route('mitra.laporan') }}" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Reset Filter">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
            </a>
            @endif
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="p-1.5 md:p-2 bg-green-50 rounded-lg">
                    <i data-lucide="banknote" class="h-4 w-4 md:h-5 md:w-5 text-green-600"></i>
                </div>
                <span class="hidden sm:inline-block text-[9px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full uppercase">Omzet</span>
            </div>
            <p class="text-base md:text-2xl font-bold text-gray-900 truncate">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 mt-1">Total pendapatan</p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="p-1.5 md:p-2 bg-blue-50 rounded-lg">
                    <i data-lucide="package" class="h-4 w-4 md:h-5 md:w-5 text-blue-600"></i>
                </div>
                <span class="hidden sm:inline-block text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full uppercase">Volume</span>
            </div>
            <p class="text-base md:text-2xl font-bold text-gray-900 truncate">{{ number_format($totalVolume, 0, ',', '.') }} <span class="text-[10px] md:text-sm font-normal text-gray-500">Kg</span></p>
            <p class="text-[10px] md:text-xs text-gray-500 mt-1">Terdistribusi</p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="p-1.5 md:p-2 bg-purple-50 rounded-lg">
                    <i data-lucide="receipt" class="h-4 w-4 md:h-5 md:w-5 text-purple-600"></i>
                </div>
                <span class="hidden sm:inline-block text-[9px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full uppercase">Sales</span>
            </div>
            <p class="text-base md:text-2xl font-bold text-gray-900">{{ $totalTransaksi }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 mt-1">Total transaksi</p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="p-1.5 md:p-2 bg-orange-50 rounded-lg">
                    <i data-lucide="users" class="h-4 w-4 md:h-5 md:w-5 text-orange-600"></i>
                </div>
                <span class="hidden sm:inline-block text-[9px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full uppercase">Petani</span>
            </div>
            <p class="text-base md:text-2xl font-bold text-gray-900">{{ $petaniUnik }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 mt-1">Jumlah pelanggan</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        {{-- Line Chart: Trend Penjualan Harian --}}
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm md:text-base font-bold text-gray-900">Tren Penjualan Harian</h3>
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 md:h-3 md:w-3 bg-green-500 rounded-full"></div>
                    <span class="text-[10px] md:text-xs text-gray-500 font-medium">Nominal (Rp)</span>
                </div>
            </div>
            <div id="salesChart" class="min-h-[250px] md:min-h-[350px]"></div>
        </div>

        {{-- Pie Chart: Distribusi Pupuk --}}
        <div class="bg-white p-4 md:p-6 rounded-2xl md:rounded-3xl shadow-sm border border-gray-100">
            <h3 class="text-sm md:text-base font-bold text-gray-900 mb-6">Distribusi Pupuk</h3>
            <div id="pupukPieChart" class="min-h-[250px] md:min-h-[300px] flex items-center justify-center"></div>
            @if(empty($pieSeries))
                <div class="text-center py-8 md:py-12">
                    <i data-lucide="package-search" class="h-10 w-10 md:h-12 md:w-12 text-gray-200 mx-auto mb-3"></i>
                    <p class="text-xs md:text-sm text-gray-400">Belum ada data distribusi</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Transactions Table --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 md:px-6 md:py-5 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-sm md:text-base font-bold text-gray-900">10 Transaksi Terakhir</h3>
            <a href="{{ route('mitra.riwayat') }}" class="text-[10px] md:text-xs font-bold text-green-600 hover:text-green-700 transition-colors bg-green-50 px-3 py-1.5 rounded-lg">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[600px] md:min-w-full">
                <thead class="bg-gray-50/50 text-gray-500 font-medium uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Nama Petani</th>
                        <th class="px-6 py-4 text-right">Total Nominal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentTransactions as $tr)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-900 font-medium">{{ $tr->created_at->format('d M Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $tr->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $tr->petani->nama_petani ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">ID: {{ $tr->id_transaksi }}</p>
                        </td>
                        <td class="px-6 py-4 font-bold text-green-600 text-right text-sm">Rp {{ number_format($tr->total, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-0.5 bg-green-50 text-green-600 rounded-md text-[9px] font-bold uppercase tracking-tight border border-green-100">Selesai</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            Tidak ada transaksi di periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart Settings
        const salesOptions = {
            series: [{
                name: 'Pendapatan (Rp)',
                data: @json($chartData)
            }],
            chart: {
                height: window.innerWidth < 768 ? 250 : 350,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, sans-serif',
                sparkline: { enabled: window.innerWidth < 640 }
            },
            colors: ['#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: window.innerWidth < 768 ? 2 : 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: { left: 0, right: 0 }
            },
            xaxis: {
                categories: @json($chartLabels),
                title: { text: window.innerWidth < 768 ? '' : 'Tanggal', style: { color: '#94a3b8', fontWeight: 500 } },
                labels: { 
                    style: { colors: '#64748b', fontSize: '10px' },
                    rotate: -45,
                    hideOverlappingLabels: true
                },
                tickAmount: window.innerWidth < 768 ? 10 : undefined
            },
            yaxis: {
                show: window.innerWidth >= 640,
                labels: {
                    formatter: (val) => 'Rp ' + (val >= 1000000 ? (val/1000000).toFixed(1) + 'M' : (val/1000).toFixed(0) + 'K'),
                    style: { colors: '#64748b', fontSize: '10px' }
                }
            },
            tooltip: {
                y: { formatter: (val) => 'Rp ' + val.toLocaleString('id-ID') }
            }
        };

        const salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
        salesChart.render();

        // Pie Chart Settings
        const pieSeries = @json($pieSeries);
        if(pieSeries.length > 0) {
            const pieOptions = {
                series: pieSeries,
                labels: @json($pieLabels),
                chart: {
                    type: 'donut',
                    height: window.innerWidth < 768 ? 280 : 350,
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                legend: { 
                    position: 'bottom',
                    fontSize: '11px',
                    markers: { radius: 12 }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '12px',
                                    formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' Kg'
                                },
                                value: { fontSize: '16px', fontWeight: 700 }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false }
            };

            const pupukPieChart = new ApexCharts(document.querySelector("#pupukPieChart"), pieOptions);
            pupukPieChart.render();
        }
    });
</script>
@endsection

