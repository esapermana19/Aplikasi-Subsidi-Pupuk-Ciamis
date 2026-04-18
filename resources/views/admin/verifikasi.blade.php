@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Verifikasi Akun</h1>
            <p class="text-gray-500 mt-1">Setujui atau tolak pendaftaran akun baru</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif
        {{-- Area Filter --}}
        <div
            class="bg-white p-4 rounded-t-xl border-x border-t border-gray-100 shadow-sm space-y-4 md:space-y-0 md:flex md:items-center md:justify-between">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Dropdown Filter Role --}}
                <div class="relative group">
                    <form action="{{ route('verifikasi') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                        <div
                            class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500 transition-all">
                            <i data-lucide="filter" class="h-4 w-4 text-gray-400"></i>
                            <select name="role" onchange="this.form.submit()"
                                class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0 p-0 pr-8 cursor-pointer">
                                <option value="">Semua Peran</option>
                                <option value="petani" {{ request('role') == 'petani' ? 'selected' : '' }}>Petani</option>
                                <option value="mitra" {{ request('role') == 'mitra' ? 'selected' : '' }}>Mitra</option>
                            </select>
                        </div>

                        @if (request('role'))
                            <a href="{{ route('verifikasi') }}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors bg-red-50 px-2 py-1.5 rounded-md">
                                <i data-lucide="x" class="h-3 w-3"></i>
                                Hapus Filter
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Separator --}}
                <div class="hidden md:block h-8 border-l border-gray-200"></div>

                {{-- Badge Info Jumlah --}}
                <div class="flex items-center gap-2">
                    <span class="flex h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <p class="text-sm font-medium text-gray-600">
                        Menampilkan <span class="text-gray-900 font-bold">{{ $users->count() }}</span> permintaan pending
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIK</th>
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
                                            '{{ $user->petani->jenis_kelamin ?? '-' }}',
                                            '{{ $user->mitra->nama_pemilik ?? '-' }}',
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
    </div>
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-xl max-w-lg w-full overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Detail Pendaftaran <span id="title_role" class="text-gray-900"></span></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="h-5 w-5"></i></button>
            </div>
            <div class="p-6 space-y-4 text-left">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <label class="text-xs font-bold text-gray-400 uppercase">NIK</label>
                        <p id="det_nik" class="text-sm font-semibold text-gray-900"></p>
                    </div>
                    <div class="col-span-1" id="box_jk">
                        <label class="text-xs font-bold text-gray-400 uppercase">Jenis Kelamin</label>
                        <p id="det_jk" class="text-sm font-semibold text-gray-900"></p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-bold text-gray-400 uppercase">Nama Lengkap / Instansi</label>
                        <p id="det_nama" class="text-sm font-semibold text-gray-900"></p>
                    </div>
                    <div id="box_mitra_only" class="col-span-2 grid grid-cols-2 gap-4 hidden">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Nama Pemilik</label>
                            <p id="det_pemilik" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">No. Rekening</label>
                            <p id="det_rek" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-bold text-gray-400 uppercase">Email Login</label>
                        <p id="det_email" class="text-sm font-semibold text-gray-900"></p>
                    </div>
                    <div class="col-span-2 border-t pt-3">
                        <label class="text-xs font-bold text-gray-400 uppercase">Alamat Lengkap</label>
                        <p id="det_alamat" class="text-sm text-gray-700 bg-gray-50 p-3 rounded-xl mt-1"></p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button onclick="closeModal()" class="px-6 py-2 bg-violet-600 text-white rounded-xl text-sm font-bold">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function openDetailVerifikasi(role, nik, nama, email, alamat, jk, pemilik, rek) {
    document.getElementById('title_role').innerText = role.toUpperCase();
    document.getElementById('det_nik').innerText = nik;
    document.getElementById('det_nama').innerText = nama;
    document.getElementById('det_email').innerText = email;
    document.getElementById('det_alamat').innerText = alamat;

    // Logic tampilan berdasarkan Role
    const boxMitra = document.getElementById('box_mitra_only');
    const boxJk = document.getElementById('box_jk');

    if(role.toLowerCase() === 'mitra') {
        boxMitra.classList.remove('hidden');
        boxJk.classList.add('hidden');
        document.getElementById('det_pemilik').innerText = pemilik;
        document.getElementById('det_rek').innerText = rek;
    } else {
        boxMitra.classList.add('hidden');
        boxJk.classList.remove('hidden');
        document.getElementById('det_jk').innerText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : '-');
    }

    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>
@endsection
