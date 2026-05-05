@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Total Petani --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mb-3">
                    <i data-lucide="users" class="h-4 w-4 text-indigo-500"></i>
                </div>
                <p class="text-[11px] font-medium text-gray-400 uppercase">Total Petani</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($total_petani) }}</h3>
            </div>
        </div>

        {{-- Card 2: Total Mitra --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center mb-3">
                    <i data-lucide="store" class="h-4 w-4 text-purple-500"></i>
                </div>
                <p class="text-[11px] font-medium text-gray-400 uppercase">Total Mitra</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($total_mitra) }}</h3>
            </div>
        </div>

        {{-- Card 3: Transaksi Berhasil --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center mb-3">
                    <i data-lucide="shopping-cart" class="h-4 w-4 text-emerald-500"></i>
                </div>
                <p class="text-[11px] font-medium text-gray-400 uppercase">Transaksi Berhasil</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($transaksi_berhasil) }}</h3>
            </div>
        </div>

        {{-- Card 4: Pupuk Tersalur --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center mb-3">
                    <i data-lucide="package" class="h-4 w-4 text-orange-500"></i>
                </div>
                <p class="text-[11px] font-medium text-gray-400 uppercase">Pupuk Tersalur</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($pupuk_tersalur, 1) }} <span class="text-sm text-gray-500 font-medium">Ton</span></h3>
            </div>
        </div>
    </div>

    {{-- Chart & Activity Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Trend Chart --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 uppercase">Trend Penyaluran</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Data 7 hari terakhir</p>
                </div>
                <select class="bg-gray-50 border border-gray-100 text-xs font-bold rounded-lg px-3 py-1.5 focus:ring-indigo-500">
                    <option>Minggu Ini</option>
                </select>
            </div>
            <div id="trendAreaChart" class="w-full h-72"></div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-1 flex flex-col">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800 uppercase">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.transaksi') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
            </div>
            <div class="p-6 flex-1 overflow-y-auto max-h-[300px]">
                <div class="space-y-6">
                    @forelse($recentTransactions as $trx)
                        <div class="flex items-start justify-between relative">
                            {{-- Connector Line --}}
                            @if(!$loop->last)
                            <div class="absolute top-6 bottom-[-24px] left-3.5 w-[2px] bg-gray-100"></div>
                            @endif

                            <div class="flex items-start gap-4 z-10 relative bg-white">
                                <div class="w-7 h-7 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="check" class="h-3.5 w-3.5 text-emerald-500"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">{{ $trx->petani->nama_petani ?? 'Anonim' }}</h4>
                                    <p class="text-[10px] text-gray-500 mt-0.5">Membeli di {{ $trx->mitra->nama_mitra ?? 'Unknown' }}</p>
                                    <p class="text-[9px] text-gray-400 mt-1">{{ $trx->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-xs font-bold text-gray-900 mt-1">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-xs text-gray-400 font-medium">Belum ada aktivitas transaksi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Akun Pending Verifikasi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-2 flex flex-col">
            <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-800 uppercase">Menunggu Verifikasi Akun</h3>
                <a href="{{ route('admin.verifikasi') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap / Kios</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tipe Akun</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Waktu Daftar</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($akunPending as $akun)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-900">
                                    {{ $akun->role == 'Petani' ? ($akun->petani->nama_petani ?? '-') : ($akun->mitra->nama_mitra ?? '-') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold {{ $akun->role == 'Petani' ? 'bg-indigo-50 text-indigo-600' : 'bg-purple-50 text-purple-600' }}">
                                        {{ $akun->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $akun->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 text-center">{{ $akun->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('admin.verifikasi') }}" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors px-2.5 py-1.5 rounded-md">Tinjau</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 mb-3">
                                        <i data-lucide="check" class="h-5 w-5 text-emerald-500"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 font-medium">Semua pendaftaran akun telah diverifikasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Distribusi Jenis Pupuk --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-1">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Distribusi Jenis Pupuk</h3>
            <div class="flex items-center justify-center h-64">
                <div id="donutChart" class="w-full"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Trend Penyaluran (Area Chart)
        const trendDates = {!! $trendDates !!};
        const trendData = {!! $trendData !!};

        const areaOptions = {
            series: [{
                name: 'Penyaluran (Ton)',
                data: trendData
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#6366f1'], // Indigo color exactly like screenshot
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                colors: ['#fff'],
                strokeColors: '#6366f1',
                strokeWidth: 2,
                hover: { size: 6 }
            },
            xaxis: {
                categories: trendDates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 },
                    formatter: function (val) {
                        return val.toFixed(1);
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: true } }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Ton"
                    }
                }
            }
        };

        const areaChart = new ApexCharts(document.querySelector("#trendAreaChart"), areaOptions);
        areaChart.render();

        // Distribusi Jenis Pupuk (Donut Chart)
        const pieLabels = {!! $pieLabels !!};
        const pieSeries = {!! $pieSeries !!};

        const donutOptions = {
            series: pieSeries.length > 0 && pieSeries.reduce((a,b) => a+b, 0) > 0 ? pieSeries : [1], // fallback shape if empty
            labels: pieLabels.length > 0 ? pieLabels : ['Data Kosong'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#4f46e5', '#818cf8', '#a5b4fc', '#c7d2fe', '#e0e7ff'], // Indigo/Blue variants
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        background: 'transparent'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: false
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '11px',
                markers: { radius: 2 }
            },
            tooltip: {
                enabled: pieSeries.length > 0 && pieSeries.reduce((a,b) => a+b, 0) > 0,
                y: {
                    formatter: function(value) {
                        return value + " Kg";
                    }
                }
            }
        };

        const donutChart = new ApexCharts(document.querySelector("#donutChart"), donutOptions);
        donutChart.render();
    });
</script>
@endsection
