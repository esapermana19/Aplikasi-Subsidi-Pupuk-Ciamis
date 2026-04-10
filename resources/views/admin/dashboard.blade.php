@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
        <p class="text-gray-500 mt-1">Ringkasan sistem subsidi pupuk Kabupaten Ciamis</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Petani</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_petani']) }}</p>
                    <p class="text-xs text-green-600 font-medium mt-1">+{{ $stats['total_petani'] }} Terdaftar</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i data-lucide="users" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Jenis Pupuk</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['jenis_pupuk'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Tersedia di sistem</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i data-lucide="leaf" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Mitra Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['mitra_aktif'] }}</p>
                    <p class="text-xs text-blue-600 font-medium mt-1">Kios Terverifikasi</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg">
                    <i data-lucide="building-2" class="h-6 w-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Subsidi (Bulan Ini)</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_subsidi'] / 1000000, 1) }} Jt</p>
                    <p class="text-xs text-green-600 font-medium mt-1">Target Penyerapan</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Verifikasi Akun</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_verifikasi'] }}</p>
                    <p class="text-xs text-orange-600 font-medium mt-1">Butuh Approval</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <i data-lucide="shopping-cart" class="h-6 w-6 text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['transaksi_hari_ini'] }}</p>
                    <p class="text-xs text-green-600 font-medium mt-1">Proses Penebusan</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i data-lucide="trending-up" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-50">
            <h3 class="text-lg font-bold">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @foreach($recentActivities as $activity)
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Pendaftaran Akun Baru</p>
                        <p class="text-sm text-gray-500">{{ $activity->name }} ({{ ucfirst($activity->role) }})</p>
                    </div>
                    <div class="flex items-center gap-4">
                        @if($activity->status_akun == 'pending')
                            <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Selesai</span>
                        @endif
                        <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
