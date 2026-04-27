@extends('layouts.app')

@section('content')
    <div class="space-y-6">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo. {{ Auth::user()->admin->nama_admin ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Daftar seluruh petani yang terdaftar di sistem ASUP Ciamis</p>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <form action="{{ route('admin.list_petani') }}" method="GET" id="filterForm">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Filter Status --}}
                        <div class="relative flex-1 min-w-[140px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="filter" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <select name="status" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- Filter Kecamatan --}}
                        <div class="relative flex-1 min-w-[140px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="map" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <select name="id_kecamatan" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
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
                        <div class="relative flex-1 min-w-[140px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="map-pin" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <select name="id_desa" id="filter_desa" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
                                <option value="">Semua Desa</option>
                            </select>
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="relative w-full lg:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-violet-500 focus:border-violet-500 text-sm transition-all"
                            placeholder="Cari nama atau NIK..." onkeypress="if(event.keyCode == 13) this.form.submit();">
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <p class="text-xs text-gray-500 font-medium">
                            Total <span class="text-gray-950 font-bold">{{ $list_petani->total() }}</span> Petani
                        </p>
                    </div>

                    @if (request('search') || request('status') || request('id_kecamatan') || request('id_desa'))
                        <a href="{{ route('admin.list_petani') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Tabel Petani --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Petani</th>
                            <th class="px-6 py-4">Kecamatan</th>
                            <th class="px-6 py-4">Desa</th>
                            <th class="px-6 py-4">Status Akun</th>
                            <th class="px-6 py-4">Tanggal Daftar</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($list_petani as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center border border-green-200 shadow-sm">
                                            <i data-lucide="user" class="h-5 w-5 text-green-600"></i>
                                        </div>
                                        <div>
                                            {{-- AMBIL DARI RELASI PETANI --}}
                                            <div class="font-semibold text-gray-900">
                                                {{ $p->nama_petani ?? 'Belum Lengkap' }}</div>
                                            <div class="text-xs text-gray-500">{{ $p->nik ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $p->kecamatan->nama_kecamatan ?? 'Belum Lengkap' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $p->desa->nama_desa ?? 'Belum Lengkap' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{-- STATUS TETAP DARI USER --}}
                                    @if ($p->user->status_akun == 'aktif')
                                        <span
                                            class="px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">Aktif</span>
                                    @elseif($p->user->status_akun == 'nonaktif')
                                        <span
                                            class="px-3 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full border border-gray-200">Nonaktif</span>
                                    @elseif($p->user->status_akun == 'pending')
                                        <span
                                            class="px-3 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-200">Pending</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-bold bg-red-50 text-red-700 rounded-full border border-red-200">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $p->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2 items-center">
                                        {{-- 1. FORM TERSEMBUNYI (Sama seperti Mitra) --}}
                                        <form id="form-status-{{ $p->id_user }}"
                                            action="{{ route('admin.update_status', $p->id_user) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" id="input-status-{{ $p->id_user }}"
                                                value="{{ $p->status_akun == 'aktif' ? 'nonaktif' : 'aktif' }}">
                                        </form>

                                        {{-- 2. TOMBOL DETAIL --}}
                                        <button
                                            onclick="openDetailModal(
                                                '{{ $p->nik }}',
                                                '{{ $p->nama_petani }}',
                                                '{{ $p->user->email ?? '-' }}',
                                                '{{ $p->kecamatan->nama_kecamatan ?? '-' }}',
                                                '{{ $p->desa->nama_desa ?? '-' }}',
                                                '{{ $p->alamat_petani }}',
                                                '{{ $p->jenis_kelamin }}',
                                                '{{ $p->no_kk ?? '-' }}',
                                                '{{ $p->user->status_akun ?? 'pending' }}',
                                                '{{ $p->created_at->format('d M Y') }}'
                                            )"
                                            class="text-violet-600 hover:text-violet-900">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                        </button>

                                        {{-- 3. TOMBOL AKTIVASI/NONAKTIF (Logika Kondisional) --}}
                                        @if ($p->status_akun === 'aktif')
                                            <button type="button"
                                                onclick="confirmStatus('{{ $p->id_user }}', 'nonaktif', '{{ addslashes($p->nama_petani ?? 'Petani') }}')"
                                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors flex items-center justify-center"
                                                title="Nonaktifkan Akun">
                                                <i data-lucide="user-minus" class="h-4 w-4"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                onclick="confirmStatus('{{ $p->id_user }}', 'aktif', '{{ addslashes($p->nama_petani ?? 'Petani') }}')"
                                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors flex items-center justify-center"
                                                title="Aktifkan Akun">
                                                <i data-lucide="user-check" class="h-4 w-4"></i>
                                            </button>
                                        @endif

                                        {{-- 4. TOMBOL EDIT --}}
                                        <button type="button"
                                            onclick="openEditModal(
                                                '{{ $p->id_user }}',
                                                '{{ $p->nik ?? '' }}',
                                                '{{ addslashes($p->nama_petani ?? '') }}',
                                                '{{ $p->user->email ?? '' }}',
                                                '{{ $p->id_kecamatan ?? '' }}',
                                                '{{ $p->id_desa ?? '' }}',
                                                '{{ addslashes($p->alamat_petani ?? '') }}',
                                                '{{ $p->jenis_kelamin ?? '' }}'
                                            )"
                                            class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors flex items-center justify-center"
                                            title="Edit Data">
                                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT (Hanya Satu Saja) --}}
    <div id="editModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeEditModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data Petani</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <form id="editForm" method="POST">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <input type="text" name="nik" id="edit_nik" required
                                class="w-full px-4 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_petani" id="edit_name" required
                                class="w-full px-4 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="edit_email" required
                                class="w-full px-4 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jk" required
                                class="w-full px-4 py-2 border rounded-md">
                                <option value="L">L</option>
                                <option value="P">P</option>
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                            {{-- Tambahkan event onchange untuk memicu pengambilan data desa --}}
                            <select name="id_kecamatan" id="edit_kecamatan" onchange="loadDesaEdit(this.value)" required
                                class="w-full rounded-md border border-gray-200 px-4 py-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 transition-all">
                                <option value="">Pilih Kecamatan</option>
                                {{-- Looping data kecamatan dari Controller --}}
                                @foreach ($kecamatans as $kec)
                                    <option value="{{ $kec->id_kecamatan }}">{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
                            <select name="id_desa" id="edit_desa" required
                                class="w-full rounded-md border border-gray-200 px-4 py-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 transition-all">
                                <option value="">Pilih Desa</option>
                                {{-- Opsi desa akan diisi otomatis oleh JavaScript di bawah --}}
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" rows="2" class="w-full px-4 py-2 border rounded-md"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak
                                ganti)</label>
                            <input type="password" name="password" class="w-full px-4 py-2 border rounded-md">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-2xl hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-violet-600 rounded-2xl hover:bg-violet-700 shadow-lg shadow-violet-200 transition-all">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Detail --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                {{-- Modal Header --}}
                <div class="relative px-6 py-4 bg-gradient-to-br from-green-600 to-green-700 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 h-16 w-16 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Detail Petani</h3>
                            <p class="text-green-100 text-[10px] mt-0.5 font-medium">Informasi lengkap profil dan akun</p>
                        </div>
                        <button onclick="closeDetailModal()" class="p-1.5 hover:bg-white/20 rounded-lg transition-colors text-white/80 hover:text-white">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Profile Header Section --}}
                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="h-14 w-14 rounded-xl bg-violet-100 border border-violet-200 flex items-center justify-center text-violet-600 shrink-0">
                            <i data-lucide="user" class="h-7 w-7"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 id="det_nama" class="text-lg font-bold text-gray-900 truncate"></h4>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                <span id="det_status_badge" class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider border"></span>
                                <span class="text-gray-400 text-[10px] flex items-center gap-1 font-medium">
                                    <i data-lucide="calendar" class="h-3 w-3"></i>
                                    <span id="det_tgl"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        {{-- Identity Section --}}
                        <div class="space-y-3">
                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                Identitas Diri
                            </h5>
                            
                            <div class="space-y-2.5">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="credit-card" class="h-2.5 w-2.5"></i> NIK
                                    </label>
                                    <p id="det_nik" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="layers" class="h-2.5 w-2.5"></i> No. Kartu Keluarga
                                    </label>
                                    <p id="det_noKk" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="user-plus" class="h-2.5 w-2.5"></i> Jenis Kelamin
                                    </label>
                                    <p id="det_jk" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Contact & Address Section --}}
                        <div class="space-y-3">
                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                Kontak & Wilayah
                            </h5>

                            <div class="space-y-2.5">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="mail" class="h-2.5 w-2.5"></i> Email
                                    </label>
                                    <p id="det_email" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm truncate"></p>
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
                            <i data-lucide="navigation" class="h-2.5 w-2.5"></i> Alamat Lengkap
                        </label>
                        <div id="det_alamat" class="text-xs font-medium text-gray-700 bg-violet-50/50 p-3 rounded-xl border border-violet-100 leading-relaxed italic"></div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="closeDetailModal()"
                        class="w-full sm:w-auto px-10 py-2.5 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700 transition-all shadow-md shadow-violet-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    // 1. Fungsi Buka Modal Edit
    // PERBAIKAN: Ubah nama parameter menjadi id_kecamatan dan id_desa
    function openEditModal(id_user, nik, nama_petani, email, id_kecamatan, id_desa, alamat, jenis_kelamin) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        form.action = `/admin/petani/update/${id_user}`;

        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_name').value = nama_petani;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_kecamatan').value = id_kecamatan;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_jk').value = jenis_kelamin;

        // Panggil fetch desa
        loadDesaEdit(id_kecamatan, id_desa);

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
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

    // 2. Fungsi Tutup Modal
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // 3. Fungsi SweetAlert
    function confirmStatus(userId, status, userName) {
        const actionText = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
        const confirmColor = status === 'aktif' ? '#10b981' : '#f59e0b';

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: `Apakah Anda yakin ingin ${actionText} akun ${userName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            borderRadius: '1.5rem',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('input-status-' + userId).value = status;
                document.getElementById('form-status-' + userId).submit();
            }
        });
    }

    // 4. Render Icon Lucide
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // 5. Fungsi Modal Detail
    function openDetailModal(nik, nama, email, kecamatan, desa, alamat, jk, noKk, status, tgl) {
        document.getElementById('det_nik').innerText = nik;
        document.getElementById('det_nama').innerText = nama;
        document.getElementById('det_email').innerText = email;
        document.getElementById('det_kecamatan').innerText = kecamatan;
        document.getElementById('det_desa').innerText = desa;
        document.getElementById('det_alamat').innerText = alamat;
        document.getElementById('det_tgl').innerText = tgl;
        document.getElementById('det_noKk').innerText = noKk;
        document.getElementById('det_jk').innerText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : '-');

        const badge = document.getElementById('det_status_badge');
        badge.innerText = status.toUpperCase();
        
        // Reset classes
        badge.className = "px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border";
        
        if (status === 'aktif') {
            badge.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
        } else if (status === 'pending') {
            badge.classList.add('bg-amber-50', 'text-amber-700', 'border-amber-200');
        } else {
            badge.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
        }

        document.getElementById('detailModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
