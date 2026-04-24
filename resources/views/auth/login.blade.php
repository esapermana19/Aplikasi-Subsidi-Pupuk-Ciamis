<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ASUP Ciamis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .bg-primary { background-color: #16a34a; }
        .text-primary { color: #16a34a; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden"> <div class="flex flex-1 items-center justify-center p-6 bg-white">
        <div class="w-full max-w-md space-y-5"> <div class="text-center">
                <div class="flex justify-center mb-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary shadow-lg">
                        <i data-lucide="leaf" class="h-9 w-9 text-white"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Sistem Subsidi Pupuk</h1>
                <p class="text-gray-500 text-sm">Kabupaten Ciamis</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-7">
                <h2 class="text-xl font-bold text-gray-800">Masuk ke Sistem</h2>
                <p class="text-gray-500 text-sm mb-5">Masukkan email dan password untuk melanjutkan</p>

                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-2.5 rounded-lg mb-4 text-xs flex items-center gap-2">
                        <i data-lucide="alert-circle" class="h-4 w-4"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                            <input type="text" name="email" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none transition text-sm" placeholder="Masukkan Email Anda" required>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                            <input type="password" name="password" id="password" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none transition text-sm" placeholder="********" required>
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i data-lucide="eye" id="eye-icon" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="rounded border-gray-300 text-green-600">
                            <span class="text-gray-500 font-medium">Ingat saya</span>
                        </label>
                        <a href="#" class="text-green-600 font-semibold hover:underline">Lupa password?</a>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition duration-300 shadow-md text-sm">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500">
                Belum punya akun? <a href="{{ route('register') }}" class="text-green-600 font-bold hover:underline">Daftar Sekarang</a>
            </p>
            <p class="text-center text-[11px] text-gray-400">
                © 2026 Pemerintah Kabupaten Ciamis. All rights reserved.
            </p>
        </div>
    </div>

    <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-green-600 to-green-800 p-10 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative z-10 flex flex-col justify-between w-full h-full">
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i data-lucide="leaf" class="h-7 w-7"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold leading-none">Subsidi Pupuk</h2>
                        <p class="text-xs opacity-80">Kabupaten Ciamis</p>
                    </div>
                </div>

                <div class="space-y-4 max-w-md">
                    <h3 class="text-3xl font-bold leading-tight">Sistem Informasi Manajemen Subsidi Pupuk</h3>
                    <p class="text-base opacity-90">Platform terintegrasi untuk mengelola distribusi pupuk bersubsidi kepada petani di Kabupaten Ciamis.</p>
                </div>
            </div>

            <div class="mt-4">
                <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-white/20">
                    <img src="{{ asset('assets/images/sawah2.jpg') }}"
                         alt="Pertanian" class="w-full h-56 object-cover">
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
