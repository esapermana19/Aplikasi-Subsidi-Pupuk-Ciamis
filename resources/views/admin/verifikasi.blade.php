@extends('layouts.app')

@section('content')
    <div class="space-y-6">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo. {{ Auth::user()->admin->nama_admin ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola verifikasi pendaftaran akun baru di sini.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Area Filter --}}
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <form action="{{ route('admin.verifikasi') }}" method="GET" id="filterForm"
                class="flex flex-col lg:flex-row lg:items-center gap-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-center gap-3 flex-1">
                    {{-- Dropdown Filter Role --}}
                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500 transition-all">
                        <i data-lucide="filter" class="h-4 w-4 text-gray-400"></i>
                        <select name="role" onchange="this.form.submit()"
                            class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0 p-0 pr-8 cursor-pointer flex-1">
                            <option value="">Semua Peran</option>
                            <option value="petani" {{ request('role') == 'petani' ? 'selected' : '' }}>Petani</option>
                            <option value="mitra" {{ request('role') == 'mitra' ? 'selected' : '' }}>Mitra</option>
                        </select>
                    </div>

                    {{-- Filter Kecamatan --}}
                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-violet-500 focus-within:border-violet-500 transition-all">
                        <i data-lucide="map" class="h-4 w-4 text-gray-400"></i>
                        <select name="id_kecamatan" onchange="this.form.submit()"
                            class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0 p-0 pr-8 cursor-pointer flex-1">
                            <option value="">Semua Kecamatan</option>
                            @foreach ($kecamatans as $kec)
                                <option value="{{ $kec->id_kecamatan }}"
                                    {{ request('id_kecamatan') == $kec->id_kecamatan ? 'selected' : '' }}>
                                    {{ $kec->nama_kecamatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Desa --}}
                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-violet-500 focus-within:border-violet-500 transition-all">
                        <i data-lucide="map-pin" class="h-4 w-4 text-gray-400"></i>
                        <select name="id_desa" id="filter_desa" onchange="this.form.submit()"
                            class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0 p-0 pr-8 cursor-pointer flex-1">
                            <option value="">Semua Desa</option>
                            {{-- Opsi Desa akan dimuat oleh JavaScript otomatis --}}
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between lg:justify-end gap-3 pt-3 lg:pt-0 lg:border-l lg:pl-4 border-gray-100">
                    {{-- Tombol Hapus Filter --}}
                    @if (request('search') || request('status') || request('id_kecamatan') || request('id_desa'))
                        <a href="{{ route('admin.verifikasi') }}"
                            class="flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset
                        </a>
                    @endif

                    <div class="flex items-center gap-2">
                        <span class="flex h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span>
                        <p class="text-xs font-medium text-gray-600">
                            <span class="text-gray-900 font-bold">{{ $users->count() }}</span> Pending
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan / Desa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-right pr-20 text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{-- Mengambil nama dari relasi petani atau mitra --}}
                                    {{ $user->petani->nama_petani ?? ($user->mitra->nama_mitra ?? 'Tidak ada nama') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{-- Mengambil NIK dari relasi petani atau mitra --}}
                                    {{ $user->petani->nik ?? ($user->mitra->nik ?? '-') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{-- Mengambil nama dari relasi petani atau mitra --}}
                                    {{ $user->petani->kecamatan->nama_kecamatan ?? ($user->mitra->kecamatan->nama_kecamatan ?? 'Tidak ada nama') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{-- Mengambil NIK dari relasi petani atau mitra --}}
                                    {{ $user->petani->desa->nama_desa ?? ($user->mitra->desa->nama_desa ?? 'Tidak ada nama') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $user->role == 'petani' ? 'bg-green-100 text-green-700' : 'bg-violet-100 text-violet-700' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    {{-- Tombol Detail Baru --}}
                                    <button type="button"
                                        onclick="openDetailVerifikasi(
                                            '{{ $user->role }}',
                                            '{{ $user->petani->nik ?? ($user->mitra->nik ?? '-') }}',
                                            '{{ addslashes($user->petani->nama_petani ?? ($user->mitra->nama_mitra ?? '-')) }}',
                                            '{{ $user->email }}',
                                            '{{ addslashes($user->petani->alamat_petani ?? ($user->mitra->alamat_mitra ?? '-')) }}',
                                            '{{ $user->petani->kecamatan->nama_kecamatan ?? ($user->mitra->kecamatan->nama_kecamatan ?? '-') }}', {{-- KECAMATAN --}}
                                            '{{ $user->petani->desa->nama_desa ?? ($user->mitra->desa->nama_desa ?? '-') }}',                   {{-- DESA --}}
                                            '{{ $user->petani->jenis_kelamin ?? '-' }}',
                                            '{{ $user->petani->no_kk ?? '-' }}',
                                            '{{ addslashes($user->mitra->nama_pemilik ?? '-') }}',
                                            '{{ $user->mitra->no_rek ?? '-' }}'
                                        )"
                                        class="text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        Detail
                                    </button>
                                    <form action="{{ route('admin.approve_akun', $user->id_user) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                                            Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reject_akun', $user->id_user) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menolak pendaftaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                                Tidak ada pendaftaran akun yang butuh verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
    {{-- Modal Detail Verifikasi --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                {{-- Modal Header --}}
                <div class="relative px-6 py-4 bg-gradient-to-br from-green-600 to-green-700 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 h-16 w-16 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Tinjau Pendaftaran <span id="title_role" class="capitalize"></span></h3>
                            <p class="text-green-100 text-[10px] mt-0.5 font-medium">Verifikasi data akun baru</p>
                        </div>
                        <button onclick="closeModal()" class="p-1.5 hover:bg-white/20 rounded-lg transition-colors text-white/80 hover:text-white">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Profile Header Section --}}
                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div id="role_icon_container" class="h-14 w-14 rounded-xl bg-violet-100 border border-violet-200 flex items-center justify-center text-violet-600 shrink-0">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 id="det_nama" class="text-lg font-bold text-gray-900 truncate"></h4>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider border bg-orange-50 text-orange-700 border-orange-200">
                                    Menunggu Verifikasi
                                </span>
                                <span class="text-gray-400 text-[10px] flex items-center gap-1 font-medium">
                                    <i data-lucide="mail" class="h-3 w-3"></i>
                                    <span id="det_email" class="truncate"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        {{-- Identity Column --}}
                        <div class="space-y-3">
                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                Informasi Identitas
                            </h5>
                            
                            <div class="space-y-2.5">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="credit-card" class="h-2.5 w-2.5"></i> NIK
                                    </label>
                                    <p id="det_nik" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>

                                {{-- Petani Specific --}}
                                <div id="box_noKk">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="layers" class="h-2.5 w-2.5"></i> No. Kartu Keluarga
                                    </label>
                                    <p id="det_noKk" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>

                                {{-- Mitra Specific --}}
                                <div id="box_mitra_owner" class="hidden">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="user-circle" class="h-2.5 w-2.5"></i> Nama Pemilik
                                    </label>
                                    <p id="det_pemilik" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Info Column --}}
                        <div class="space-y-3">
                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                Informasi Pendukung
                            </h5>

                            <div class="space-y-2.5">
                                {{-- Petani Specific --}}
                                <div id="box_jk">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="user-plus" class="h-2.5 w-2.5"></i> Jenis Kelamin
                                    </label>
                                    <p id="det_jk" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>

                                {{-- Mitra Specific --}}
                                <div id="box_mitra_rek" class="hidden">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="wallet" class="h-2.5 w-2.5"></i> No. Rekening
                                    </label>
                                    <p id="det_rek" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                            <i data-lucide="map" class="h-2.5 w-2.5"></i> Kecamatan
                                        </label>
                                        <p id="det_kecamatan" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm truncate"></p>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                            <i data-lucide="map-pin" class="h-2.5 w-2.5"></i> Desa
                                        </label>
                                        <p id="det_desa" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm truncate"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-1">
                            <i data-lucide="navigation" class="h-2.5 w-2.5"></i> Alamat Domisili
                        </label>
                        <div id="det_alamat" class="text-xs font-medium text-gray-700 bg-violet-50/50 p-3 rounded-xl border border-violet-100 leading-relaxed italic"></div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="closeModal()"
                        class="w-full sm:w-auto px-10 py-2.5 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700 transition-all shadow-md shadow-violet-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDetailVerifikasi(role, nik, nama, email, alamat, kecamatan, desa, jk, noKk, pemilik, rek) {
            document.getElementById('title_role').innerText = role.toUpperCase();
            document.getElementById('det_nik').innerText = nik;
            document.getElementById('det_nama').innerText = nama;
            document.getElementById('det_email').innerText = email;
            document.getElementById('det_alamat').innerText = alamat;
            document.getElementById('det_kecamatan').innerText = kecamatan;
            document.getElementById('det_desa').innerText = desa;

            const iconContainer = document.getElementById('role_icon_container');
            const boxMitraOwner = document.getElementById('box_mitra_owner');
            const boxMitraRek = document.getElementById('box_mitra_rek');
            const boxJk = document.getElementById('box_jk');
            const boxNoKk = document.getElementById('box_noKk');

            if (role.toLowerCase() === 'mitra') {
                // Tampilan Mitra
                iconContainer.innerHTML = '<i data-lucide="store" class="h-10 w-10"></i>';
                boxMitraOwner.classList.remove('hidden');
                boxMitraRek.classList.remove('hidden');
                boxJk.classList.add('hidden');
                boxNoKk.classList.add('hidden');
                document.getElementById('det_pemilik').innerText = pemilik;
                document.getElementById('det_rek').innerText = rek;
            } else {
                // Tampilan Petani
                iconContainer.innerHTML = '<i data-lucide="user" class="h-10 w-10"></i>';
                boxMitraOwner.classList.add('hidden');
                boxMitraRek.classList.add('hidden');
                boxJk.classList.remove('hidden');
                boxNoKk.classList.remove('hidden');
                document.getElementById('det_jk').innerText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : '-');
                document.getElementById('det_noKk').innerText = noKk;
            }

            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function loadDesaEdit(idKecamatan, selectedDesaId = null) {
            const desaSelect = document.getElementById('edit_desa');
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (idKecamatan) {
                fetch(`{{ url('/get-desa') }}/${idKecamatan}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(desa => {
                            const option = document.createElement('option');
                            option.value = desa.id_desa;
                            option.text = desa.nama_desa;

                            if (desa.id_desa == selectedDesaId) {
                                option.selected = true;
                            }
                            desaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching desa:', error));
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data kecamatan & desa dari URL (jika ada filter aktif)
            const activeKecamatanId = "{{ request('id_kecamatan') }}";
            const activeDesaId = "{{ request('id_desa') }}";

            const filterDesaSelect = document.getElementById('filter_desa');

            if (activeKecamatanId) {
                // Fetch ke backend untuk mengambil list desa
                fetch(`{{ url('/get-desa') }}/${activeKecamatanId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(desa => {
                            const option = document.createElement('option');
                            option.value = desa.id_desa;
                            option.text = desa.nama_desa;

                            // Tandai terpilih jika sesuai filter
                            if (desa.id_desa == activeDesaId) {
                                option.selected = true;
                            }
                            filterDesaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error memuat desa untuk filter:', error));
            }
        });

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
