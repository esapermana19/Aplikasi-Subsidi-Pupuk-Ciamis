@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-green-700">Riwayat Transaksi</h2>
    <p class="text-sm text-gray-500">Klik pada transaksi untuk melihat detail faktur.</p>
</div>

<div class="space-y-3">
    @foreach($transaksi as $t)
    <div onclick="showDetail('{{ $t->id_transaksi }}')" 
         class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:border-green-500 cursor-pointer transition-all flex justify-between items-center">
        <div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $t->id_transaksi }}</p>
            <h4 class="font-bold text-gray-800">{{ $t->nama_kios }}</h4>
            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($t->tgl_transaksi)->format('d M Y, H:i') }} WIB</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-green-700">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</p>
            @if($t->status_pengambilan == 'sudah')
                <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">Selesai</span>
            @else
                <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">Belum Diambil</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div id="modalFaktur" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl overflow-hidden max-w-lg w-full shadow-2xl">
        <div class="bg-green-600 p-4 text-white flex justify-between items-center">
            <h3 class="font-bold">Faktur Pembelian</h3>
            <button onclick="closeModal()" class="hover:bg-white/20 rounded-full p-1"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        
        <div id="isi-faktur" class="p-6">
            </div>

        <div class="p-4 bg-gray-50 flex gap-2">
            <button class="flex-1 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-white transition-colors">Cetak PDF</button>
            <button onclick="closeModal()" class="flex-1 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">Tutup</button>
        </div>
    </div>
</div>

<script>
async function showDetail(id) {
    const res = await fetch(`/api/transaksi/detail/${id}`);
    const data = await res.json();
    
    const t = data.transaksi;
    const items = data.details;

    let itemsHtml = '';
    items.forEach(item => {
        itemsHtml += `
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">${item.nama_pupuk} (${item.jml_beli} kg)</span>
                <span class="font-medium">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</span>
            </div>
        `;
    });

    document.getElementById('isi-faktur').innerHTML = `
        <div class="text-center mb-6">
            <p class="text-xs text-gray-400 mb-1">ID TRANSAKSI</p>
            <p class="font-black text-gray-800 text-lg">${t.id_transaksi}</p>
        </div>
        
        <div class="space-y-3 border-b border-dashed pb-4 mb-4">
            <div class="flex justify-between text-xs items-start">
                <span class="text-gray-500">Mitra</span>
                <div class="text-right">
                    <span class="font-bold text-gray-800 block">${t.nama_kios}</span>
                    <span class="text-[10px] text-gray-500 font-medium">No: ${t.nomor_mitra ?? '-'}</span>
                </div>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-bold text-gray-800">${t.tgl_transaksi}</span>
            </div>
        </div>

        <div class="mb-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Item Pembelian</p>
            ${itemsHtml}
            <div class="flex justify-between border-t mt-2 pt-2 font-bold text-green-700">
                <span>Total</span>
                <span>Rp ${parseInt(t.total_harga).toLocaleString('id-ID')}</span>
            </div>
        </div>

        <div class="flex flex-col items-center pt-4 border-t border-gray-100 text-center">
            <p class="text-[10px] font-bold text-gray-500 mb-2">SCAN UNTUK PENGAMBILAN</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${t.id_transaksi}" class="w-24 h-24 mb-2">
            <span class="px-3 py-1 rounded-md text-[10px] font-bold ${t.status_pengambilan == 'sudah' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'}">
                ${t.status_pengambilan == 'sudah' ? 'SUDAH DIAMBIL' : 'MENUNGGU PENGAMBILAN'}
            </span>
        </div>
    `;

    document.getElementById('modalFaktur').classList.remove('hidden');
    document.getElementById('modalFaktur').classList.add('flex');
    lucide.createIcons();
}

function closeModal() {
    document.getElementById('modalFaktur').classList.add('hidden');
    document.getElementById('modalFaktur').classList.remove('flex');
}
</script>
@endsection