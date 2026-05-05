<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Pembelian - {{ $transaksi->id_transaksi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: white; margin: 0; padding: 0; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 0; size: 100mm 150mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-800 p-5 w-full max-w-[380px] mx-auto text-[10px]">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-leaf"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
        </div>
        <div>
            <h1 class="text-base font-black text-[#111827] tracking-tight uppercase leading-none mb-0.5">Faktur Pembelian</h1>
            <p class="text-gray-500 font-medium text-[8px]">Sistem Subsidi Pupuk Kab. Ciamis</p>
        </div>
    </div>

    <div class="h-px w-full bg-gray-100 mb-4"></div>

    {{-- Info Section --}}
    <div class="flex flex-col gap-3 mb-4">
        <div class="flex justify-between">
            <div class="w-1/2 pr-2">
                <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-1">Diterbitkan Untuk</div>
                <h3 class="text-[11px] font-bold text-gray-900 leading-tight">{{ $transaksi->nama_petani ?? '-' }}</h3>
                <p class="text-[9px] text-gray-500 mt-0.5 break-all">NIK: {{ $transaksi->nik ?? '-' }}</p>
            </div>
            <div class="w-1/2 pl-2 text-right">
                <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-1">ID Transaksi</div>
                <div class="inline-block bg-gray-50 px-2 py-1 rounded text-[11px] font-bold text-gray-900 border border-gray-100 break-all">#{{ $transaksi->id_transaksi }}</div>
            </div>
        </div>

        <div class="flex justify-between mt-1">
            <div class="w-1/2 pr-2">
                <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kios / Mitra Penyalur</div>
                <h3 class="text-[11px] font-bold text-gray-900 leading-tight">{{ $transaksi->nama_mitra ?? '-' }}</h3>
                <p class="text-[9px] text-gray-500 mt-0.5">No: {{ $transaksi->nomor_mitra ?? '-' }}</p>
                <p class="text-[9px] text-gray-500 mt-0.5 leading-tight">{{ $transaksi->alamat_mitra ?? '-' }}</p>
            </div>
            <div class="w-1/2 pl-2 text-right">
                <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Transaksi</div>
                <h3 class="text-[11px] font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($transaksi->tgl_transaksi)->translatedFormat('d M Y, H.i') }}</h3>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="mb-4">
        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rincian Pembelian</div>
        <div class="border border-gray-100 rounded-lg overflow-hidden shadow-sm">
            <table class="w-full text-left text-[9px]">
                <thead class="bg-gray-50/80 text-gray-600 font-bold">
                    <tr>
                        <th class="px-2 py-2">Item</th>
                        <th class="px-2 py-2">Harga</th>
                        <th class="px-2 py-2 text-center">Qty</th>
                        <th class="px-2 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rincian as $detail)
                        <tr>
                            <td class="px-2 py-2 text-gray-900 font-bold">{{ $detail->nama_pupuk }}</td>
                            <td class="px-2 py-2 text-gray-500">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-2 py-2 text-center text-gray-900 font-bold">{{ $detail->jml_beli }} Kg</td>
                            <td class="px-2 py-2 text-right text-gray-900 font-bold">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Total Footer --}}
            <div class="bg-gray-50/50 px-2 py-2 flex justify-between items-center border-t border-gray-100">
                <span class="font-black text-gray-900 uppercase text-[10px] tracking-wide">Total Pembayaran</span>
                <span class="text-sm font-black text-[#8B5CF6]">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    
    {{-- QR Code & Status Section --}}
    <div class="mt-4 flex gap-3">
        {{-- Status --}}
        <div class="w-1/2 bg-gray-50 rounded-lg p-3 flex flex-col justify-center border border-gray-100 text-center items-center">
            <div class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status</div>
            @if($transaksi->status_pengambilan == 'sudah')
                <div class="flex items-center gap-1 text-emerald-600 font-bold bg-emerald-50 px-2 py-1.5 rounded text-[8px] border border-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    SUDAH DIAMBIL
                </div>
            @else
                <div class="flex items-center gap-1 text-amber-600 font-bold bg-amber-50 px-2 py-1.5 rounded text-[8px] border border-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    MENUNGGU
                </div>
            @endif
        </div>

        {{-- QR Code --}}
        <div class="w-1/2 bg-gray-50 rounded-lg p-3 flex flex-col items-center justify-center border border-gray-100">
            <div class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-center">Scan Pengambilan</div>
            <div class="bg-white p-1 rounded shadow-sm border border-gray-100">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data={{ $transaksi->id_transaksi }}" class="w-12 h-12" alt="QR Code">
            </div>
        </div>
    </div>
</body>
</html>
