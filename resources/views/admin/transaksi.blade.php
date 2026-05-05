@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Transaksi</h1>
                <p class="text-sm text-gray-500 mt-1">Keseluruhan riwayat transaksi pembelian pupuk dari semua petani dan
                    mitra.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.transaksi.export', request()->query()) }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-emerald-700 hover:shadow-emerald-100 transition-all active:scale-95">
                    <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                    Ekspor Excel
                </a>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <form action="{{ route('admin.transaksi') }}" method="GET" id="filterForm">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Filter Bulan --}}
                        <div class="relative flex-1 min-w-[140px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <input type="month" name="filter_bulan" value="{{ request('filter_bulan') }}" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-3 py-2.5 appearance-none cursor-pointer">
                        </div>

                        {{-- Filter Kecamatan --}}
                        <div class="relative flex-1 min-w-[140px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="map" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <select name="kecamatan" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-violet-500 focus:border-violet-500 text-sm transition-all"
                            placeholder="Cari ID, Nama, atau NIK..." onkeypress="if(event.keyCode == 13) this.form.submit();">
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <p class="text-xs text-gray-500 font-medium">
                            Total <span class="text-gray-950 font-bold">{{ $transaksis->total() }}</span> Transaksi
                        </p>
                    </div>

                    @if(request()->hasAny(['kecamatan', 'desa', 'mitra', 'search', 'filter_bulan']) && (request('kecamatan') != '' || request('desa') != '' || request('mitra') != '' || request('search') != '' || request('filter_bulan') != ''))
                        <a href="{{ route('admin.transaksi') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                                Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Petani</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kios
                                / Mitra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status Ambil</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Faktur</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transaksis as $t)
                            <tr>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    #{{ $t->id_transaksi }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($t->tgl_transaksi)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-bold">{{ $t->petani->nama_petani ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 font-medium">NIK: {{ $t->petani->nik ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-bold">{{ $t->mitra->nama_mitra ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 font-medium">No.
                                        Mitra: {{ $t->mitra->nomor_mitra ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    Rp {{ number_format($t->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($t->status_pembayaran == 'success')
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">SUCCESS</span>
                                    @elseif($t->status_pembayaran == 'pending')
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700">PENDING</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">{{ strtoupper($t->status_pembayaran) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($t->status_pengambilan == 'sudah')
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700">SUDAH</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700">BELUM</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                        onclick="openPrintPreview('/admin/transaksi/{{ $t->id_transaksi }}/cetak')"
                                        class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors"
                                        title="Lihat Faktur">
                                        <i data-lucide="file-text" class="h-4 w-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-400 italic">Belum ada transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $transaksis->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Print Preview --}}
    <div id="printPreviewModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[95vh] relative">
            <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg> Preview Faktur
                </h3>
                <button onclick="closePrintPreview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            
            <div class="p-0 overflow-y-auto flex-1 flex justify-center bg-gray-50 border-b border-gray-100">
                <iframe id="printFrame" class="w-full h-[600px] bg-white border-0" src=""></iframe>
            </div>
            
            <div class="px-5 py-4 border-t border-gray-200 flex justify-end gap-3 bg-white">
                <button onclick="closePrintPreview()" class="px-4 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors shadow-sm">Batal</button>
                <button onclick="executePrint()" class="px-4 py-2 text-sm font-bold text-white bg-violet-600 hover:bg-violet-700 rounded-xl flex items-center gap-2 transition-colors shadow-sm shadow-violet-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg> Cetak PDF
                </button>
            </div>
        </div>
    </div>
@endsection

<script>
    function openPrintPreview(url) {
        const modal = document.getElementById('printPreviewModal');
        const frame = document.getElementById('printFrame');
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        frame.src = url;
    }

    function closePrintPreview() {
        const modal = document.getElementById('printPreviewModal');
        const frame = document.getElementById('printFrame');
        
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        frame.src = '';
    }

    function executePrint() {
        const frame = document.getElementById('printFrame');
        if(frame.contentWindow) {
            frame.contentWindow.print();
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('printPreviewModal');
        if (event.target == modal) {
            closePrintPreview();
        }
    }
</script>
