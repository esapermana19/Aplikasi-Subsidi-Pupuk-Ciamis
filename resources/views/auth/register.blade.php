<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - ASUP Ciamis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .bg-primary {
            background-color: #16a34a;
        }

        .text-primary {
            color: #16a34a;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">
    <div class="flex flex-1 items-center justify-center p-6 bg-white overflow-y-auto">
        <div class="w-full max-w-md space-y-4 py-4">

            <div class="text-center">
                <div class="flex justify-center mb-2">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary shadow-lg">
                        <i data-lucide="user-plus" class="h-7 w-7 text-white"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">Daftar Akun Baru</h1>
                <p class="text-xs text-red-500 font-semibold mt-1">* Khusus Domisili Ciamis (NIK diawali 3207)</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6">
                <form action="{{ route('register.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700">Nama Lengkap Sesuai KTP</label>
                        <div class="relative">
                            <i data-lucide="user"
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="Masukkan Nama Lengkap" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700">NIK (16 Digit)</label>
                            <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none @error('nik') border-red-500 @enderror"
                                placeholder="3207..." required>
                            @error('nik')
                                <p class="text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none @error('email') border-red-500 @enderror"
                                placeholder="nama@email.com" required>
                            @error('email')
                                <p class="text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700">Daftar Sebagai</label>
                            <select name="role" id="role_select"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none appearance-none bg-white">
                                <option value="Petani" {{ old('role') == 'Petani' ? 'selected' : '' }}>Petani</option>
                                <option value="Mitra" {{ old('role') == 'Mitra' ? 'selected' : '' }}>Mitra (Kios/Toko)
                                </option>
                            </select>
                        </div>

                        <div class="space-y-1" id="petani_fields">
                            <label class="text-xs font-bold text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-white">
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                        </div>

                        <div class="space-y-1 hidden" id="mitra_norek_field">
                            <label class="text-xs font-bold text-gray-700">No. Rekening</label>
                            <input type="text" name="no_rek" value="{{ old('no_rek') }}"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="Nomor Rekening">
                        </div>
                    </div>

                    <div class="space-y-1 hidden" id="mitra_name_field">
                        <label class="text-xs font-bold text-gray-700">Nama Toko / Kios</label>
                        <div class="relative">
                            <i data-lucide="store"
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                            <input type="text" name="nama_mitra" value="{{ old('nama_mitra') }}"
                                class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="Contoh: Kios Pupuk Jaya">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Dropdown Kecamatan --}}
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Kecamatan</label>
                            <select id="select_kecamatan" name="id_kecamatan"
                                class="w-full pl-3 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">Pilih Kecamatan</option>
                                @foreach ($kecamatans as $kec)
                                    <option value="{{ $kec->id_kecamatan }}">{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Desa --}}
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Desa</label>
                            <select id="select_desa" name="id_desa"
                                class="w-full pl-3 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">Pilih Desa</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700">Alamat Lengkap di Ciamis</label>
                        <textarea name="alamat" rows="2"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                            placeholder="Jl. Raya Ciamis No..." required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700">Password</label>
                            <input type="password" name="password"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700">Konfirmasi</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                required>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition duration-300 shadow-md text-sm mt-2">
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}"
                    class="text-green-600 font-bold hover:underline">Masuk</a>
            </p>
        </div>
    </div>

    <div
        class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-green-600 to-green-800 p-10 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 30px 30px;">
        </div>
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
                    <p class="text-base opacity-90">Platform terintegrasi untuk mengelola distribusi pupuk bersubsidi
                        kepada petani di Kabupaten Ciamis.</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-white/20">
                    <img src="{{asset('assets/images/sawah4.jpg')}}"
                        alt="Pertanian" class="w-full h-56 object-cover">
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const roleSelect = document.getElementById('role_select');
        const petaniFields = document.getElementById('petani_fields');
        const mitraNameField = document.getElementById('mitra_name_field');
        const mitraNorekField = document.getElementById('mitra_norek_field');

        function toggleFields(role) {
            if (role === 'Mitra') {
                petaniFields.classList.add('hidden');
                mitraNameField.classList.remove('hidden');
                mitraNorekField.classList.remove('hidden');
            } else {
                petaniFields.classList.remove('hidden');
                mitraNameField.classList.add('hidden');
                mitraNorekField.classList.add('hidden');
            }
        }

        // Jalankan saat pertama kali load (untuk menangani old value)
        toggleFields(roleSelect.value);

        roleSelect.addEventListener('change', function() {
            toggleFields(this.value);
        });

        // Logika untuk memuat desa berdasarkan kecamatan
        document.getElementById('select_kecamatan').addEventListener('change', function() {
            const idKecamatan = this.value;
            const desaSelect = document.getElementById('select_desa');

            // Reset Dropdown Desa
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (idKecamatan) {
                fetch(`/get-desa/${idKecamatan}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(desa => {
                            const option = document.createElement('option');
                            option.value = desa.id_desa;
                            option.text = desa.nama_desa;
                            desaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching desa:', error));
            }
        });
    </script>
</body>

</html>
