{{-- Asumsi Anda menggunakan layout utama app.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="p-6 sm:p-10 space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pupuk Tersedia</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Ketersediaan stok pupuk subsidi di kios Anda (Berdasarkan sisa dari transaksi & permintaan).
                </p>
            </div>
            <div>
                {{-- Tombol jika Mitra ingin melakukan permintaan stok baru ke pusat --}}
                <a href="{{ route('mitra.permintaan') }}"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-colors shadow-sm">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Buat Permintaan Baru
                </a>
            </div>
        </div>

        {{-- Grid Card Stok Pupuk --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($pupukList as $pupuk)
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Bagian Atas / Gambar (Opsional jika ada img_pupuk ) --}}
                    <div class="h-32 bg-green-50 rounded-t-xl flex items-center justify-center overflow-hidden">
                        @if ($pupuk->img_pupuk)
                            {{-- Tampilkan Gambar dari folder storage --}}
                            <img src="{{ asset('storage/' . $pupuk->img_pupuk) }}" alt="{{ $pupuk->nama_pupuk }}"
                                class="w-full h-full object-cover">
                        @else
                            {{-- Fallback ke Icon Box jika img_pupuk tidak ada --}}
                            <div class="p-4 bg-white rounded-lg shadow-sm">
                                <i data-lucide="package" class="h-10 w-10 text-green-500"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Bagian Detail --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $pupuk->nama_pupuk }}</h3>

                            {{-- Status Indikator --}}
                            @if ($pupuk->stok_mitra > 50)
                                <span
                                    class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Aman</span>
                            @elseif ($pupuk->stok_mitra > 0)
                                <span
                                    class="bg-orange-100 text-orange-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Menipis</span>
                            @else
                                <span
                                    class="bg-red-100 text-red-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Habis</span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 mb-4">Harga Eceran: Rp
                            {{ number_format($pupuk->harga_subsidi, 0, ',', '.') }} / {{ $pupuk->satuan ?? 'Kg' }}</p>

                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-600">Sisa Stok</span>
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-2xl font-black {{ $pupuk->stok_mitra > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $pupuk->stok_mitra }}
                                </span>
                                <span class="text-xs font-bold text-gray-500">{{ $pupuk->satuan ?? 'Kg' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-200 border-dashed p-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 mb-3">
                        <i data-lucide="package-x" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Belum Ada Data Pupuk</h3>
                    <p class="mt-1 text-sm text-gray-500">Data master pupuk belum tersedia dari pusat.</p>
                </div>
            @endforelse
        </div>

    </div>
@endsection
