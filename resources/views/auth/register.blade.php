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
    <div class="flex flex-1 items-center justify-center p-2 bg-white overflow-y-auto">
        <div class="w-full max-w-xl space-y-2 py-2">

            <div class="text-center">
                <h1 class="text-xl font-bold text-gray-900 leading-tight">Daftar Akun Baru</h1>
                <p class="text-[10px] text-red-500 font-semibold">* Khusus Domisili Ciamis (NIK diawali 3207)</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-4">
                <form action="{{ route('register.store') }}" method="POST" class="space-y-2">
                    @csrf

                    {{-- Row 1: Nama & Email --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Nama Lengkap (KTP)</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="Nama Lengkap">
                            @error('name') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="nama@email.com">
                            @error('email') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Row 2: NIK & No HP --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">NIK (16 Digit)</label>
                            <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('nik') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="3207...">
                            @error('nik') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">No. Telepon</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('no_hp') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="0812...">
                            @error('no_hp') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Row 3: Role & Kecamatan --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Daftar Sebagai</label>
                            <select name="role" id="role_select"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-white">
                                <option value="Petani" {{ old('role') == 'Petani' ? 'selected' : '' }}>Petani</option>
                                <option value="Mitra" {{ old('role') == 'Mitra' ? 'selected' : '' }}>Mitra (Kios/Toko)</option>
                            </select>
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Kecamatan</label>
                            <select id="select_kecamatan" name="id_kecamatan"
                                class="w-full px-3 py-1.5 border {{ $errors->has('id_kecamatan') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">Pilih Kecamatan</option>
                                @foreach ($kecamatans as $kec)
                                    <option value="{{ $kec->id_kecamatan }}" {{ old('id_kecamatan') == $kec->id_kecamatan ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                            @error('id_kecamatan') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Row 4: Dynamic Fields --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Petani Fields --}}
                        <div class="space-y-0.5" id="petani_fields">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Jenis Kelamin</label>
                            <select name="jenis_kelamin"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-white">
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-0.5" id="petani_nokk_field">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">No. Kartu Keluarga</label>
                            <input type="text" name="no_kk" maxlength="16" value="{{ old('no_kk') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('no_kk') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="16 Digit KK">
                            @error('no_kk') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Mitra Fields --}}
                        <div class="space-y-0.5 hidden" id="mitra_norek_field">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">No. Rekening</label>
                            <input type="text" name="no_rek" value="{{ old('no_rek') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('no_rek') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="No. Rekening">
                            @error('no_rek') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-0.5 hidden" id="mitra_name_field">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Nama Toko / Kios</label>
                            <input type="text" name="nama_mitra" value="{{ old('nama_mitra') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('nama_mitra') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="Nama Toko">
                            @error('nama_mitra') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Row 5: Desa & Alamat --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Desa</label>
                            <select id="select_desa" name="id_desa"
                                class="w-full px-3 py-1.5 border {{ $errors->has('id_desa') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">Pilih Desa</option>
                            </select>
                            @error('id_desa') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Alamat Lengkap</label>
                            <input type="text" name="alamat" value="{{ old('alamat') }}"
                                class="w-full px-3 py-1.5 border {{ $errors->has('alamat') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                placeholder="Jl. Raya...">
                            @error('alamat') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Row 6: Password --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Password</label>
                            <input type="password" name="password"
                                class="w-full px-3 py-1.5 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none">
                            @error('password') <p class="text-[9px] text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Konfirmasi</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition duration-300 shadow-md text-sm mt-1">
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-green-600 font-bold hover:underline">Masuk</a>
            </p>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-green-600 to-green-800 p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="relative z-10 flex flex-col justify-between w-full h-full">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i data-lucide="leaf" class="h-6 w-6"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold leading-none">Subsidi Pupuk</h2>
                        <p class="text-[10px] opacity-80">Kabupaten Ciamis</p>
                    </div>
                </div>
                <div class="space-y-2 max-w-sm">
                    <h3 class="text-2xl font-bold leading-tight">Sistem Informasi Manajemen Subsidi Pupuk</h3>
                    <p class="text-sm opacity-90">Platform terintegrasi untuk mengelola distribusi pupuk bersubsidi kepada petani di Kabupaten Ciamis.</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-white/20">
                    <img src="{{ asset('assets/images/sawah4.jpg') }}" alt="Pertanian" class="w-full h-48 object-cover">
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const roleSelect = document.getElementById('role_select');
        const petaniFields = document.getElementById('petani_fields');
        const petaniNokkField = document.getElementById('petani_nokk_field');
        const mitraNameField = document.getElementById('mitra_name_field');
        const mitraNorekField = document.getElementById('mitra_norek_field');

        function toggleFields(role) {
            if (role === 'Mitra') {
                petaniFields.classList.add('hidden');
                petaniNokkField.classList.add('hidden');
                mitraNameField.classList.remove('hidden');
                mitraNorekField.classList.remove('hidden');
            } else {
                petaniFields.classList.remove('hidden');
                petaniNokkField.classList.remove('hidden');
                mitraNameField.classList.add('hidden');
                mitraNorekField.classList.add('hidden');
            }
        }

        toggleFields(roleSelect.value);
        roleSelect.addEventListener('change', function() { toggleFields(this.value); });

        document.getElementById('select_kecamatan').addEventListener('change', function() {
            const idKecamatan = this.value;
            const desaSelect = document.getElementById('select_desa');
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
                    });
            }
        });
    </script>
</body>
</html>
