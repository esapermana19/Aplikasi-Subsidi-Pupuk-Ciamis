@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-8">
                    
                    {{-- Header Section --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Log Aktivitas</h1>
                            <p class="text-sm text-gray-500 mt-1">Pantau semua perubahan dan tindakan yang dilakukan oleh Administrator.</p>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                        <form action="{{ route('admin.log_activity') }}" method="GET" class="flex flex-col sm:flex-row gap-4" id="filterForm">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 px-3 py-2.5 transition-colors"
                                    placeholder="Cari aktivitas atau fitur...">
                            </div>
                            <div class="relative flex-1 sm:max-w-[200px]">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                                </div>
                                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 px-3 py-2.5 cursor-pointer">
                            </div>
                            @if(request('search') || request('tanggal'))
                            <a href="{{ route('admin.log_activity') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-red-200 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 focus:ring-4 focus:ring-red-100 transition-colors">
                                Reset
                            </a>
                            @endif
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50/80 border-b border-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 font-semibold">Waktu</th>
                                        <th scope="col" class="px-6 py-4 font-semibold">Administrator</th>
                                        <th scope="col" class="px-6 py-4 font-semibold">Fitur</th>
                                        <th scope="col" class="px-6 py-4 font-semibold">Aktivitas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($logs as $log)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold shrink-0">
                                                        {{ strtoupper(substr($log->user->admin->nama_admin ?? $log->user->username ?? 'S', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-gray-900 block">{{ $log->user->admin->nama_admin ?? $log->user->username ?? 'Sistem' }}</span>
                                                        <span class="text-[10px] text-gray-500 font-medium uppercase tracking-wider block mt-0.5">NIP: {{ $log->user->admin->nip ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $log->fitur }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm text-gray-900 font-medium">{{ $log->aktivitas }}</p>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                        <i data-lucide="history" class="h-8 w-8 text-gray-400"></i>
                                                    </div>
                                                    <p class="text-base font-medium text-gray-900">Belum ada log aktivitas</p>
                                                    <p class="text-sm mt-1">Data log sistem akan muncul di sini.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Pagination --}}
                        @if($logs->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    </div>
                </div>

    <script>
        lucide.createIcons();
    </script>
@endsection
