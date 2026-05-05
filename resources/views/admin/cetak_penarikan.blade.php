<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Penarikan - {{ $penarikan->id_penarikan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: white; margin: 0; padding: 0; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 0; size: 100mm 150mm; }
        }
    </style>
</head>
<body class="bg-white text-gray-800 p-5 w-full max-w-[380px] mx-auto text-[10px]">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shrink-0">
            <i data-lucide="wallet" class="w-5 h-5"></i>
        </div>
        <div>
            <h1 class="text-base font-black text-gray-900 tracking-tight uppercase leading-none mb-0.5">Bukti Penarikan</h1>
            <p class="text-[8px] text-gray-500 font-medium">Sistem Subsidi Pupuk Kab. Ciamis</p>
        </div>
    </div>

    <div class="h-px w-full bg-gray-100 mb-4"></div>

    {{-- Info Grid --}}
    <div class="flex justify-between mb-4">
        <div class="w-1/2 pr-2">
            <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ditransfer Kepada</div>
            <h3 class="text-[11px] font-bold text-gray-900 leading-tight">{{ $penarikan->mitra->nama_mitra ?? '-' }}</h3>
            <p class="text-[9px] text-gray-500 mt-0.5">Pemilik: {{ $penarikan->mitra->nama_pemilik ?? '-' }}</p>
            <p class="text-[9px] text-gray-500 mt-0.5 leading-tight">Bank {{ $penarikan->mitra->bank ?? 'BRI' }} - {{ $penarikan->mitra->no_rek ?? '-' }}</p>
        </div>
        <div class="w-1/2 pl-2 text-right">
            <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-1">ID Penarikan</div>
            <div class="inline-block bg-gray-50 px-2 py-1 rounded text-[11px] font-bold text-gray-900 break-all">#{{ $penarikan->id_penarikan }}</div>
            
            <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-2 mb-1">Tanggal & Waktu</div>
            <p class="text-[11px] font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($penarikan->tgl_transfer)->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>

    {{-- Details Table --}}
    <div class="mb-4">
        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rincian Penarikan</div>
        <div class="border border-gray-100 rounded-lg overflow-hidden">
            <table class="w-full text-left text-[9px]">
                <thead class="bg-gray-50/50 text-gray-600 font-bold">
                    <tr>
                        <th class="px-2 py-2">Keterangan</th>
                        <th class="px-2 py-2 text-center">Status</th>
                        <th class="px-2 py-2 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-2 py-2 text-gray-900 font-medium">Pencairan Saldo Virtual Mitra</td>
                        <td class="px-2 py-2 text-center">
                            @if($penarikan->status === 'success')
                                <span class="text-emerald-600 font-bold">Berhasil</span>
                            @elseif($penarikan->status === 'failed')
                                <span class="text-red-600 font-bold">Gagal</span>
                            @else
                                <span class="text-orange-500 font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 text-right text-gray-900 font-bold">Rp {{ number_format($penarikan->jml_transfer, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            
            {{-- Total Footer --}}
            <div class="bg-gray-50/30 px-2 py-2 flex justify-between items-center border-t border-gray-100">
                <span class="font-black text-gray-900 uppercase text-[10px] tracking-wide">Total Transfer</span>
                <span class="text-sm font-black text-indigo-600">Rp {{ number_format($penarikan->jml_transfer, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
