<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <h1 class="font-bold text-lg text-gray-800 capitalize">{{ str_replace('-', ' ', $activeMenu ?? 'Dashboard') }}</h1>
            <div class="w-10 lg:hidden"></div> <!-- Spacer for center alignment on mobile if needed -->
        </header>

        <main class="p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
