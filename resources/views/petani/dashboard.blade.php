@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-gray-800">Dashboard Petani</h1>
<div class="bg-white p-8 rounded-2xl shadow-lg border border-green-100">
    <div class="flex items-center gap-4 mb-6">
        <div class="p-3 bg-green-100 rounded-full text-green-700">
            <i data-lucide="info"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sisa Kuota Pupuk Anda</h2>
            <p class="text-gray-500">Update Terakhir: {{ now()->format('d M Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 rounded-lg border">
            <p class="text-sm font-bold text-gray-600 uppercase">Pupuk Urea</p>
            <p class="text-4xl font-black text-green-600">50 <span class="text-lg">Kg</span></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg border">
            <p class="text-sm font-bold text-gray-600 uppercase">Pupuk NPK</p>
            <p class="text-4xl font-black text-blue-600">25 <span class="text-lg">Kg</span></p>
        </div>
    </div>

    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex gap-3">
        <i data-lucide="qr-code" class="text-yellow-700"></i>
        <p class="text-sm text-yellow-800">Tunjukkan kartu digital atau NIK Anda ke Mitra/Kios terdekat untuk menebus pupuk.</p>
    </div>
</div>
@endsection
