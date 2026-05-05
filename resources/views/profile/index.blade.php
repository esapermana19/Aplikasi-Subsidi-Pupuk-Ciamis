@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div class="p-3 bg-green-600 rounded-2xl shadow-lg shadow-green-200">
            <i data-lucide="user" class="h-6 w-6 text-white"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-sm text-gray-500">Kelola informasi pribadi dan keamanan akun Anda</p>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-100 rounded-2xl animate-in fade-in slide-in-from-top-4 duration-300">
        <i data-lucide="check-circle" class="h-5 w-5 text-green-600"></i>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left: Profile Card --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-24 bg-gradient-to-br from-green-500 to-emerald-600"></div>
                <div class="px-6 pb-6 text-center">
                    <div class="relative -mt-12 mb-4 inline-block">
                        <div class="h-24 w-24 rounded-full bg-white p-1 shadow-lg">
                            <div class="h-full w-full rounded-full bg-gray-50 flex items-center justify-center text-green-600 font-bold text-3xl border border-gray-100">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="absolute bottom-0 right-0 h-6 w-6 bg-green-500 border-2 border-white rounded-full flex items-center justify-center">
                            <i data-lucide="shield-check" class="h-3 w-3 text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $user->name }}</h3>
                    @if(strtolower($user->role) === 'mitra')
                        <p class="text-[11px] text-gray-500 font-medium mt-1">Pemilik: {{ $user->mitra->nama_pemilik ?? '-' }}</p>
                    @endif
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-wider">
                        {{ $user->role }}
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-50 flex flex-col gap-3">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i data-lucide="mail" class="h-4 w-4 text-gray-400"></i>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                            <span>Bergabung {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Card --}}
            <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="info" class="h-4 w-4 text-emerald-600"></i>
                    </div>
                    <h4 class="text-sm font-bold text-emerald-900">Status Akun</h4>
                </div>
                <p class="text-xs text-emerald-700 leading-relaxed">
                    Akun Anda saat ini berstatus <span class="font-bold text-emerald-800 capitalize">{{ $user->status_akun }}</span>. Anda memiliki akses penuh ke fitur aplikasi sesuai role Anda.
                </p>
            </div>
        </div>

        {{-- Right: Forms --}}
        <div class="md:col-span-2 space-y-8">
            {{-- Update Info --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <i data-lucide="user-cog" class="h-5 w-5 text-gray-400"></i>
                        <h3 class="font-bold text-gray-900">Informasi Pribadi</h3>
                    </div>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(strtolower($user->role) === 'mitra')
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Nama Mitra (Toko/Kios)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                    <i data-lucide="store" class="h-4 w-4"></i>
                                </div>
                                <input type="text" name="nama_mitra" value="{{ old('nama_mitra', $user->mitra->nama_mitra ?? '') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium">
                            </div>
                            @error('nama_mitra') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Nama Lengkap (Pemilik)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                    <i data-lucide="user" class="h-4 w-4"></i>
                                </div>
                                <input type="text" name="nama_pemilik" value="{{ old('nama_pemilik', $user->mitra->nama_pemilik ?? '') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium">
                            </div>
                            @error('nama_pemilik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        @else
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Nama Lengkap</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                    <i data-lucide="user" class="h-4 w-4"></i>
                                </div>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium">
                            </div>
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Alamat Email</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                    <i data-lucide="mail" class="h-4 w-4"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium">
                            </div>
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-green-200 hover:shadow-green-300 hover:-translate-y-0.5">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Update Password --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        <h3 class="font-bold text-gray-900">Keamanan & Password</h3>
                    </div>
                </div>
                <form action="{{ route('profile.password') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Password Saat Ini</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                    <i data-lucide="key" class="h-4 w-4"></i>
                                </div>
                                <input type="password" name="current_password" required
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium"
                                    placeholder="Masukkan password saat ini untuk verifikasi">
                            </div>
                            @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Password Baru</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                        <i data-lucide="lock" class="h-4 w-4"></i>
                                    </div>
                                    <input type="password" name="password" required
                                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium"
                                        placeholder="Min. 8 karakter">
                                </div>
                                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Konfirmasi Password Baru</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none text-sm font-medium"
                                        placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-gray-200 hover:shadow-gray-300 hover:-translate-y-0.5">
                            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
