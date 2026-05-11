@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Regulasi Musim</h1>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi jatah pupuk dan periode musim tanam aktif</p>
        </div>
        <div>
            <button onclick="openAddModal()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-green-700 transition-all active:scale-95">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Tambah Musim Baru
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i data-lucide="calendar" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Musim Aktif</p>
                <p class="text-lg font-bold text-gray-900">
                    {{ $musims->where('is_active', true)->first()->nama_musim ?? 'Tidak Ada' }}
                </p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i data-lucide="boxes" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota Global</p>
                <p class="text-lg font-bold text-gray-900">
                    {{ number_format($musims->where('is_active', true)->first()->kuota_global ?? 0, 0, ',', '.') }} Kg
                </p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i data-lucide="user-minus" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Limit / Petani</p>
                <p class="text-lg font-bold text-gray-900">
                    {{ number_format($musims->where('is_active', true)->first()->limit_per_petani ?? 0, 0, ',', '.') }} Kg
                </p>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Nama Musim</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Periode</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Kuota Global</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Limit / Petani</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($musims as $musim)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900">{{ $musim->nama_musim }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-gray-700">{{ \Carbon\Carbon::parse($musim->tgl_mulai)->translatedFormat('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">s/d {{ \Carbon\Carbon::parse($musim->tgl_selesai)->translatedFormat('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold">
                                {{ number_format($musim->kuota_global, 0, ',', '.') }} Kg
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">
                                {{ number_format($musim->limit_per_petani, 0, ',', '.') }} Kg
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <button onclick="confirmToggle({{ $musim->id_musim }}, '{{ $musim->is_active ? 'nonaktifkan' : 'aktifkan' }}')" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all {{ $musim->is_active ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                    <div class="h-1.5 w-1.5 rounded-full {{ $musim->is_active ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></div>
                                    {{ $musim->is_active ? 'Aktif' : 'Non-aktif' }}
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditModal({{ json_encode($musim) }})" 
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                                    <i data-lucide="edit-3" class="h-4 w-4"></i>
                                </button>
                                <button onclick="confirmDelete({{ $musim->id_musim }})" 
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="info" class="h-12 w-12 mx-auto mb-3 opacity-20"></i>
                            <p class="text-sm">Belum ada data musim konfigurasi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($musims->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
            {{ $musims->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="musimModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white" id="modalTitle">Tambah Musim Baru</h3>
                        <p class="text-green-100 text-[10px] mt-1 font-medium tracking-wide uppercase">Konfigurasi parameter subsidi pupuk</p>
                    </div>
                    <button onclick="closeModal()" class="p-2 hover:bg-white/20 rounded-xl transition-all text-white">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>

            <form id="musimForm" method="POST" action="">
                @csrf
                <div id="methodField"></div>
                <div class="p-6 space-y-4">
                    {{-- Nama Musim --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Nama Musim</label>
                        <input type="text" name="nama_musim" id="musim_nama" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all text-sm font-bold text-gray-700"
                            placeholder="Contoh: Musim Tanam 1 2026">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Tanggal Mulai --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Tgl Mulai</label>
                            <input type="date" name="tgl_mulai" id="musim_tgl_mulai" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all text-sm font-bold text-gray-700">
                        </div>
                        {{-- Tanggal Selesai --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Tgl Selesai</label>
                            <input type="date" name="tgl_selesai" id="musim_tgl_selesai" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all text-sm font-bold text-gray-700">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Kuota Global --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Kuota Global (Kg)</label>
                            <input type="number" name="kuota_global" id="musim_kuota" required min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all text-sm font-bold text-gray-700"
                                placeholder="0">
                        </div>
                        {{-- Limit Per Petani --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Limit Per Petani (Kg)</label>
                            <input type="number" name="limit_per_petani" id="musim_limit" required min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all text-sm font-bold text-gray-700"
                                placeholder="0">
                        </div>
                    </div>

                    {{-- Toggle Aktif --}}
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-100 rounded-xl">
                        <div>
                            <h4 class="text-xs font-bold text-green-900">Aktifkan Musim Ini?</h4>
                            <p class="text-[10px] text-green-600">Hanya diperbolehkan satu musim yang aktif dalam satu waktu.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="musim_active" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">Batal</button>
                    <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 transition-all active:scale-95">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Toggle Form --}}
<form id="toggleForm" method="POST" class="hidden">
    @csrf
    @method('PATCH')
</form>

{{-- Hidden Delete Form --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    const modal = document.getElementById('musimModal');
    const form = document.getElementById('musimForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');

    function openAddModal() {
        modalTitle.innerText = 'Tambah Musim Baru';
        form.action = "{{ route('admin.musim.store') }}";
        methodField.innerHTML = '';
        form.reset();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(data) {
        modalTitle.innerText = 'Edit Konfigurasi Musim';
        form.action = `/superadmin/regulasi/update/${data.id_musim}`;
        methodField.innerHTML = '@method("PATCH")';
        
        document.getElementById('musim_nama').value = data.nama_musim;
        document.getElementById('musim_tgl_mulai').value = data.tgl_mulai;
        document.getElementById('musim_tgl_selesai').value = data.tgl_selesai;
        document.getElementById('musim_kuota').value = data.kuota_global;
        document.getElementById('musim_limit').value = data.limit_per_petani;
        document.getElementById('musim_active').checked = data.is_active;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmToggle(id, action) {
        Swal.fire({
            title: `Konfirmasi ${action.toUpperCase()}`,
            text: `Apakah Anda yakin ingin ${action} musim ini? Musim lain akan otomatis dinonaktifkan.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'aktifkan' ? '#16a34a' : '#d33',
            confirmButtonText: `Ya, ${action}!`,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const toggleForm = document.getElementById('toggleForm');
                toggleForm.action = `/superadmin/regulasi/toggle/${id}`;
                toggleForm.submit();
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Musim?',
            text: "Data yang sudah terhapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/superadmin/regulasi/delete/${id}`;
                deleteForm.submit();
            }
        });
    }
</script>
@endpush
@endsection
