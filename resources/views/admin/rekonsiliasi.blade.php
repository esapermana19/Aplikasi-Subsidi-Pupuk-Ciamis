@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekonsiliasi Data Saldo</h1>
            <p class="text-sm text-gray-500 mt-1">Pengecekan kesesuaian antara riwayat transaksi dengan saldo aplikasi Mitra.</p>
        </div>

        <form action="{{ route('admin.rekonsiliasi') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="month" name="periode" value="{{ $bulanTahun }}" 
                class="bg-white border border-gray-200 text-sm rounded-lg focus:ring-violet-500 px-4 py-2.5">
            
            <select name="kecamatan" class="bg-white border border-gray-200 text-sm rounded-lg focus:ring-violet-500 px-4 py-2.5">
                <option value="">Semua Kecamatan</option>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id_kecamatan }}" {{ request('kecamatan') == $kecamatan->id_kecamatan ? 'selected' : '' }}>
                        {{ $kecamatan->nama_kecamatan }}
                    </option>
                @endforeach
            </select>

            <select name="desa" class="bg-white border border-gray-200 text-sm rounded-lg focus:ring-violet-500 px-4 py-2.5">
                <option value="">Semua Desa</option>
                @if(request('kecamatan'))
                    @foreach($desas as $desa)
                        @if($desa->id_kecamatan == request('kecamatan'))
                            <option value="{{ $desa->id_desa }}" {{ request('desa') == $desa->id_desa ? 'selected' : '' }}>
                                {{ $desa->nama_desa }}
                            </option>
                        @endif
                    @endforeach
                @endif
            </select>

            <div class="relative w-48">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:ring-violet-500"
                    placeholder="Cari Nama Mitra...">
            </div>

            <select name="status" class="bg-white border border-gray-200 text-sm rounded-lg focus:ring-violet-500 px-4 py-2.5">
                <option value="">Semua Status</option>
                <option value="match" {{ request('status') == 'match' ? 'selected' : '' }}>Match</option>
                <option value="mismatch" {{ request('status') == 'mismatch' ? 'selected' : '' }}>Mismatch</option>
            </select>

            <button type="submit" class="px-5 py-2.5 bg-emerald-500 text-white text-sm font-bold rounded-lg hover:bg-emerald-600 flex items-center gap-2">
                <i data-lucide="filter" class="h-4 w-4"></i> Filter
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
        <div class="absolute top-6 right-6">
            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-md uppercase">Review</span>
        </div>
        
        <h2 class="text-lg font-bold text-gray-900">Rekonsiliasi Periode: {{ \Carbon\Carbon::parse($bulanTahun)->translatedFormat('F Y') }}</h2>
        <p class="text-[11px] text-gray-400 font-medium mt-1">Terakhir diupdate: {{ now()->format('d M Y, H:i') }} WIB</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6 pt-6 border-t border-gray-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <i data-lucide="database" class="h-5 w-5 text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Total Transaksi</p>
                    <p class="text-xl font-black text-gray-900 mt-0.5">{{ $totalTransaksiCount }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="h-5 w-5 text-blue-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Total Nominal</p>
                    <p class="text-xl font-black text-gray-900 mt-0.5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Data Sesuai</p>
                    <p class="text-xl font-black text-green-600 mt-0.5">{{ $dataSesuai }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-orange-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Data Selisih</p>
                    <p class="text-xl font-black text-orange-500 mt-0.5">{{ $dataSelisih }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Nama Kios / Mitra</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Transaksi Masuk</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Penarikan (Berhasil)</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-blue-500 uppercase tracking-wider bg-blue-50/30">Saldo Hitungan Sistem</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-purple-500 uppercase tracking-wider bg-purple-50/30">Saldo App (Saat Ini)</th>
                        <th class="px-6 py-4 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status & Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekonsiliasiList as $rekonsiliasi)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $rekonsiliasi['mitra']->nama_mitra }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Pemilik: {{ $rekonsiliasi['mitra']->nama_pemilik }}</p>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-600">
                                Rp {{ number_format($rekonsiliasi['total_masuk'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-red-500">
                                - Rp {{ number_format($rekonsiliasi['total_penarikan'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-blue-600 bg-blue-50/30">
                                Rp {{ number_format($rekonsiliasi['saldo_hitungan'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-purple-600 bg-purple-50/30">
                                Rp {{ number_format($rekonsiliasi['saldo_app'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($rekonsiliasi['is_match'])
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100">
                                        <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-emerald-500"></i>
                                        <span class="text-xs font-bold text-emerald-600">Match</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 border border-orange-100">
                                        <i data-lucide="alert-triangle" class="h-3.5 w-3.5 text-orange-500"></i>
                                        <span class="text-xs font-bold text-orange-600">Mismatch</span>
                                    </div>
                                    @php
                                        $selisih = abs($rekonsiliasi['saldo_hitungan'] - $rekonsiliasi['saldo_app']);
                                    @endphp
                                    <div class="text-[10px] font-bold text-orange-500 mt-1">Selisih: Rp {{ number_format($selisih, 0, ',', '.') }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 mb-3">
                                    <i data-lucide="search-x" class="h-6 w-6 text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Tidak ada data mitra yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $mitras->links() }}
        </div>
    </div>
</div>
@endsection
