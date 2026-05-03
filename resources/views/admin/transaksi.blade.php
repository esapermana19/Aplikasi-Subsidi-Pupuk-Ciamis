@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Transaksi</h1>
                <p class="text-sm text-gray-500 mt-1">Keseluruhan riwayat transaksi pembelian pupuk dari semua petani dan
                    mitra.</p>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <form action="{{ route('admin.transaksi') }}" method="GET" id="filterForm">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
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

                    @if(request()->hasAny(['kecamatan', 'desa', 'mitra', 'search']) && (request('kecamatan') != '' || request('desa') != '' || request('mitra') != '' || request('search') != ''))
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
                                        onclick='openFakturModal(@json($t), @json($t->rincian))'
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

    {{-- Modal Faktur --}}
    <div id="fakturModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeFakturModal()">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">

                <div id="fakturContent" class="p-8 bg-white">
                    {{-- Kop Surat --}}
                    <div class="flex items-center gap-4 mb-8 pb-6 border-b-2 border-gray-100">
                        <div
                            class="h-16 w-16 bg-green-600 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-green-200">
                            <i data-lucide="leaf" class="h-8 w-8"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">FAKTUR PEMBELIAN</h2>
                            <p class="text-sm font-medium text-gray-500 mt-1">Sistem Subsidi Pupuk Kab. Ciamis</p>
                        </div>
                    </div>

                    {{-- Info Transaksi --}}
                    <div class="grid grid-cols-2 gap-8 mb-8">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Diterbitkan
                                    Untuk</p>
                                <p class="text-sm font-bold text-gray-900" id="faktur_petani"></p>
                                <p class="text-[10px] font-medium text-gray-500 mt-0.5" id="faktur_petani_nik"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kios / Mitra
                                    Penyalur</p>
                                <p class="text-sm font-bold text-gray-900" id="faktur_mitra"></p>
                                <p class="text-[10px] font-medium text-gray-500 mt-0.5" id="faktur_mitra_alamat"></p>
                            </div>
                        </div>

                        <div class="space-y-4 text-right">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ID Transaksi
                                </p>
                                <p class="text-sm font-bold text-gray-900 bg-gray-50 inline-block px-3 py-1 rounded-lg"
                                    id="faktur_id"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal
                                    Transaksi</p>
                                <p class="text-sm font-bold text-gray-900" id="faktur_tanggal"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Rincian Pupuk --}}
                    <div class="mb-8">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Rincian Pembelian</p>
                        <div class="border rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700">Nama Pupuk</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Harga</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-700">Qty</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="faktur_rincian" class="divide-y divide-gray-100">
                                    {{-- Rincian akan dirender via JS --}}
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-right font-black text-gray-900 uppercase">
                                            Total Pembayaran</td>
                                        <td class="px-4 py-4 text-right font-black text-violet-600 text-lg"
                                            id="faktur_total"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-white flex items-center justify-center shadow-sm text-gray-500">
                                <i data-lucide="shield-check" class="h-5 w-5"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Pembayaran
                                </p>
                                <p class="text-sm font-bold" id="faktur_status_bayar"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status
                                Pengambilan</p>
                            <p class="text-sm font-bold" id="faktur_status_ambil"></p>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="printFaktur()"
                        class="px-6 py-2 text-sm font-bold text-violet-600 bg-violet-50 border border-violet-100 rounded-xl hover:bg-violet-100 transition-colors flex items-center gap-2">
                        <i data-lucide="printer" class="h-4 w-4"></i> Cetak
                    </button>
                    <button type="button" onclick="closeFakturModal()"
                        class="px-6 py-2 text-sm font-bold text-white bg-gray-800 rounded-xl hover:bg-gray-900 transition-all shadow-md shadow-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function openFakturModal(transaksi, rincian) {
        document.getElementById('faktur_id').innerText = '#' + transaksi.id_transaksi;

        // Format tanggal
        const date = new Date(transaksi.tgl_transaksi);
        const options = {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.getElementById('faktur_tanggal').innerText = date.toLocaleDateString('id-ID', options);

        document.getElementById('faktur_petani').innerText = transaksi.petani ? transaksi.petani.nama_petani : '-';
        document.getElementById('faktur_petani_nik').innerText = 'NIK: ' + (transaksi.petani && transaksi.petani.nik ?
            transaksi.petani.nik : '-');

        document.getElementById('faktur_mitra').innerHTML = transaksi.mitra ? transaksi.mitra.nama_mitra +
            ` <br><span class="text-[10px] font-medium text-gray-500 mt-0.5 inline-block">No: ${transaksi.mitra.nomor_mitra ?? '-'}</span>` :
            '-';

        // Alamat Mitra
        let alamatMitra = '-';
        if (transaksi.mitra) {
            const desa = transaksi.mitra.desa ? transaksi.mitra.desa.nama_desa : '';
            const kec = transaksi.mitra.kecamatan ? transaksi.mitra.kecamatan.nama_kecamatan : '';
            if (desa && kec) alamatMitra = desa + ', ' + kec;
            else if (desa) alamatMitra = desa;
            else if (kec) alamatMitra = kec;
        }
        document.getElementById('faktur_mitra_alamat').innerText = alamatMitra;

        // Format Currency
        const formatRupiah = (angka) => 'Rp ' + parseInt(angka).toLocaleString('id-ID');

        document.getElementById('faktur_total').innerText = formatRupiah(transaksi.total);

        // Status Pembayaran
        const statusBayarEl = document.getElementById('faktur_status_bayar');
        if (transaksi.status_pembayaran === 'success') {
            statusBayarEl.innerText = 'LUNAS (SUCCESS)';
            statusBayarEl.className = 'text-sm font-bold text-green-600';
        } else if (transaksi.status_pembayaran === 'pending') {
            statusBayarEl.innerText = 'MENUNGGU (PENDING)';
            statusBayarEl.className = 'text-sm font-bold text-amber-600';
        } else {
            statusBayarEl.innerText = transaksi.status_pembayaran.toUpperCase();
            statusBayarEl.className = 'text-sm font-bold text-red-600';
        }

        // Status Pengambilan
        const statusAmbilEl = document.getElementById('faktur_status_ambil');
        if (transaksi.status_pengambilan === 'sudah') {
            statusAmbilEl.innerText = 'SUDAH DIAMBIL';
            statusAmbilEl.className = 'text-sm font-bold text-blue-600';
        } else {
            statusAmbilEl.innerText = 'BELUM DIAMBIL';
            statusAmbilEl.className = 'text-sm font-bold text-gray-600';
        }

        // Rincian
        const tbody = document.getElementById('faktur_rincian');
        tbody.innerHTML = '';

        rincian.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-3 text-gray-900 font-medium">${item.pupuk ? item.pupuk.nama_pupuk : '-'}</td>
                <td class="px-4 py-3 text-right text-gray-600">${formatRupiah(item.harga_satuan)}</td>
                <td class="px-4 py-3 text-center font-medium">${item.jml_beli} Kg</td>
                <td class="px-4 py-3 text-right font-medium text-gray-900">${formatRupiah(item.subtotal)}</td>
            `;
            tbody.appendChild(tr);
        });

        // Tampilkan Modal
        document.getElementById('fakturModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeFakturModal() {
        document.getElementById('fakturModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function printFaktur() {
        // Simple print function using window.print() but restricted to the modal content
        const printContent = document.getElementById('fakturContent').innerHTML;
        const originalContent = document.body.innerHTML;

        // Custom print styles
        const printStyles = `
            <style>
                @media print {
                    body { background: white; margin: 0; padding: 20px; font-family: sans-serif; }
                    .lucide { width: 24px; height: 24px; }
                    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                    .shadow-lg { box-shadow: none !important; border: 1px solid #eee; }
                }
            </style>
        `;

        document.body.innerHTML = printStyles + '<div style="max-width: 800px; margin: 0 auto;">' + printContent +
            '</div>';

        window.print();

        // Restore original content
        document.body.innerHTML = originalContent;

        // Re-init lucide icons and event listeners
        if (typeof lucide !== 'undefined') lucide.createIcons();
        window.location.reload(); // Reload to restore all event listeners easily
    }

    // Menutup modal jika di klik di luar
    window.onclick = function(event) {
        const modal = document.getElementById('fakturModal');
        const modalBackdrop = document.querySelector('.bg-gray-900\\/60');
        if (event.target == modal || event.target == modalBackdrop) {
            closeFakturModal();
        }
    }
</script>
