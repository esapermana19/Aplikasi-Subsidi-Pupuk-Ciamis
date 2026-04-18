@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-500 mt-1">Ringkasan sistem subsidi pupuk Kabupaten Ciamis</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {{-- Total Petani --}}
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

            {{-- Mitra Aktif (Aksen Violet Ciamis) --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Mitra Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['mitra_aktif'] }}</p>
                        <p class="text-xs text-violet-600 font-medium mt-1">Kios Terverifikasi</p>
                    </div>
                    <div class="bg-violet-50 p-3 rounded-lg">
                        <i data-lucide="building-2" class="h-6 w-6 text-violet-600"></i>
                    </div>
                </div>
            </div>

            {{-- Verifikasi Akun --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Verifikasi Akun</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_verifikasi'] }}</p>
                        <p class="text-xs text-amber-600 font-medium mt-1">Butuh Approval</p>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-lg">
                        <i data-lucide="user-check" class="h-6 w-6 text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Aktivitas Terbaru - Dinamis Berdasarkan Role --}}
        {{-- Riwayat Aktivitas Terbaru --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h3>
                <p class="text-xs text-gray-500 mt-1">Menampilkan pendaftaran dan pembaruan data terakhir</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        {{-- Asumsi: Jika $activity adalah model User yang memiliki relasi petani/mitra --}}
                        @php
                            $isPetani = $activity->role == 'Petani';
                            $profile = $isPetani ? $activity->petani : $activity->mitra;
                            $displayName = $isPetani
                                ? $profile->nama_petani ?? 'User'
                                : $profile->nama_mitra ?? 'Mitra';
                        @endphp

                        <div
                            class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                            <div class="flex items-center gap-4 flex-1">
                                {{-- Ikon Profil Dinamis --}}
                                <div
                                    class="h-12 w-12 rounded-full {{ $isPetani ? 'bg-green-100 border-green-200' : 'bg-violet-100 border-violet-200' }} border-2 flex items-center justify-center shadow-sm">
                                    <i data-lucide="user"
                                        class="h-6 w-6 {{ $isPetani ? 'text-green-600' : 'text-violet-600' }}"></i>
                                </div>

                                <div>
                                    <p class="text-base font-semibold text-gray-900">{{ $displayName }}</p>
                                    <p class="text-sm text-gray-600">
                                        <span class="{{ $isPetani ? 'text-green-600' : 'text-violet-600' }} font-medium">
                                            {{ ucfirst($activity->role) }}
                                        </span>
                                        •
                                        <span class="text-gray-400 text-xs">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- Badge Status --}}
                                @if ($activity->status_akun == 'pending')
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-200">
                                        <span class="relative flex h-2 w-2">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                        </span>
                                        Menunggu Verifikasi
                                    </div>
                                @elseif($activity->status_akun == 'aktif')
                                    <div class="text-right">
                                        <div
                                            class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                                            <i data-lucide="check-circle-2" class="h-3.5 w-3.5"></i>
                                            Terverifikasi
                                        </div>
                                        @if ($activity->verifikatorAdmin)
                                            {{-- Gunakan nama relasi baru di model User --}}
                                            <p
                                                class="text-[10px] text-gray-500 font-medium mt-1.5 flex items-center gap-1 justify-end">
                                                <i data-lucide="user-cog" class="h-3 w-3"></i>
                                                Oleh: {{ $activity->verifikatorAdmin->nama_admin }}
                                            </p>
                                        @endif
                                    </div>
                                @elseif($activity->status_akun == 'ditolak')
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold bg-red-50 text-red-700 rounded-full border border-red-200">
                                        <i data-lucide="x-circle" class="h-3.5 w-3.5"></i>
                                        Ditolak
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-400 italic text-sm">
                            Belum ada aktivitas terbaru.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
