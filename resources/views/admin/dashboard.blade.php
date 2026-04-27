@extends('layouts.app')

@section('content')
    <div class="space-y-6">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo. {{ Auth::user()->admin->nama_admin ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali! Berikut ringkasan sistem hari ini.</p>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-xs font-bold text-violet-600 bg-violet-50 px-3 py-1 rounded-full border border-violet-100">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Total Petani --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Petani</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_petani']) }}</p>
                        <p class="text-[10px] text-green-600 font-bold mt-1 bg-green-50 px-2 py-0.5 rounded-full inline-block">+{{ $stats['total_petani'] }} Terdaftar</p>
                    </div>
                    <div class="bg-green-50 p-2.5 rounded-lg">
                        <i data-lucide="users" class="h-5 w-5 text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Mitra Aktif --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mitra Aktif</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['mitra_aktif'] }}</p>
                        <p class="text-[10px] text-violet-600 font-bold mt-1 bg-violet-50 px-2 py-0.5 rounded-full inline-block">Kios Terverifikasi</p>
                    </div>
                    <div class="bg-violet-50 p-2.5 rounded-lg">
                        <i data-lucide="building-2" class="h-5 w-5 text-violet-600"></i>
                    </div>
                </div>
            </div>

            {{-- Verifikasi Akun --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm sm:col-span-2 lg:col-span-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikasi Akun</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending_verifikasi'] }}</p>
                        <p class="text-[10px] text-amber-600 font-bold mt-1 bg-amber-50 px-2 py-0.5 rounded-full inline-block">Butuh Approval</p>
                    </div>
                    <div class="bg-amber-50 p-2.5 rounded-lg">
                        <i data-lucide="user-check" class="h-5 w-5 text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Aktivitas Terbaru --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Aktivitas Terbaru</h3>
                    <p class="text-[10px] text-gray-500 mt-0.5">Pendaftaran dan pembaruan data terakhir</p>
                </div>
                <i data-lucide="bell" class="h-4 w-4 text-gray-400"></i>
            </div>
            <div class="p-2 sm:p-5">
                <div class="space-y-2">
                    @forelse($recentActivities as $activity)
                        @php
                            $isPetani = $activity->role == 'Petani';
                            $profile = $isPetani ? $activity->petani : $activity->mitra;
                            $displayName = $isPetani
                                ? $profile->nama_petani ?? 'User'
                                : $profile->nama_mitra ?? 'Mitra';
                        @endphp

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 rounded-xl hover:bg-gray-50 transition-all border border-gray-50 hover:border-gray-200 group">
                            <div class="flex items-center gap-3 sm:gap-4 flex-1 mb-3 sm:mb-0">
                                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full {{ $isPetani ? 'bg-green-100 border-green-200 text-green-600' : 'bg-violet-100 border-violet-200 text-violet-600' }} border flex items-center justify-center shadow-sm shrink-0 transition-transform group-hover:scale-105">
                                    <i data-lucide="{{ $isPetani ? 'user' : 'store' }}" class="h-5 w-5 sm:h-6 sm:w-6"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm sm:text-base font-bold text-gray-900 truncate">{{ $displayName }}</p>
                                    <p class="text-[11px] sm:text-sm text-gray-500 flex items-center gap-1.5 mt-0.5">
                                        <span class="{{ $isPetani ? 'text-green-600' : 'text-violet-600' }} font-bold bg-opacity-10 px-1.5 rounded">
                                            {{ ucfirst($activity->role) }}
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="clock" class="h-3 w-3"></i>
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
