@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-green-700">Permintaan Penarikan Saldo</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan proses pengajuan penarikan saldo virtual dari Mitra.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.penarikan.export', request()->query()) }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-emerald-700 hover:shadow-emerald-100 transition-all active:scale-95">
                <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                Ekspor Excel
            </a>
            <div class="hidden md:block text-right">
                <p class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100 h-fit">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filter & Search Section --}}
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
        <form action="{{ route('admin.permintaan_penarikan') }}" method="GET" id="filterForm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filter Bulan --}}
                    <div class="relative flex-1 min-w-[140px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <input type="month" name="filter_bulan" value="{{ request('filter_bulan') }}" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block pl-10 pr-3 py-2.5 appearance-none cursor-pointer">
                    </div>

                    {{-- Filter Kecamatan --}}
                    <div class="relative flex-1 min-w-[140px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="map" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <select name="kecamatan" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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

                {{-- Search Bar --}}
                <div class="relative w-full lg:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-green-500 focus:border-green-500 text-sm transition-all"
                        placeholder="Cari ID, Nama Mitra..." onkeypress="if(event.keyCode == 13) this.form.submit();">
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    <p class="text-xs text-gray-500 font-medium">
                        Total <span class="text-gray-950 font-bold">{{ $penarikans->total() }}</span> Permintaan
                    </p>
                </div>

                @if(request()->hasAny(['kecamatan', 'desa', 'mitra', 'search', 'filter_bulan']) && (request('kecamatan') != '' || request('desa') != '' || request('mitra') != '' || request('search') != '' || request('filter_bulan') != ''))
                    <a href="{{ route('admin.permintaan_penarikan') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                        <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Permintaan</h3>
                <p class="text-sm text-gray-500">Menampilkan semua riwayat dan permintaan penarikan saldo terbaru.</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">ID Penarikan</th>
                        <th scope="col" class="px-6 py-4">Waktu Permintaan</th>
                        <th scope="col" class="px-6 py-4">Mitra & Kios</th>
                        <th scope="col" class="px-6 py-4">No Rekening</th>
                        <th scope="col" class="px-6 py-4">Nominal</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penarikans as $p)
                        <tr class="bg-white border-b hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-gray-900">
                                {{ $p->id_penarikan }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $p->mitra->nama_mitra ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $p->mitra->nama_pemilik ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm">
                                {{ $p->mitra->no_rek ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-red-600">
                                Rp {{ number_format($p->jml_transfer, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->status === 'success')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full border border-green-200">Berhasil</span>
                                @elseif($p->status === 'pending')
                                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-1 rounded-full border border-orange-200 shadow-sm animate-pulse">Pending</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full border border-red-200">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Tombol Setujui --}}
                                        <form action="{{ route('admin.penarikan.update', $p->id_penarikan) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui penarikan ini dan sudah mentransfer ke rekening Mitra?');">
                                            @csrf
                                            <input type="hidden" name="status" value="success">
                                            <button type="submit" class="p-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm" title="Setujui & Tandai Berhasil">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak --}}
                                        <form action="{{ route('admin.penarikan.update', $p->id_penarikan) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menolak penarikan ini? Saldo akan dikembalikan ke Mitra.');">
                                            @csrf
                                            <input type="hidden" name="status" value="failed">
                                            <button type="submit" class="p-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm" title="Tolak">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.penarikan.cetak', $p->id_penarikan) }}" target="_blank" class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Cetak Faktur">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-center gap-2 hidden">
                                        {{-- Selesai text hidden --}}
                                    </div>
                                @else
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="text-xs text-gray-400 italic">Selesai</span>
                                        <button type="button" onclick="openPrintPreview('{{ route('admin.penarikan.cetak', $p->id_penarikan) }}')" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Cetak Faktur">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="h-12 w-12 text-gray-300 mb-3"></i>
                                    <p class="font-medium text-gray-600">Belum ada permintaan penarikan</p>
                                    <p class="text-xs text-gray-400 mt-1">Data penarikan saldo mitra akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($penarikans->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $penarikans->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Print Preview --}}
<div id="printPreviewModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[95vh] relative">
        <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="file-text" class="h-4 w-4 text-indigo-600"></i> Preview Faktur
            </h3>
            <button onclick="closePrintPreview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        
        <div class="p-0 overflow-y-auto flex-1 flex justify-center bg-gray-50 border-b border-gray-100">
            <iframe id="printFrame" class="w-full h-[600px] bg-white border-0" src=""></iframe>
        </div>
        
        <div class="px-5 py-4 border-t border-gray-200 flex justify-end gap-3 bg-white">
            <button onclick="closePrintPreview()" class="px-4 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition-colors">Batal</button>
            <button onclick="executePrint()" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                <i data-lucide="printer" class="h-4 w-4"></i> Cetak Sekarang
            </button>
        </div>
    </div>
</div>

<script>
    function openPrintPreview(url) {
        const modal = document.getElementById('printPreviewModal');
        const frame = document.getElementById('printFrame');
        
        modal.classList.remove('hidden');
        frame.src = url;
    }

    function closePrintPreview() {
        const modal = document.getElementById('printPreviewModal');
        const frame = document.getElementById('printFrame');
        
        modal.classList.add('hidden');
        frame.src = '';
    }

    function executePrint() {
        const frame = document.getElementById('printFrame');
        if(frame.contentWindow) {
            frame.contentWindow.print();
        }
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endsection
