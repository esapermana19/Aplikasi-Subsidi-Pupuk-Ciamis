@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
        <h3 class="text-gray-500 text-sm font-bold">TOTAL PETANI</h3>
        <p class="text-3xl font-bold">120</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
        <h3 class="text-gray-500 text-sm font-bold">DISTRIBUSI PUPUK</h3>
        <p class="text-3xl font-bold">450 Ton</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500">
        <h3 class="text-gray-500 text-sm font-bold">MENUNGGU VERIFIKASI</h3>
        <p class="text-3xl font-bold text-yellow-600">5 Akun</p>
    </div>
</div>

<div class="mt-8 bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-xl font-bold mb-4">Aksi Cepat</h2>
    <a href="{{ route('admin.verifikasi') }}" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        Buka Halaman Verifikasi Akun
    </a>
</div>
@endsection
