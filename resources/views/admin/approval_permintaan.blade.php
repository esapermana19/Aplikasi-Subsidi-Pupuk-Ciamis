@extends('layouts.app') @section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo. {{ Auth::user()->admin->nama_admin ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dan setujui permintaan stok pupuk dari mitra.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.permintaan.export', request()->query()) }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-emerald-700 hover:shadow-emerald-100 transition-all active:scale-95">
                    <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                    Ekspor Excel
                </a>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
            <form action="{{ route('admin.approval_permintaan') }}" method="GET" id="filterForm">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Filter Bulan --}}
                        <div class="relative flex-1 min-w-[140px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <input type="month" name="filter_bulan" value="{{ request('filter_bulan') }}" onchange="this.form.submit()"
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

                    {{-- Search Bar --}}
                    <div class="relative w-full lg:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all"
                            placeholder="Cari ID, Nama Mitra..." onkeypress="if(event.keyCode == 13) this.form.submit();">
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <p class="text-xs text-gray-500 font-medium">
                            Total <span class="text-gray-950 font-bold">{{ $permintaans->total() }}</span> Permintaan
                        </p>
                    </div>

                    @if(request()->hasAny(['kecamatan', 'desa', 'mitra', 'search', 'filter_bulan']) && (request('kecamatan') != '' || request('desa') != '' || request('mitra') != '' || request('search') != '' || request('filter_bulan') != ''))
                        <a href="{{ route('admin.approval_permintaan') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nama Kios/Mitra</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Catatan</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permintaans as $req)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ \Carbon\Carbon::parse($req->tgl_permintaan)->format('d/m/Y') }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold">{{ $req->mitra->nama_mitra ?? '-' }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $req->catatan ?? '-' }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span
                                    class="px-2 py-1 font-semibold rounded-full text-xs
                            {{ $req->status_permintaan == 'pending'
                                ? 'bg-yellow-100 text-yellow-800'
                                : ($req->status_permintaan == 'diproses' || $req->status_permintaan == 'disetujui'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($req->status_permintaan == 'selesai' || $req->status_permintaan == 'diterima'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800')) }}">
                                    {{ strtoupper($req->status_permintaan) }}
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <button onclick="openModal('{{ $req->id_permintaan }}', '{{ $req->status_permintaan }}')"
                                    class="bg-violet-500 hover:bg-violet-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    Detail / Proses
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $permintaans->links() }}
        </div>
    </div>

    <div id="modalApproval" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 p-6 relative max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Permintaan</h2>

            <form id="formApproval" method="POST" action="">
                @csrf

                <div id="detailPupukContainer" class="mb-4 space-y-3">
                    <p class="text-gray-500 text-sm">Memuat detail pupuk...</p>
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t pt-4" id="actionButtons">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-violet-600 text-white rounded hover:bg-violet-700">Tutup</button>

                    <button type="submit" name="action" value="tolak"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tolak Permintaan</button>

                    <button type="submit" name="action" value="setujui"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Setujui & Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id_permintaan, status) {
            const modal = document.getElementById('modalApproval');
            const form = document.getElementById('formApproval');
            const container = document.getElementById('detailPupukContainer');
            const actionButtons = document.getElementById('actionButtons');

            // Atur action form sesuai ID permintaan
            form.action = `/admin/permintaan/${id_permintaan}/update`;

            // Tampilkan Modal
            modal.classList.remove('hidden');
            container.innerHTML = '<p class="text-gray-500 text-sm">Memuat data...</p>';

            // Sembunyikan tombol approve/reject jika status sudah bukan pending
            if (status !== 'pending') {
                actionButtons.querySelector('button[value="tolak"]').classList.add('hidden');
                actionButtons.querySelector('button[value="setujui"]').classList.add('hidden');
            } else {
                actionButtons.querySelector('button[value="tolak"]').classList.remove('hidden');
                actionButtons.querySelector('button[value="setujui"]').classList.remove('hidden');
            }

            // Fetch detail dari API
            fetch(`/admin/permintaan/${id_permintaan}/detail`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';

                    // Header Kolom
                    container.innerHTML += `
                    <div class="flex font-bold text-sm text-gray-600 border-b pb-2">
                        <div class="w-1/3">Jenis Pupuk</div>
                        <div class="w-1/3 text-center">Jumlah Diminta</div>
                        <div class="w-1/3 text-right">Jumlah Disetujui</div>
                    </div>
                `;

                    data.forEach(item => {
                        let jml_diminta_zak = item.jml_diminta / 50;
                        let jml_disetujui_zak = item.jml_disetujui / 50;
                        // Jika belum disetujui (0), otomatis isi rekomendasi sesuai jumlah diminta
                        let defaultSetuju = item.jml_disetujui == 0 ? jml_diminta_zak : jml_disetujui_zak;

                        // Menonaktifkan input jika status sudah bukan pending
                        let readonly = status !== 'pending' ? 'readonly class="bg-gray-100"' : '';

                        container.innerHTML += `
                        <div class="flex items-center text-sm py-2 border-b">
                            <div class="w-1/3 font-medium text-gray-800">${item.nama_pupuk}</div>
                            <div class="w-1/3 text-center text-gray-800 font-bold">${jml_diminta_zak} Zak</div>
                            <div class="w-1/3 text-right flex justify-end items-center gap-2">
                                <input type="number" name="pupuk_disetujui[${item.id_detail_permintaan}]"
                                       value="${defaultSetuju}" min="0" max="${jml_diminta_zak}"
                                       class="w-20 border border-gray-300 rounded px-2 py-1 text-center focus:outline-none focus:border-green-500"
                                       ${readonly}>
                                <span class="text-xs text-gray-500">Zak</span>
                            </div>
                        </div>
                    `;
                    });
                });
        }

        function closeModal() {
            document.getElementById('modalApproval').classList.add('hidden');
        }
    </script>
@endsection
