@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Akun mitra</h1>
            <p class="text-gray-500 mt-1">Daftar seluruh mitra yang terdaftar di sistem ASUP Ciamis</p>
        </div>

        {{-- Filter & Search Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.list_mitra') }}" method="GET" id="filterForm" class="flex items-center gap-3">
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
                        <a href="{{ route('admin.list_mitra') }}"
                            class="flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Hapus Filter
                        </a>
                    @endif
                </form>

                <div class="hidden md:block h-8 w-px bg-gray-200 mx-2"></div>

                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    <p class="text-sm text-gray-500 font-medium">
                        Menampilkan <span class="text-gray-950 font-bold">{{ $mitra->total() }}</span> total mitra
                    </p>
                </div>
            </div>

            <div class="w-full md:w-80">
                <form action="{{ route('admin.list_mitra') }}" method="GET" class="relative">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-2xl focus:ring-violet-500 focus:border-violet-500 text-sm shadow-sm transition-all"
                        placeholder="Cari mitra...">
                </form>
            </div>
        </div>

        {{-- Tabel mitra --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                Mitra / Pemilik</th>
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
                                            {{ substr($m->mitra->nama_mitra ?? 'M', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $m->mitra->nama_mitra ?? 'Belum Diatur' }}</div>
                                            <div class="text-xs text-gray-500">Pemilik:
                                                {{ $m->mitra->nama_pemilik ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium italic">{{ $m->mitra->nik ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Rek: {{ $m->mitra->no_rek ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass =
                                            [
                                                'aktif' => 'bg-green-100 text-green-700',
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'nonaktif' => 'bg-red-100 text-red-700',
                                            ][$m->status_akun] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                        {{ strtoupper($m->status_akun) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        {{-- Tombol Detail --}}
                                        <button type="button"
                                            onclick="openDetailModal('{{ $m->mitra->nik ?? '-' }}', '{{ addslashes($m->mitra->nama_mitra ?? '-') }}', '{{ $m->email }}', '{{ addslashes($m->mitra->alamat_mitra ?? '-') }}', '{{ addslashes($m->mitra->nama_pemilik ?? '-') }}', '{{ $m->mitra->no_rek ?? '-' }}')"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Detail">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                        </button>

                                        {{-- Tombol Aktivasi/Nonaktifkan (Sama seperti Petani) --}}
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
                                            onclick="openEditModal('{{ $m->id_user }}', '{{ $m->mitra->nik ?? '' }}', '{{ addslashes($m->mitra->nama_mitra ?? '') }}', '{{ $m->email }}', '{{ addslashes($m->mitra->alamat_mitra ?? '') }}', '{{ addslashes($m->mitra->nama_pemilik ?? '') }}', '{{ $m->mitra->no_rek ?? '' }}')"
                                            class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Data mitra tidak
                                    ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT (Hanya Satu Saja) --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" onclick="closeEditModal()"></div>
            <div class="relative bg-white rounded-3xl shadow-xl max-w-lg w-full overflow-hidden">
                <form id="editForm" method="POST">
                    @csrf @method('PATCH')
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Edit Data Mitra</h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i
                                data-lucide="x" class="h-5 w-5"></i></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">NIK</label>
                                <input type="text" name="nik" id="edit_nik"
                                    class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-violet-500">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Email</label>
                                <input type="email" name="email" id="edit_email"
                                    class="w-full px-4 py-2 border rounded-xl bg-gray-50" readonly>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama
                                    Mitra/Instansi</label>
                                <input type="text" name="nama_mitra" id="edit_name"
                                    class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-violet-500">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Pemilik</label>
                                <input type="text" name="nama_pemilik" id="edit_pemilik"
                                    class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-violet-500">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">No. Rekening</label>
                                <input type="text" name="no_rek" id="edit_rek"
                                    class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-violet-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alamat</label>
                                <textarea name="alamat" id="edit_alamat" rows="2"
                                    class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-violet-500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-2 text-sm font-bold text-gray-600 bg-white border rounded-xl hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-200">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Detail --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" onclick="closeDetailModal()"></div>
            <div class="relative bg-white rounded-3xl shadow-xl max-w-lg w-full overflow-hidden">
                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Detail Informasi Mitra</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-xs font-bold text-gray-400 uppercase">NIK</label>
                            <p id="det_nik" class="text-sm font-semibold text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-xs font-bold text-gray-400 uppercase">No. Rekening</label>
                            <p id="det_rek" class="text-sm font-semibold text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-gray-400 uppercase">Nama Mitra / Instansi</label>
                            <p id="det_nama" class="text-sm font-semibold text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-gray-400 uppercase">Nama Pemilik</label>
                            <p id="det_pemilik" class="text-sm font-semibold text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-gray-400 uppercase">Email</label>
                            <p id="det_email" class="text-sm font-semibold text-gray-900 mt-1"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-gray-400 uppercase">Alamat</label>
                            <p id="det_alamat"
                                class="text-sm text-gray-700 bg-gray-50 p-4 rounded-2xl mt-1 border border-gray-100 leading-relaxed">
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-right">
                    <button onclick="closeDetailModal()"
                        class="px-6 py-2 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700 transition-all shadow-lg shadow-violet-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    // 1. Fungsi Modal Edit (Sudah diperbaiki)
    function openEditModal(id, nik, nama, email, alamat, pemilik, rek) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        form.action = `/admin/mitra/update/${id}`;

        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_name').value = nama;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_pemilik').value = pemilik;
        document.getElementById('edit_rek').value = rek;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // 2. Fungsi Modal Detail (Baru)
    function openDetailModal(nik, nama, email, alamat, pemilik, rek) {
        document.getElementById('det_nik').innerText = nik;
        document.getElementById('det_nama').innerText = nama;
        document.getElementById('det_email').innerText = email;
        document.getElementById('det_alamat').innerText = alamat;
        document.getElementById('det_pemilik').innerText = pemilik;
        document.getElementById('det_rek').innerText = rek;

        document.getElementById('detailModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
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
