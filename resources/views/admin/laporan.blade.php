@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
            <p class="text-sm text-gray-500 mt-1">Analisis dan laporan sistem subsidi pupuk</p>
        </div>
        <div class="flex items-center gap-2">
            <p class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Filter & Search Section --}}
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
        <form action="{{ route('admin.laporan') }}" method="GET" id="filterForm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filter Bulan --}}
                    <div class="relative flex-1 min-w-[140px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 pr-3 py-2.5 appearance-none cursor-pointer">
                    </div>

                    {{-- Filter Kecamatan --}}
                    <div class="relative flex-1 min-w-[140px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="map" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <select name="kecamatan" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
                            <option value="">Semua Kecamatan</option>
                            @foreach($kecamatans as $kecamatan)
                                <option value="{{ $kecamatan->id_kecamatan }}" {{ request('kecamatan') == $kecamatan->id_kecamatan ? 'selected' : '' }}>{{ $kecamatan->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Desa --}}
                    <div class="relative flex-1 min-w-[140px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="map-pin" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <select name="desa" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
                            <option value="">Semua Desa</option>
                            @if(request('kecamatan'))
                                @foreach($desas as $desa)
                                    @if($desa->id_kecamatan == request('kecamatan'))
                                        <option value="{{ $desa->id_desa }}" {{ request('desa') == $desa->id_desa ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Filter Mitra --}}
                    <div class="relative flex-1 min-w-[140px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="store" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <select name="mitra" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
                            <option value="">Semua Kios/Mitra</option>
                            @foreach($mitras as $mitra)
                                @php
                                    $showMitra = true;
                                    if(request('kecamatan') && $mitra->id_kecamatan != request('kecamatan')) $showMitra = false;
                                    if(request('desa') && $mitra->id_desa != request('desa')) $showMitra = false;
                                @endphp
                                @if($showMitra)
                                    <option value="{{ $mitra->id_mitra }}" {{ request('mitra') == $mitra->id_mitra ? 'selected' : '' }}>{{ $mitra->nama_mitra }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(request()->hasAny(['kecamatan', 'desa', 'mitra', 'periode']) && (request('kecamatan') != '' || request('desa') != '' || request('mitra') != ''))
                    <a href="{{ route('admin.laporan') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                        <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1 --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Total Subsidi</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    Rp {{ number_format($totalSubsidi / 1000000, 1) }} Jt
                </h3>
                <p class="text-[10px] font-medium text-emerald-600 mt-2">+8.2% dari bulan lalu</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="trending-up" class="h-5 w-5 text-emerald-500"></i>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Total Penerima</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    {{ number_format($totalPenerima) }}
                </h3>
                <p class="text-[10px] font-medium text-emerald-600 mt-2">+12.5% dari bulan lalu</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i data-lucide="file-text" class="h-5 w-5 text-purple-500"></i>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Pupuk Tersalur</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    {{ number_format($pupukTersalurTon, 1) }} Ton
                </h3>
                <p class="text-[10px] font-medium text-emerald-600 mt-2">+6.8% dari bulan lalu</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="calendar" class="h-5 w-5 text-emerald-500"></i>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Mitra Aktif</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    {{ number_format($mitraAktif) }}
                </h3>
                <p class="text-[10px] font-medium text-gray-400 mt-2">Tidak ada perubahan</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i data-lucide="file-text" class="h-5 w-5 text-purple-500"></i>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Bar Chart --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Trend Penyaluran Pupuk (6 Bulan Terakhir)</h3>
            <div id="barChart" class="w-full h-72"></div>
        </div>

        {{-- Pie Chart 1 --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Distribusi Jenis Pupuk</h3>
            <div class="flex items-center justify-center h-72">
                <div id="pieChart" class="w-full max-w-[350px]"></div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Pie Chart 2 (Pemerataan Kecamatan) --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-1">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Pemerataan Penerima per Kecamatan</h3>
            <div class="flex items-center justify-center h-72">
                <div id="kecamatanPieChart" class="w-full max-w-[350px]"></div>
            </div>
        </div>
        
        {{-- Laporan Tersedia (Dipindah ke samping Pie Chart 2 agar rapi) --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-2">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Laporan Tersedia</h3>
            <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-colors">
                <i data-lucide="file-text" class="h-4 w-4"></i> Buat Laporan Baru
            </button>
        </div>
        
        <div class="p-6 space-y-4">
            {{-- Laporan Item 1 --}}
            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="h-5 w-5 text-emerald-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Laporan Penyaluran Pupuk Februari 2026</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">1/3/2026 &bull; Bulanan &bull; 2.4 MB</p>
                    </div>
                </div>
                <button class="flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-white bg-gray-50 transition-colors">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download
                </button>
            </div>

            {{-- Laporan Item 2 --}}
            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="h-5 w-5 text-emerald-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Laporan Transaksi Per Mitra Februari 2026</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">1/3/2026 &bull; Bulanan &bull; 1.8 MB</p>
                    </div>
                </div>
                <button class="flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-white bg-gray-50 transition-colors">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download
                </button>
            </div>

            {{-- Laporan Item 3 --}}
            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="h-5 w-5 text-emerald-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Laporan Rekapitulasi Subsidi Q1 2026</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">28/2/2026 &bull; Triwulan &bull; 3.2 MB</p>
                    </div>
                </div>
                <button class="flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-white bg-gray-50 transition-colors">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download
                </button>
            </div>

            {{-- Laporan Item 4 --}}
            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="h-5 w-5 text-emerald-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Laporan Data Petani Aktif Januari 2026</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">1/2/2026 &bull; Bulanan &bull; 1.5 MB</p>
                    </div>
                </div>
                <button class="flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-white bg-gray-50 transition-colors">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Data dari Controller
        const trendBulan = {!! $trendBulan !!};
        const trendData = {!! $trendData !!};
        const pieLabels = {!! $pieLabels !!};
        const pieSeries = {!! $pieSeries !!};
        const kecamatanLabels = {!! $kecamatanLabels !!};
        const kecamatanSeries = {!! $kecamatanSeries !!};

        // Konfigurasi Bar Chart (Trend Penyaluran)
        const barOptions = {
            series: [{
                name: 'Total (Ton)',
                data: trendData
            }],
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            colors: ['#10b981'],
            dataLabels: { enabled: false },
            stroke: { width: 2, colors: ['transparent'] },
            xaxis: {
                categories: trendBulan,
                axisBorder: { show: true, color: '#e5e7eb' },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#6b7280', fontSize: '12px' }
                }
            },
            yaxis: {
                tickAmount: 4,
                labels: {
                    style: { colors: '#6b7280', fontSize: '12px' },
                    formatter: function (val) {
                        return val.toFixed(0);
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } }
            },
            fill: { opacity: 1 },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Ton"
                    }
                }
            },
            legend: {
                position: 'bottom',
                markers: { radius: 2 }
            }
        };

        const barChart = new ApexCharts(document.querySelector("#barChart"), barOptions);
        barChart.render();

        // Konfigurasi Pie Chart (Distribusi Pupuk)
        const pieOptions = {
            series: pieSeries,
            labels: pieLabels,
            chart: {
                type: 'pie',
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10b981', '#c4b5fd', '#60a5fa', '#fbbf24', '#f87171'],
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return opts.w.globals.labels[opts.seriesIndex] + ": " + val.toFixed(0) + "%";
                },
                style: {
                    fontSize: '11px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 600,
                    colors: ['#10b981', '#c4b5fd', '#60a5fa', '#fbbf24', '#f87171']
                },
                background: {
                    enabled: false
                },
                dropShadow: {
                    enabled: false
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -20,
                        minAngleToShowLabel: 10
                    }
                }
            },
            stroke: {
                width: 2,
                colors: ['#ffffff']
            },
            legend: {
                show: false // Menyembunyikan legend default karena label sudah di luar pie
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + "%";
                    }
                }
            }
        };

        // Agar data labels ada di luar persis seperti screenshot
        // Sedikit penyesuaian untuk ApexCharts (secara default pie chart sulit melempar label jauh keluar)
        // Jika tidak sempurna di luar, warnanya sudah diatur sesuai request
        const pieChart = new ApexCharts(document.querySelector("#pieChart"), pieOptions);
        pieChart.render();

        // Konfigurasi Pie Chart 2 (Pemerataan Kecamatan)
        const kecamatanOptions = {
            series: kecamatanSeries,
            labels: kecamatanLabels,
            chart: {
                type: 'donut', // Supaya beda sedikit (donut chart)
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#0284c7', '#0ea5e9', '#38bdf8', '#7dd3fc', '#bae6fd', '#e0f2fe', '#8b5cf6', '#a855f7'],
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return val.toFixed(1) + "%";
                },
                style: {
                    fontSize: '10px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 600,
                },
                dropShadow: { enabled: false }
            },
            plotOptions: {
                pie: {
                    donut: { size: '65%' }
                }
            },
            stroke: { width: 2, colors: ['#ffffff'] },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '11px',
                markers: { radius: 2 }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " Petani";
                    }
                }
            }
        };

        const kecamatanChart = new ApexCharts(document.querySelector("#kecamatanPieChart"), kecamatanOptions);
        kecamatanChart.render();
    });
</script>
@endsection
