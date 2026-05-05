@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-green-700">Riwayat Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar penyerahan pupuk yang telah selesai.</p>
        </div>
        <a href="{{ url('/mitra/scan') }}" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
            + Scan QR Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b">
                        <th class="p-4 font-semibold">Tgl Penyerahan</th>
                        <th class="p-4 font-semibold">ID Transaksi</th>
                        <th class="p-4 font-semibold">Nama Petani</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($riwayat as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-4 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="p-4 text-sm font-mono text-gray-600">
                                {{ $item->id_transaksi }}
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-800">
                                {{ $item->petani->nama_petani ?? '-' }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    Selesai / Diambil
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button type="button" onclick="openPrintPreview('{{ route('mitra.riwayat.cetak', $item->id_transaksi) }}')" class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors inline-block" title="Cetak Faktur">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">
                                Belum ada riwayat penyerahan pupuk saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>

{{-- Modal Print Preview --}}
<div id="printPreviewModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[95vh] relative">
        <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg> Preview Faktur
            </h3>
            <button onclick="closePrintPreview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        
        <div class="p-0 overflow-y-auto flex-1 flex justify-center bg-gray-50 border-b border-gray-100">
            <iframe id="printFrame" class="w-full h-[600px] bg-white border-0" src=""></iframe>
        </div>
        
        <div class="px-5 py-4 border-t border-gray-200 flex justify-end gap-3 bg-white">
            <button onclick="closePrintPreview()" class="px-4 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition-colors">Batal</button>
            <button onclick="executePrint()" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg> Cetak Sekarang
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
</script>
@endsection