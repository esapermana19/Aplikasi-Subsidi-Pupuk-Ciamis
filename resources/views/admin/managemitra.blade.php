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
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">mitra</th>
                            <th class="px-6 py-4">Status Akun</th>
                            <th class="px-6 py-4">Tanggal Daftar</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($mitra as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center border border-green-200 shadow-sm">
                                            <i data-lucide="user" class="h-5 w-5 text-green-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $p->name }}</div>
                                            {{-- Pakai nik_nip sesuai data Anda --}}
                                            <div class="text-xs text-gray-500">{{ $p->nik_nip }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
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
                                    <div class="flex justify-center gap-2">
                                        <form action="{{ route('admin.mitra.update_status', $p->id) }}" method="POST"
                                            id="form-status-{{ $p->id }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="button"
                                                onclick="confirmStatus('{{ $p->id }}', '{{ $p->status_akun == 'aktif' ? 'nonaktif' : 'aktif' }}', '{{ addslashes($p->name) }}')"
                                                class="p-2 {{ $p->status_akun == 'aktif' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition-colors">
                                                <i data-lucide="{{ $p->status_akun == 'aktif' ? 'user-minus' : 'user-check' }}"
                                                    class="h-4 w-4"></i>
                                            </button>
                                            <input type="hidden" name="status" id="input-status-{{ $p->id }}">
                                        </form>

                                        {{-- SINKRONKAN NIK DI SINI: {{ $p->nik_nip }} --}}
                                        <button type="button"
                                            onclick="openEditModal('{{ $p->id }}', '{{ $p->nik_nip }}', '{{ addslashes($p->name) }}', '{{ $p->email }}')"
                                            class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors">
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
                    <h3 class="text-lg font-bold text-gray-900">Edit Data mitra</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <form id="editForm" method="POST">
                    @csrf @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            {{-- Tambahkan name="nik_nip" di bawah ini --}}
                            <input type="text" id="edit_nik" name="nik_nip" readonly
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="edit_email" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm transition-all"
                                placeholder="Kosongkan jika tidak ingin ganti">
                            <p class="text-[10px] text-gray-400 mt-1 italic">*Minimal 8 karakter jika ingin diubah</p>
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
@endsection

@include('layouts.scripts')
