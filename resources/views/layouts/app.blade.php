<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ASUP Ciamis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-green-700 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="font-bold text-xl">ASUP CIAMIS</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm">Halo, {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-10 p-4">
        @yield('content')
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
