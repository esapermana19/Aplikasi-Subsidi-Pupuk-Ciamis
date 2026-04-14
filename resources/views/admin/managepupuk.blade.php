@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Stok Pupuk</h1>
            <p class="text-gray-500 text-sm">Pantau ketersediaan dan harga pupuk subsidi Kabupaten Ciamis.</p>
        </div>
        <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Pupuk
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Jenis</p>
            <p class="text-xl font-bold text-gray-900">{{ $pupuk->count() }} Jenis</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Stok</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($pupuk->sum('stok')) }} Kg</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Pupuk</th>
                    <th class="px-6 py-4">Harga Subsidi</th>
                    <th class="px-6 py-4">Stok (Kg)</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pupuk as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $p->kode_pupuk }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $p->nama_pupuk }}</td>
                    <td class="px-6 py-4 text-gray-900 font-medium">Rp {{ number_format($p->harga_subsidi, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $p->stok < 50 ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                            {{ number_format($p->stok) }} Kg
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <button onclick="openEditModal({{ json_encode($p) }})" class="p-2 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-all">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.pupuk.destroy', $p->id_pupuk) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pupuk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data pupuk tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form (Create & Update) -->
<div id="pupukModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Pupuk</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form id="pupukForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pupuk (5 Karakter)</label>
                        <input type="text" name="kode_pupuk" id="kode_pupuk" required maxlength="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none uppercase font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pupuk</label>
                        <input type="text" name="nama_pupuk" id="nama_pupuk" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Subsidi</label>
                            <input type="number" name="harga_subsidi" id="harga_subsidi" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok (Kg)</label>
                            <input type="number" name="stok" id="stok" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm transition-all">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('pupukModal');
    const form = document.getElementById('pupukForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');

    function openAddModal() {
        modalTitle.innerText = 'Tambah Pupuk Baru';
        form.action = "{{ route('admin.pupuk.store') }}";
        methodField.innerHTML = '';
        form.reset();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(data) {
        modalTitle.innerText = 'Edit Data Pupuk';
        // Sesuaikan nama route update di web.php Anda
        form.action = "{{ route('admin.pupuk.update', ['id' => ':id']) }}".replace(':id', data.id_pupuk);
        methodField.innerHTML = '@method("PATCH")';

        document.getElementById('kode_pupuk').value = data.kode_pupuk;
        document.getElementById('nama_pupuk').value = data.nama_pupuk;
        document.getElementById('harga_subsidi').value = data.harga_subsidi;
        document.getElementById('stok').value = data.stok;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endsection
