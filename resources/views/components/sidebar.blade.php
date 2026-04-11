@props(['active'])

<div class="flex h-screen w-64 flex-col border-r bg-white fixed left-0 top-0">
    <div class="flex h-16 items-center gap-3 border-b px-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-600">
            <i data-lucide="leaf" class="h-6 w-6 text-white"></i>
        </div>
        <div>
            <h2 class="text-base font-semibold text-gray-900 leading-none">Subsidi Pupuk</h2>
            <p class="text-xs text-gray-500">Kab. Ciamis</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-3 py-4">
        <div class="space-y-1">
            @php
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'dashboard'],
                    [
                        'id' => 'verifikasi',
                        'label' => 'Verifikasi Akun',
                        'icon' => 'user-check',
                        'route' => 'verifikasi',
                    ],
                    ['id' => 'petani', 'label' => 'Kelola Akun Petani', 'icon' => 'users', 'route' => 'admin.list_petani'],
                    ['id' => 'mitra', 'label' => 'Kelola  Akun Mitra', 'icon' => 'building-2', 'route' => 'mitra'],
                    ['id' => 'pupuk', 'label' => 'Kelola Pupuk', 'icon' => 'leaf', 'route' => 'pupuk'],
                    [
                        'id' => 'approval-permintaan',
                        'label' => 'Approval Permintaan',
                        'icon' => 'file-check',
                        'route' => 'approval-permintaan',
                    ],
                    [
                        'id' => 'approval-pencairan',
                        'label' => 'Approval Pencairan',
                        'icon' => 'dollar-sign',
                        'route' => 'approval-pencairan',
                    ],
                    [
                        'id' => 'rekonsiliasi',
                        'label' => 'Rekonsiliasi Data',
                        'icon' => 'database',
                        'route' => 'rekonsiliasi',
                    ],
                    ['id' => 'transaksi', 'label' => 'Data Transaksi', 'icon' => 'receipt', 'route' => 'transaksi'],
                    ['id' => 'laporan', 'label' => 'Laporan', 'icon' => 'file-text', 'route' => 'laporan'],
                ];
            @endphp

            @foreach ($menuItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="{{ ($activeMenu ?? '') == $item['id'] ? 'flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm font-medium bg-green-600 text-white shadow-sm' : 'flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">

                    <div class="flex items-center gap-3">
                        <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
                        {{ $item['label'] }}
                    </div>

                    {{-- Notifikasi khusus untuk menu verifikasi --}}
                    @if ($item['id'] === 'verifikasi' && ($pendingCount ?? 0) > 0)
                        <span class="relative flex h-5 w-5">
                            {{-- Efek Lingkaran Berkedip --}}
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            {{-- Angka Jumlah Permintaan --}}
                            <span
                                class="relative inline-flex rounded-full h-5 w-5 bg-orange-500 text-[10px] text-white items-center justify-center font-bold">
                                {{ $pendingCount }}
                            </span>
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <hr class="border-t border-gray-200">

    <div class="p-4">
        <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3 mb-3 border border-gray-100">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-600 text-white font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex w-full items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all">
                <i data-lucide="log-out" class="h-4 w-4"></i>
                Keluar
            </button>
        </form>
    </div>
</div>
