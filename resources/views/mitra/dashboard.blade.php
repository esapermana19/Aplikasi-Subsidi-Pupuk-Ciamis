@extends('layouts.app')

@section('content')
<span class="font-bold">Ini Halaman Mitra</span><br><br>
<div class="bg-white p-8 rounded-2xl shadow-lg">
    <div class="mb-6">
        <h2 class="text-sm font-bold text-green-600 uppercase tracking-widest">Toko / Mitra</h2>
        <h1 class="text-3xl font-black text-gray-900">{{ auth()->user()->nama_mitra }}</h1>
        <p class="text-gray-500 italic">{{ auth()->user()->alamat }}</p>
    </div>

    <div class="flex flex-col md:flex-row gap-4">
        <a href="{{ route('mitra.transaksi') }}" class="flex-1 bg-green-600 text-white p-6 rounded-xl hover:bg-green-700 transition flex flex-col items-center justify-center gap-3">
            <i data-lucide="scan-line" class="h-10 w-10"></i>
            <span class="font-bold">Mulai Transaksi Baru</span>
        </a>
        <div class="flex-1 bg-gray-100 p-6 rounded-xl flex flex-col items-center justify-center">
            <p class="text-gray-500 font-bold uppercase text-xs">Total Penjualan Hari Ini</p>
            <p class="text-3xl font-black">Rp 2.450.000</p>
        </div>
    </div>
</div>
@endsection
