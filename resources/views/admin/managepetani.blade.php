@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Akun Petani</h1>
            <p class="text-gray-500 mt-1">Daftar seluruh petani yang terdaftar di sistem ASUP Ciamis</p>
        </div>

        {{-- Filter & Search Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.list_petani') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="filter" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <select name="status" onchange="this.form.submit()"
                            class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-violet-500 focus:border-violet-500 block pl-10 pr-10 py-2.5 shadow-sm appearance-none">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                            </option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    @if (request('search') || request('status'))
                        <a href="{{ route('admin.list_petani') }}"
                            class="flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Hapus Filter
                        </a>
                    @endif
                </form>

                <div class="hidden md:block h-8 w-px bg-gray-200 mx-2"></div>

                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    <p class="text-sm text-gray-500 font-medium">
                        Menampilkan <span class="text-gray-950 font-bold">{{ $petani->total() }}</span> total petani
                    </p>
                </div>
            </div>

            <div class="w-full md:w-80">
                <form action="{{ route('admin.list_petani') }}" method="GET" class="relative">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-2xl focus:ring-violet-500 focus:border-violet-500 text-sm shadow-sm transition-all"
                        placeholder="Cari petani...">
                </form>
            </div>
        </div>

        {{-- Tabel Petani --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Petani</th>
                            <th class="px-6 py-4">Status Akun</th>
                            <th class="px-6 py-4">Tanggal Daftar</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($petani as $p)
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
                                                {{ $p->petani->nama_petani ?? 'Belum Lengkap' }}</div>
                                            <div class="text-xs text-gray-500">{{ $p->petani->nik ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{-- STATUS TETAP DARI USER --}}
                                    @if ($p->status_akun == 'aktif')
                                        <span
                                            class="px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">Aktif</span>
                                    @elseif($p->status_akun == 'nonaktif')
                                        <span
                                            class="px-3 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full border border-gray-200">Nonaktif</span>
                                    @elseif($p->status_akun == 'pending')
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
                                        <button type="button"
                                            onclick="openDetailModal(
                                                '{{ $p->petani->nik ?? '-' }}',
                                                '{{ addslashes($p->petani->nama_petani ?? '-') }}',
                                                '{{ $p->email }}',
                                                '{{ addslashes($p->petani->alamat_petani ?? '-') }}',
                                                '{{ $p->petani->jenis_kelamin ?? '-' }}',
                                                '{{ $p->status_akun }}',
                                                '{{ $p->created_at->format('d M Y H:i') }}'
                                            )"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center"
                                            title="Detail Akun">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                        </button>

                                        {{-- 3. TOMBOL AKTIVASI/NONAKTIF (Logika Kondisional) --}}
                                        @if ($p->status_akun === 'aktif')
                                            <button type="button"
                                                onclick="confirmStatus('{{ $p->id_user }}', 'nonaktif', '{{ addslashes($p->petani->nama_petani ?? 'Petani') }}')"
                                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors flex items-center justify-center"
                                                title="Nonaktifkan Akun">
                                                <i data-lucide="user-minus" class="h-4 w-4"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                onclick="confirmStatus('{{ $p->id_user }}', 'aktif', '{{ addslashes($p->petani->nama_petani ?? 'Petani') }}')"
                                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors flex items-center justify-center"
                                                title="Aktifkan Akun">
                                                <i data-lucide="user-check" class="h-4 w-4"></i>
                                            </button>
                                        @endif

                                        {{-- 4. TOMBOL EDIT --}}
                                        <button type="button"
                                            onclick="openEditModal(
                                                '{{ $p->id_user }}',
                                                '{{ $p->petani->nik ?? '' }}',
                                                '{{ addslashes($p->petani->nama_petani ?? '') }}',
                                                '{{ $p->email }}',
                                                '{{ addslashes($p->petani->alamat_petani ?? '') }}',
                                                '{{ $p->petani->jenis_kelamin ?? '' }}'
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
                class="inline-block align-bottom bg-white rounded-3xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
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
                                class="w-full px-4 py-2 border rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_petani" id="edit_name" required
                                class="w-full px-4 py-2 border rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="edit_email" required
                                class="w-full px-4 py-2 border rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jk" required
                                class="w-full px-4 py-2 border rounded-xl">
                                <option value="L">L</option>
                                <option value="P">P</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" rows="2" class="w-full px-4 py-2 border rounded-xl"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak
                                ganti)</label>
                            <input type="password" name="password" class="w-full px-4 py-2 border rounded-xl">
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
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Detail Informasi Petani</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-xs font-semibold text-gray-400 uppercase">NIK</label>
                            <p id="det_nik" class="text-sm font-medium text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-xs font-semibold text-gray-400 uppercase">Status Akun</label>
                            <p id="det_status" class="mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-semibold text-gray-400 uppercase">Nama Lengkap</label>
                            <p id="det_nama" class="text-sm font-medium text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-semibold text-gray-400 uppercase">Email</label>
                            <p id="det_email" class="text-sm font-medium text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-1">
                            <label class="text-xs font-semibold text-gray-400 uppercase">Jenis Kelamin</label>
                            <p id="det_jk" class="text-sm font-medium text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-1">
                            <label class="text-xs font-semibold text-gray-400 uppercase">Terdaftar Pada</label>
                            <p id="det_tgl" class="text-sm font-medium text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-semibold text-gray-400 uppercase">Alamat</label>
                            <p id="det_alamat"
                                class="text-sm font-medium text-gray-900 mt-1 p-3 bg-gray-50 rounded-xl border border-gray-100">
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-right">
                    <button onclick="closeDetailModal()"
                        class="px-6 py-2 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    // 1. Fungsi Buka Modal (Sudah disesuaikan dengan struktur relasi yang baru)
    function openEditModal(id_user, nik, nama_petani, email, alamat, jenis_kelamin) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        // Sesuaikan URL action form. Pastikan '/admin/petani/update/' sesuai dengan route di web.php
        form.action = `/admin/petani/update/${id_user}`;

        // Mengisi field modal
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_name').value = nama_petani;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_jk').value = jenis_kelamin;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Mengunci scroll background
    }

    // 2. Fungsi Tutup Modal (Ini yang membuat tombol Batal dan (X) berfungsi)
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Mengembalikan scroll background
    }

    // 3. Fungsi SweetAlert untuk Konfirmasi Status
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
    function openDetailModal(nik, nama, email, alamat, jk, status, tgl) {
        document.getElementById('det_nik').innerText = nik;
        document.getElementById('det_nama').innerText = nama;
        document.getElementById('det_email').innerText = email;
        document.getElementById('det_alamat').innerText = alamat;
        document.getElementById('det_tgl').innerText = tgl;

        // Format Tampilan Jenis Kelamin
        document.getElementById('det_jk').innerText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : '-');

        // Format Badge Status
        const statusEl = document.getElementById('det_status');
        let badgeClass = "px-2 py-0.5 rounded text-xs font-bold ";
        if (status === 'aktif') badgeClass += "bg-green-100 text-green-700";
        else if (status === 'pending') badgeClass += "bg-yellow-100 text-yellow-700";
        else badgeClass += "bg-red-100 text-red-700";

        statusEl.innerHTML = `<span class="${badgeClass}">${status.toUpperCase()}</span>`;

        document.getElementById('detailModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
