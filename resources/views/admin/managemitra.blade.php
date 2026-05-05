@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo. {{ Auth::user()->admin->nama_admin ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Daftar seluruh mitra yang terdaftar di sistem ASUP Ciamis</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.mitra.export', request()->query()) }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-violet-700 hover:shadow-violet-100 transition-all active:scale-95">
                    <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                    Ekspor Excel
                </a>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <form action="{{ route('admin.list_mitra') }}" method="GET" id="filterForm">
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
                            placeholder="Cari nama mitra atau pemilik..." onkeypress="if(event.keyCode == 13) this.form.submit();">
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <p class="text-xs text-gray-500 font-medium">
                            Total <span class="text-gray-950 font-bold">{{ $mitra->total() }}</span> Mitra
                        </p>
                    </div>

                    @if (request('search') || request('status') || request('id_kecamatan') || request('id_desa'))
                        <a href="{{ route('admin.list_mitra') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Tabel mitra --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                Mitra / Pemilik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kecamatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK &
                                Rekening</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mitra as $m)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 font-bold">
                                            {{ substr($m->nama_mitra ?? 'M', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $m->nama_mitra ?? 'Belum Diatur' }}</div>
                                            <div class="text-[10px] font-black tracking-wider text-violet-600 bg-violet-50 px-2 py-0.5 rounded border border-violet-100 inline-block mt-0.5 mb-0.5">
                                                No: {{ $m->nomor_mitra ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">Pemilik:
                                                {{ $m->nama_pemilik ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- PERBAIKAN 1: Panggil nama kecamatan melalui relasi --}}
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $m->kecamatan->nama_kecamatan ?? 'Belum Lengkap' }}
                                </td>

                                {{-- PERBAIKAN 2: Panggil nama desa melalui relasi --}}
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $m->desa->nama_desa ?? 'Belum Lengkap' }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium italic">{{ $m->nik ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Rek: {{ $m->no_rek ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass =
                                            [
                                                'aktif' => 'bg-green-100 text-green-700',
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'nonaktif' => 'bg-red-100 text-red-700',
                                            ][$m->user->status_akun] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                        {{ strtoupper($m->user->status_akun) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        {{-- Tombol Detail --}}
                                        <button type="button"
                                            onclick="openDetailModal(
                                            '{{ $m->nik ?? '-' }}',
                                            '{{ addslashes($m->nama_mitra ?? '-') }}',
                                            '{{ $m->user->email ?? '-' }}',
                                            '{{ addslashes($m->alamat_mitra ?? '-') }}',
                                            '{{ addslashes($m->nama_pemilik ?? '-') }}',
                                            '{{ $m->no_rek ?? '-' }}',
                                            '{{ $m->kecamatan->nama_kecamatan ?? '-' }}', 
                                            '{{ $m->desa->nama_desa ?? '-' }}',
                                            '{{ $m->user->status_akun }}',
                                            '{{ $m->nomor_mitra ?? '-' }}')"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Detail">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                        </button>

                                        {{-- Tombol Aktivasi/Nonaktifkan --}}
                                        <form id="form-status-{{ $m->id_user }}"
                                            action="{{ route('admin.update_status', $m->id_user) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" id="input-status-{{ $m->id_user }}">
                                        </form>

                                        @if ($m->status_akun === 'aktif')
                                            <button type="button"
                                                onclick="confirmStatus('{{ $m->id_user }}', 'nonaktif', '{{ addslashes($m->mitra->nama_mitra ?? 'Mitra') }}')"
                                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                title="Nonaktifkan Akun">
                                                <i data-lucide="user-x" class="h-4 w-4"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                onclick="confirmStatus('{{ $m->id_user }}', 'aktif', '{{ addslashes($m->mitra->nama_mitra ?? 'Mitra') }}')"
                                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                                title="Aktifkan Akun">
                                                <i data-lucide="user-check" class="h-4 w-4"></i>
                                            </button>
                                        @endif

                                        {{-- Tombol Edit --}}
                                        <button type="button"
                                            onclick="openEditModal(
                                            '{{ $m->id_user }}',
                                            '{{ $m->nik ?? '' }}',
                                            '{{ addslashes($m->nama_mitra ?? '') }}',
                                            '{{ $m->user->email ?? '-' }}',
                                            '{{ addslashes($m->alamat_mitra ?? '') }}',
                                            '{{ addslashes($m->nama_pemilik ?? '') }}',
                                            '{{ $m->no_rek ?? '' }}',
                                            '{{ $m->id_kecamatan ?? '' }}', {{-- PERBAIKAN 3: Kirim ID Kecamatan --}}
                                            '{{ $m->id_desa ?? '' }}')"
                                            {{-- PERBAIKAN 3: Kirim ID Desa --}}
                                            class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Colspan diubah ke 6 sesuai jumlah kolom tabel Anda --}}
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data mitra tidak
                                    ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $mitra->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form id="editForm" method="POST">
                    @csrf @method('PATCH')
                    
                    {{-- Modal Header --}}
                    <div class="relative px-6 py-5 bg-gradient-to-br from-violet-600 to-violet-700 text-white overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 h-16 w-16 bg-white/10 rounded-full blur-xl"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold">Edit Data Mitra</h3>
                                <p class="text-violet-100 text-[10px] mt-1 font-medium tracking-wide">Perbarui informasi kemitraan secara berkala</p>
                            </div>
                            <button type="button" onclick="closeEditModal()" class="p-2 hover:bg-white/20 rounded-xl transition-all">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-5 bg-white">
                        <div class="grid grid-cols-2 gap-5">
                            {{-- NIK --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">NIK Pemilik</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="credit-card" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="nik" id="edit_nik"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Masukkan NIK">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Email Mitra</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="mail" class="h-4 w-4 text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" id="edit_email"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-400 text-sm font-bold cursor-not-allowed"
                                        readonly>
                                </div>
                            </div>

                            {{-- Nama Mitra --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Nama Mitra/Instansi</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="store" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="nama_mitra" id="edit_name"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Contoh: Kios Pupuk Maju Jaya">
                                </div>
                            </div>

                            {{-- Nama Pemilik --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Nama Pemilik</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="user" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="nama_pemilik" id="edit_pemilik"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Nama Lengkap">
                                </div>
                            </div>

                            {{-- No Rekening --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">No. Rekening</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="wallet" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="no_rek" id="edit_rek"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Nomor Rekening">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Password Baru (Opsional)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="lock" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password" id="edit_password"
                                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <button type="button" onclick="togglePassword('edit_password', 'toggle_icon_edit')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i id="toggle_icon_edit" data-lucide="eye" class="h-4 w-4 text-gray-400 hover:text-violet-500 transition-colors"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Kecamatan --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Kecamatan</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="map" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <select name="id_kecamatan" id="edit_kecamatan" onchange="loadDesaEdit(this.value)"
                                        required
                                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700 appearance-none">
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach ($kecamatans as $kec)
                                            <option value="{{ $kec->id_kecamatan }}">{{ $kec->nama_kecamatan }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Desa --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Desa</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="map-pin" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <select name="id_desa" id="edit_desa" required
                                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700 appearance-none">
                                        <option value="">Pilih Desa</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Alamat Lengkap Kios</label>
                                <div class="relative group">
                                    <div class="absolute top-3 left-3.5 pointer-events-none">
                                        <i data-lucide="navigation" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <textarea name="alamat" id="edit_alamat" rows="2"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Masukkan alamat lengkap kios..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-5 bg-gray-50/80 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all active:scale-95">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-8 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-violet-600 to-violet-700 rounded-xl hover:from-violet-700 hover:to-violet-800 transition-all shadow-lg shadow-violet-200 active:scale-95">
                            Simpan Perubahan
                        </button>
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
                <div class="relative px-6 py-4 bg-gradient-to-br from-violet-600 to-violet-700 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 h-16 w-16 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Detail Mitra</h3>
                            <p class="text-violet-100 text-[10px] mt-0.5 font-medium">Informasi profil dan operasional</p>
                        </div>
                        <button onclick="closeDetailModal()" class="p-1.5 hover:bg-white/20 rounded-lg transition-colors text-white/80 hover:text-white">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Profile Header Section --}}
                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="h-14 w-14 rounded-xl bg-green-100 border border-green-200 flex items-center justify-center text-green-600 shrink-0">
                            <i data-lucide="store" class="h-7 w-7"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 id="det_nama" class="text-lg font-bold text-gray-900 truncate"></h4>
                            <div class="text-[10px] font-black tracking-widest text-violet-600 bg-violet-100 px-2 py-0.5 rounded border border-violet-200 inline-block my-1">
                                No Mitra: <span id="det_nomor_mitra"></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                <span id="det_status_badge" class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider border"></span>
                                <span class="text-gray-400 text-[10px] flex items-center gap-1 font-medium">
                                    <i data-lucide="user-circle" class="h-3 w-3"></i>
                                    <span id="det_pemilik"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        {{-- Identity Section --}}
                        <div class="space-y-3">
                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                Legalitas & Akun
                            </h5>
                            
                            <div class="space-y-2.5">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="credit-card" class="h-2.5 w-2.5"></i> NIK Pemilik
                                    </label>
                                    <p id="det_nik" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1 mb-0.5">
                                        <i data-lucide="wallet" class="h-2.5 w-2.5"></i> No. Rekening
                                    </label>
                                    <p id="det_rek" class="text-xs font-bold text-gray-800 bg-white px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm"></p>
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
                            <i data-lucide="navigation" class="h-2.5 w-2.5"></i> Alamat Lengkap Kios
                        </label>
                        <div id="det_alamat" class="text-xs font-medium text-gray-700 bg-green-50/50 p-3 rounded-xl border border-green-100 leading-relaxed italic"></div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="closeDetailModal()"
                        class="w-full sm:w-auto px-10 py-2.5 text-sm font-bold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all shadow-md shadow-green-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    // 1. Fungsi Modal Edit (Sudah diperbaiki)
    // Parameter disesuaikan dengan urutan tombol edit Mitra
    function openEditModal(id_user, nik, nama_mitra, email, alamat, nama_pemilik, no_rek, id_kecamatan, id_desa) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        // Sesuaikan URL action-nya (ganti dengan route update mitra Anda)
        form.action = `/admin/mitra/update/${id_user}`;

        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_name').value = nama_mitra;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_pemilik').value = nama_pemilik;
        document.getElementById('edit_rek').value = no_rek;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_password').value = ''; // Reset password field


        // Set nilai Dropdown Kecamatan
        document.getElementById('edit_kecamatan').value = id_kecamatan;

        // Panggil fetch untuk mengisi Dropdown Desa secara otomatis
        loadDesaEdit(id_kecamatan, id_desa);

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
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

    // Catatan: Fungsi loadDesaEdit(...) dan event filter lainnya SAMA PERSIS
    // seperti yang ada di halaman Petani, Anda bisa langsung copy-paste saja.

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // 2. Fungsi Modal Detail (Baru)
    function openDetailModal(nik, nama_mitra, email, alamat, nama_pemilik, no_rek, kecamatan, desa, status, nomor_mitra) {
        document.getElementById('det_nik').innerText = nik;
        document.getElementById('det_rek').innerText = no_rek;
        document.getElementById('det_nama').innerText = nama_mitra;
        document.getElementById('det_pemilik').innerText = nama_pemilik;
        document.getElementById('det_email').innerText = email;
        document.getElementById('det_kecamatan').innerText = kecamatan;
        document.getElementById('det_desa').innerText = desa;
        document.getElementById('det_alamat').innerText = alamat;
        document.getElementById('det_nomor_mitra').innerText = nomor_mitra;

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

        // Munculkan Modal (Hapus class hidden)
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');

        // Kunci scroll body agar tidak bergeser saat modal tampil
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
    }

    /**
     * Fungsi untuk menutup Modal Detail
     */
    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');

        // Kembalikan scroll body
        document.body.style.overflow = 'auto';
    }

    // Opsional: Menutup modal jika area di luar kotak modal diklik
    window.onclick = function(event) {
        const detailModal = document.getElementById('detailModal');
        const editModal = document.getElementById('editModal');
        if (event.target == detailModal) closeDetailModal();
        if (event.target == editModal) closeEditModal();
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Render Icon
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        if (window.lucide) lucide.createIcons();
    }

    // 3. Fungsi Konfirmasi Status (Baru)
    function confirmStatus(userId, status, userName) {
        const actionText = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
        const confirmColor = status === 'aktif' ? '#10b981' : '#f59e0b'; // Hijau untuk aktif, Amber untuk nonaktif

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
                // Set value input hidden dan submit form
                document.getElementById('input-status-' + userId).value = status;
                document.getElementById('form-status-' + userId).submit();
            }
        });
    }
</script>
