@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Admin</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data administrator sistem ASUP Ciamis</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-green-700 hover:shadow-green-100 transition-all active:scale-95">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    Tambah Admin
                </button>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <form action="{{ route('superadmin.manage_admin') }}" method="GET" id="filterForm">
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
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                            placeholder="Cari nama, NIP, atau email..." onkeypress="if(event.keyCode == 13) this.form.submit();">
                    </div>
                </div>
            </div>
        </form>

        {{-- Tabel Admin --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Admin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email & WhatsApp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Terakhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($admins as $admin)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 font-bold">
                                            {{ substr($admin->nama_admin, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-900">{{ $admin->nama_admin }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                    {{ $admin->nip }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $admin->user->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $admin->user->no_hp ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $admin->last_login ? $admin->last_login->diffForHumans() : 'Belum pernah login' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = $admin->user->status_akun === 'aktif' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'bg-red-100 text-red-700';
                                    @endphp
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-full {{ $statusClass }}">
                                        {{ strtoupper($admin->user->status_akun) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        {{-- Toggle Status --}}
                                        <button type="button" 
                                            onclick="confirmStatus('{{ $admin->id_user }}', '{{ $admin->user->status_akun === 'aktif' ? 'nonaktif' : 'aktif' }}', '{{ addslashes($admin->nama_admin) }}')"
                                            class="p-2 {{ $admin->user->status_akun === 'aktif' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition-colors">
                                            <i data-lucide="{{ $admin->user->status_akun === 'aktif' ? 'user-x' : 'user-check' }}" class="h-4 w-4"></i>
                                        </button>

                                        {{-- Edit --}}
                                        <button type="button"
                                            onclick="openEditModal('{{ $admin->id_admin }}', '{{ $admin->nip }}', '{{ addslashes($admin->nama_admin) }}', '{{ $admin->user->email }}', '{{ $admin->user->no_hp }}')"
                                            class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors">
                                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <button type="button" onclick="confirmDelete('{{ $admin->id_admin }}', '{{ addslashes($admin->nama_admin) }}')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data admin tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $admins->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL ADD/EDIT --}}
    <div id="adminModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form id="adminForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="relative px-6 py-5 bg-gradient-to-br from-violet-600 to-violet-700 text-white overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 h-16 w-16 bg-white/10 rounded-full blur-xl"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <h3 id="modalTitle" class="text-xl font-bold">Tambah Admin Baru</h3>
                                <p class="text-violet-100 text-[10px] mt-1 font-medium tracking-wide">Lengkapi data administrator sistem</p>
                            </div>
                            <button type="button" onclick="closeModal()" class="p-2 hover:bg-white/20 rounded-xl transition-all">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-4 bg-white">
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Nama Admin --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Nama Lengkap</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="user" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="nama_admin" id="admin_nama" required
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Masukkan nama lengkap">
                                </div>
                            </div>

                            {{-- NIP --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">NIP (18 Digit)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="credit-card" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="nip" id="admin_nip" required maxlength="18"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="19xxxxxxxxxxxxxx">
                                </div>
                            </div>

                            {{-- No HP --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">No. WhatsApp</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="phone" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="no_hp" id="admin_hp"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="08xxxxxxxx">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Email Address</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="mail" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" id="admin_email" required
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="admin@example.com">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="col-span-2" id="passwordContainer">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i data-lucide="lock" class="h-4 w-4 text-gray-400 group-focus-within:text-violet-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password" id="admin_password"
                                        class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 transition-all text-sm font-bold text-gray-700"
                                        placeholder="Masukkan password">
                                    <button type="button" onclick="togglePassword('admin_password', 'toggle_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i id="toggle_icon" data-lucide="eye" class="h-4 w-4 text-gray-400 hover:text-violet-500 transition-colors"></i>
                                    </button>
                                </div>
                                <p id="passwordHelp" class="hidden text-[10px] text-gray-400 mt-1 italic">Kosongkan jika tidak ingin mengubah password</p>
                                <p id="defaultPasswordNote" class="hidden text-[10px] text-violet-500 mt-1 font-bold italic">Password default: adminasup123</p>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-5 bg-gray-50/80 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-8 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-green-600 to-green-700 rounded-xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-100">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Form Toggle Status --}}
    <form id="form-status" action="" method="POST" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" id="input-status">
    </form>

    {{-- Form Delete --}}
    <form id="form-delete" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        const modal = document.getElementById('adminModal');
        const form = document.getElementById('adminForm');
        const methodInput = document.getElementById('formMethod');
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        document.getElementById('modalTitle').innerText = 'Tambah Admin Baru';
        document.getElementById('passwordContainer').classList.add('hidden');
        document.getElementById('defaultPasswordNote').classList.remove('hidden');
        document.getElementById('admin_password').required = false;
        
        form.action = "{{ route('superadmin.admin.store') }}";
        methodInput.value = "POST";
        
        form.reset();
        if (window.lucide) lucide.createIcons();
    }

    function openEditModal(id, nip, nama, email, hp) {
        const modal = document.getElementById('adminModal');
        const form = document.getElementById('adminForm');
        const methodInput = document.getElementById('formMethod');
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        document.getElementById('modalTitle').innerText = 'Edit Data Admin';
        document.getElementById('passwordContainer').classList.remove('hidden');
        document.getElementById('passwordHelp').classList.remove('hidden');
        document.getElementById('defaultPasswordNote').classList.add('hidden');
        document.getElementById('admin_password').required = false;
        
        form.action = `/superadmin/admin/update/${id}`;
        methodInput.value = "PATCH";
        
        document.getElementById('admin_nama').value = nama;
        document.getElementById('admin_nip').value = nip;
        document.getElementById('admin_email').value = email;
        document.getElementById('admin_hp').value = hp;
        document.getElementById('admin_password').value = '';
        
        if (window.lucide) lucide.createIcons();
    }

    function closeModal() {
        document.getElementById('adminModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmStatus(userId, status, userName) {
        const actionText = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
        const confirmColor = status === 'aktif' ? '#10b981' : '#f59e0b';

        Swal.fire({
            title: 'Konfirmasi Status',
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
                const form = document.getElementById('form-status');
                form.action = `/superadmin/admin/status/${userId}`;
                document.getElementById('input-status').value = status;
                form.submit();
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Admin?',
            text: `Seluruh data terkait admin ${name} akan dihapus permanen!`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '1.5rem',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-delete');
                form.action = `/superadmin/admin/delete/${id}`;
                form.submit();
            }
        });
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endpush
