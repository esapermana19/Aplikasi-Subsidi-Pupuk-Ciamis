<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASUP Ciamis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-900">

    <x-sidebar :active="$activeMenu ?? 'dashboard'" />

    <div class="pl-64 flex flex-col min-h-screen">
        <header class="h-16 border-b bg-white flex items-center px-8 sticky top-0 z-10">
            <h1 class="font-bold text-lg text-gray-800 capitalize">{{ str_replace('-', ' ', $activeMenu ?? 'Dashboard') }}</h1>
        </header>

        <main class="p-8">
            @yield('content')
        </main>
    </div>

    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
