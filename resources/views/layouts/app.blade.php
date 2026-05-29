<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ASUP Ciamis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" 
        class="fixed inset-0 z-40 bg-black/50 transition-opacity lg:hidden">
    </div>

    <x-sidebar :activeMenu="$activeMenu ?? ''" :pendingCount="$pendingCount ?? 0" />

    <div class="lg:pl-64 flex flex-col min-h-screen transition-all duration-300">
        <header class="h-16 border-b bg-white flex items-center px-4 lg:px-8 sticky top-0 z-30 justify-between lg:justify-start">
            <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-lg text-gray-600 lg:hidden hover:bg-gray-100">
                <i data-lucide="menu" class="h-6 w-6"></i>
            </button>
            @php
                $menuTitles = [
                    // Admin & General
                    'dashboard' => 'Dashboard',
                    'petani' => 'Kelola Akun Petani',
                    'mitra' => 'Kelola Akun Mitra',
                    'pupuk' => 'Kelola Pupuk',
                    'verifikasi' => 'Verifikasi Akun',
                    'approval-permintaan' => 'Permintaan Pupuk',
                    'transaksi' => 'Data Transaksi',
                    'permintaan-penarikan' => 'Permintaan Penarikan',
                    'rekonsiliasi' => 'Rekonsiliasi Data',
                    'laporan' => 'Laporan',
                    'log-activity' => 'Log Aktivitas',
                    'profile' => 'Profil Saya',

                    // Mitra Specific
                    'mitra.dashboard' => 'Dashboard',
                    'mitra.pupuk_tersedia' => 'Pupuk Tersedia',
                    'mitra.riwayat_permintaan' => 'Riwayat Permintaan',
                    'mitra.scan' => 'Scan QR Code',
                    'mitra.riwayat' => 'Riwayat Transaksi',
                    'mitra.saldo' => 'Saldo Saya',
                    'mitra.laporan' => 'Laporan Penjualan',

                    // Petani Specific
                    'petani.dashboard' => 'Dashboard',
                    'petani.beli_pupuk' => 'Beli Pupuk',
                    'petani.riwayat_transaksi' => 'Riwayat Transaksi',

                    // Superadmin Specific
                    'superadmin.manage_admin' => 'Kelola Admin',
                    'regulasi' => 'Regulasi Musim',
                ];
                $pageTitle = $menuTitles[$activeMenu ?? ''] ?? str_replace(['-', '_', '.'], ' ', $activeMenu ?? 'Dashboard');
            @endphp
            <h1 class="font-bold text-lg text-gray-800 capitalize">{{ $pageTitle }}</h1>
            <div class="w-10 lg:hidden"></div> <!-- Spacer for center alignment on mobile if needed -->
        </header>

        <main class="p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // SweetAlert2 Notifications
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                borderRadius: '1.5rem'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                showConfirmButton: true,
                borderRadius: '1.5rem'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: "{{ $errors->first() }}",
                showConfirmButton: true,
                borderRadius: '1.5rem'
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>
