@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-green-700">Permintaan Penarikan Saldo</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan proses pengajuan penarikan saldo virtual dari Mitra.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
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
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Selesai</span>
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

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endsection
