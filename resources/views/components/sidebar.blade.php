<head>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</head>
@props(['activeMenu', 'pendingCount'])

<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
    class="flex h-screen w-64 flex-col border-r bg-white fixed left-0 top-0 z-50 transition-transform duration-300 lg:translate-x-0">
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
                // Ambil role user dan ubah ke huruf kecil untuk menghindari typo
                $userRole = strtolower(auth()->user()->role);
                $menuItems = [];

                // 1. MENU UNTUK ADMIN & SUPERADMIN
                if ($userRole === 'admin' || $userRole === 'superadmin') {
                    $menuItems = [
                        [
                            'id' => 'dashboard',
                            'label' => 'Dashboard',
                            'icon' => 'layout-dashboard',
                            'route' => 'admin.dashboard',
                        ],
                        [
                            'id' => 'verifikasi',
                            'label' => 'Verifikasi Akun',
                            'icon' => 'user-check',
                            'route' => 'admin.verifikasi',
                        ],
                        [
                            'id' => 'petani',
                            'label' => 'Kelola Akun Petani',
                            'icon' => 'users',
                            'route' => 'admin.list_petani',
                        ],
                        [
                            'id' => 'mitra',
                            'label' => 'Kelola Akun Mitra',
                            'icon' => 'building-2',
                            'route' => 'admin.list_mitra',
                        ],
                        ['id' => 'pupuk', 'label' => 'Kelola Pupuk', 'icon' => 'leaf', 'route' => 'admin.pupuk.index'],
                        [
                            'id' => 'approval-permintaan',
                            'label' => 'Approval Permintaan',
                            'icon' => 'file-check',
                            'route' => 'admin.approval_permintaan',
                        ],
                        [
                            'id' => 'approval-pencairan',
                            'label' => 'Approval Pencairan',
                            'icon' => 'dollar-sign',
                            'route' => 'admin.approval-pencairan',
                        ],
                        [
                            'id' => 'rekonsiliasi',
                            'label' => 'Rekonsiliasi Data',
                            'icon' => 'database',
                            'route' => 'admin.rekonsiliasi',
                        ],
                        [
                            'id' => 'transaksi',
                            'label' => 'Data Transaksi',
                            'icon' => 'receipt',
                            'route' => 'admin.transaksi',
                        ],
                        ['id' => 'laporan', 'label' => 'Laporan', 'icon' => 'file-text', 'route' => 'admin.laporan'],
                    ];
                }
                // 2. MENU UNTUK MITRA
                elseif ($userRole === 'mitra') {
                    $menuItems = [
                        [
                            'id' => 'mitra.dashboard',
                            'label' => 'Dashboard',
                            'icon' => 'layout-dashboard',
                            'route' => 'mitra.dashboard',
                        ],
                        [
                            'id' => 'manajemen_pupuk',
                            'label' => 'Manajemen Pupuk',
                            'icon' => 'package',
                            'is_dropdown' => true,
                            'sub_menu' => [
                                [
                                    'id' => 'mitra.pupuk_tersedia',
                                    'label' => 'Pupuk Tersedia',
                                    'icon' => 'package', // Coba ganti ke 'package' jika 'package-check' tidak muncul
                                    'route' => 'mitra.pupuk_tersedia',
                                ],
                                [
                                    'id' => 'mitra.riwayat_permintaan',
                                    'label' => 'Riwayat Permintaan',
                                    'icon' => 'clock', // 'clock' lebih universal dibanding 'history' di beberapa versi
                                    'route' => 'mitra.riwayat_permintaan',
                                ],
                            ],
                        ],
                        ['id' => 'mitra.scan', 'label' => 'Scan QR', 'icon' => 'qr-code', 'route' => 'mitra.scan'],
                        [
                            'id' => 'mitra.transaksi',
                            'label' => 'Transaksi',
                            'icon' => 'receipt',
                            'route' => 'mitra.transaksi',
                        ],
                        [
                            'id' => 'mitra.pencairan',
                            'label' => 'Pencairan',
                            'icon' => 'banknote',
                            'route' => 'mitra.pencairan',
                        ],
                        [
                            'id' => 'mitra.tarik_saldo',
                            'label' => 'Tarik Saldo',
                            'icon' => 'wallet',
                            'route' => 'mitra.tarik_saldo',
                        ],
                        [
                            'id' => 'mitra.laporan',
                            'label' => 'Laporan',
                            'icon' => 'file-text',
                            'route' => 'mitra.laporan',
                        ],
                    ];
                }
                // 3. MENU UNTUK PETANI
                elseif ($userRole === 'petani') {
                    $menuItems = [
                        [
                            'id' => 'petani.dashboard',
                            'label' => 'Dashboard',
                            'icon' => 'layout-dashboard',
                            'route' => 'petani.dashboard',
                        ],
                        [
                            'id' => 'petani.beli_pupuk',
                            'label' => 'Beli Pupuk',
                            'icon' => 'shopping-bag',
                            'route' => 'petani.beli_pupuk',
                        ],
                        [
                            'id' => 'petani.riwayat_transaksi',
                            'label' => 'Riwayat Transaksi',
                            'icon' => 'history',
                            'route' => 'petani.riwayat_transaksi',
                        ],
                    ];
                }
            @endphp

            @foreach ($menuItems as $item)
                @php
                    $hasSubmenu = isset($item['is_dropdown']) && $item['is_dropdown'];

                    // Cek apakah salah satu sub-menu sedang aktif untuk otomatis buka dropdown
                    $isParentActive = false;
                    if ($hasSubmenu) {
                        foreach ($item['sub_menu'] as $sub) {
                            if (request()->routeIs($sub['route'] ?? '')) {
                                $isParentActive = true;
                                break;
                            }
                        }
                    }

                    $isActive = ($activeMenu ?? '') === ($item['id'] ?? '') || request()->routeIs($item['route'] ?? '');
                @endphp

                @if ($hasSubmenu)
                    {{-- Logika Dropdown dengan Alpine.js --}}
                    <div x-data="{ open: {{ $isParentActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" type="button"
                            class="flex w-full items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm font-medium {{ $isParentActive ? 'text-green-700 bg-green-50' : 'text-gray-700 hover:bg-gray-100' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
                                {{ $item['label'] }}
                            </div>
                            {{-- Icon Chevron yang berputar --}}
                            <i data-lucide="chevron-down" class="h-4 w-4 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        {{-- Isi Submenu --}}
                        <div x-show="open" x-cloak x-transition.origin.top class="pl-6 space-y-1">
                            @foreach ($item['sub_menu'] as $sub)
                                <a href="{{ Route::has($sub['route']) ? route($sub['route']) : '#' }}"
                                    class="flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs($sub['route']) ? 'text-green-600 bg-green-100' : 'text-gray-600 hover:bg-gray-100' }}">
                                    <i data-lucide="{{ $sub['icon'] ?? 'circle' }}" class="h-4 w-4"></i>
                                    {{ $sub['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Menu Biasa --}}
                    <a href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#' }}"
                        class="{{ $isActive
                            ? 'flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm font-medium bg-green-600 text-white shadow-sm'
                            : 'flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm font-medium text-gray-700 hover:bg-gray-100' }}">

                        <div class="flex items-center gap-3">
                            <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="h-5 w-5"></i>
                            {{ $item['label'] }}
                        </div>

                        {{-- Badge Notifikasi tetap dipertahankan --}}
                        @if (($item['id'] ?? '') === 'verifikasi' && ($pendingCount ?? 0) > 0)
                            <span class="relative flex h-5 w-5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-5 w-5 bg-orange-500 text-[10px] text-white items-center justify-center font-bold">
                                    {{ $pendingCount }}
                                </span>
                            </span>
                        @endif

                        @if (($item['id'] ?? '') === 'approval-permintaan' && ($pendingPermintaanCount ?? 0) > 0)
                            <span class="relative flex h-5 w-5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-5 w-5 bg-orange-500 text-[10px] text-white items-center justify-center font-bold">
                                    {{ $pendingPermintaanCount }}
                                </span>
                            </span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <hr class="border-t border-gray-200">

    <div class="p-4">
        <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3 mb-3 border border-gray-100">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-600 text-white font-bold">
                <i data-lucide="user" class="h-5 w-5 text-gray-100"></i>
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
                class="flex w-full items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all cursor-pointer">
                <i data-lucide="log-out" class="h-4 w-4"></i>
                Keluar
            </button>
        </form>
    </div>
</div>
