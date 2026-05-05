@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
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
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-green-700">Saldo Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau dan kelola saldo virtual dari transaksi yang sudah berhasil.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="bg-gradient-to-br from-green-600 to-green-500 text-white rounded-xl shadow-md p-6 border-0 col-span-1 md:col-span-2 lg:col-span-1">
            <div class="flex flex-row items-center justify-between pb-4">
                <h3 class="text-base font-semibold text-white/90">Total Saldo Aktif</h3>
                <i data-lucide="wallet" class="h-6 w-6 text-white/90"></i>
            </div>
            <div>
                <div class="text-4xl font-bold">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                <p class="text-sm text-white/80 mt-2">Saldo ini dapat dicairkan melalui fitur Tarik Saldo</p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/20">
                <button @click="showModal = true" class="inline-flex items-center gap-2 bg-white text-green-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-50 transition-colors w-full sm:w-auto justify-center">
                    <i data-lucide="arrow-right-circle" class="h-4 w-4"></i> Tarik Saldo
                </button>
            </div>
        </div>
        
        <div class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <i data-lucide="info" class="h-6 w-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Informasi Saldo Virtual</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Saldo virtual akan bertambah secara otomatis setiap kali Anda berhasil melakukan scan QR Code dan mengkonfirmasi pengambilan pupuk oleh petani. Petani yang terdaftar membayar secara non-tunai melalui aplikasi, dan dana tersebut akan diteruskan ke saldo Anda di sini.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Transaksi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Riwayat Penambahan Saldo</h3>
                <p class="text-sm text-gray-500">Daftar transaksi pengambilan pupuk yang telah diselesaikan.</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Waktu</th>
                        <th scope="col" class="px-6 py-4">ID Transaksi</th>
                        <th scope="col" class="px-6 py-4">Nama Petani</th>
                        <th scope="col" class="px-6 py-4">Nominal</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat_transaksi as $t)
                        <tr class="bg-white border-b hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($t->updated_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $t->id_transaksi }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $t->petani->nama_petani ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-green-600">
                                +Rp {{ number_format($t->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full border border-green-200">
                                    Berhasil
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="h-10 w-10 text-gray-300 mb-2"></i>
                                    <p class="font-medium text-gray-600">Belum ada riwayat penambahan saldo</p>
                                    <p class="text-xs text-gray-400 mt-1">Selesaikan transaksi dengan menscan QR petani untuk menambah saldo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Penarikan Saldo --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Riwayat Penarikan Saldo</h3>
                <p class="text-sm text-gray-500">Daftar riwayat penarikan saldo (penarikan) yang pernah dilakukan.</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Waktu</th>
                        <th scope="col" class="px-6 py-4">Nominal Tarik</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat_penarikan as $p)
                        <tr class="bg-white border-b hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($p->updated_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-red-600">
                                -Rp {{ number_format($p->jml_transfer, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->status === 'success')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full border border-green-200">Berhasil</span>
                                @elseif($p->status === 'pending')
                                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-1 rounded-full border border-orange-200">Pending</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full border border-red-200">Gagal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="wallet" class="h-10 w-10 text-gray-300 mb-2"></i>
                                    <p class="font-medium text-gray-600">Belum ada riwayat penarikan saldo</p>
                                    <p class="text-xs text-gray-400 mt-1">Lakukan penarikan saldo untuk melihat riwayatnya di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tarik Saldo --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div @click.away="showModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 relative">
            <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
            
            <h2 class="text-xl font-bold text-gray-900 mb-2">Tarik Saldo</h2>
            <p class="text-sm text-gray-500 mb-6">Penarikan minimal Rp 10.000 dan dibatasi maksimal 1 kali per hari.</p>
            
            <form action="{{ route('mitra.proses_tarik_saldo') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="jml_transfer" class="block text-sm font-medium text-gray-700 mb-1">Nominal Penarikan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="jml_transfer" id="jml_transfer" required min="10000" max="{{ $saldo }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm py-2 px-3 border"
                            placeholder="10000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Saldo Anda: Rp {{ number_format($saldo, 0, ',', '.') }}</p>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        Proses Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endsection
