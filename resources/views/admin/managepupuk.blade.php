@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo. {{ Auth::user()->admin->nama_admin ?? Auth::user()->name }},</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola stok dan data pupuk subsidi pusat di sini.</p>
            </div>
            <button onclick="openAddModal()"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-violet-600 text-white font-bold rounded-xl hover:bg-violet-700 transition-all shadow-lg shadow-violet-100">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Tambah Pupuk</span>
            </button>
        </div>

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Stok Pusat</p>
                <p class="text-lg font-bold text-violet-600 mt-1">{{ number_format($pupuk->sum('stok_pusat')) }} Kg</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Sedang Diproses</p>
                <p class="text-lg font-bold text-blue-500 mt-1">{{ number_format($pupuk->sum('sedang_diproses')) }} Kg</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Stok di Mitra</p>
                <p class="text-lg font-bold text-orange-500 mt-1">{{ number_format($pupuk->sum('stok_mitra')) }} Kg</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Total Stok</p>
                <p class="text-lg font-bold text-green-600 mt-1">{{ number_format($pupuk->sum('total_stok')) }} Kg</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                    <form action="{{ route('admin.pupuk.index') }}" method="GET" id="filterForm" class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="filter" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <select name="status_stok" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block pl-10 pr-10 py-2.5 appearance-none cursor-pointer">
                                <option value="">Semua Status Stok</option>
                                <option value="kritis" {{ request('status_stok') == 'kritis' ? 'selected' : '' }}>Stok Kritis</option>
                                <option value="menipis" {{ request('status_stok') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                                <option value="aman" {{ request('status_stok') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                            </select>
                        </div>
                    </form>

                    <form action="{{ route('admin.pupuk.index') }}" method="GET" class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-violet-500 focus:border-violet-500 text-sm transition-all"
                                placeholder="Cari nama pupuk...">
                        </div>
                    </form>
                </div>

                <div class="flex items-center justify-between lg:justify-end gap-3 lg:border-l lg:pl-4 border-gray-100">
                    @if (request('search') || request('status_stok'))
                        <a href="{{ route('admin.pupuk.index') }}"
                            class="flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset
                        </a>
                    @endif

                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <p class="text-xs text-gray-500 font-medium">
                            <span class="text-gray-950 font-bold">{{ $pupuk->count() }}</span> Jenis Pupuk
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tabel Data -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Nama Pupuk</th>
                        <th class="px-6 py-4">Harga Subsidi</th>
                        <th class="px-6 py-3">Stok Pusat</th>
                        <th class="px-6 py-3">Stok Mitra</th>
                        <th class="px-6 py-3">Total Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pupuk as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $p->kode_pupuk }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $p->nama_pupuk }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">Rp
                                {{ number_format($p->harga_subsidi, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if ($p->stok_pusat < 1000)
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        {{ number_format($p->stok_pusat) }} Kg
                                    </span>
                                @elseif($p->stok_pusat <= 5000)
                                    <span
                                        class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                        {{ number_format($p->stok_pusat) }} Kg
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        {{ number_format($p->stok_pusat) }} Kg
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($p->stok_mitra) }} Kg</td>
                            <td class="px-6 py-4 text-sm font-bold text-green-700">{{ number_format($p->total_stok) }} Kg
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="openStockModal({{ $p->id_pupuk }}, '{{ $p->nama_pupuk }}')"
                                        class="p-2 text-gray-500 hover:text-violet-700 hover:bg-violet-100 rounded-lg transition-colors shadow-sm"
                                        title="Tambah Stok Pusat">
                                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                    </button>

                                    <button onclick="openEditModal({{ json_encode($p) }})"
                                        class="p-2 text-gray-500 hover:text-violet-700 hover:bg-violet-100 rounded-lg transition-colors shadow-sm"
                                        title="Edit Pupuk">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <form action="{{ route('admin.pupuk.destroy', $p->id_pupuk) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pupuk ini?')"
                                        class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-gray-500 hover:text-red-700 hover:bg-red-100 rounded-lg transition-colors shadow-sm"
                                            title="Hapus Pupuk">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data pupuk
                                tersedia.</td>
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

                <form id="pupukForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pupuk (5 Karakter)</label>
                            <input type="text" name="kode_pupuk" id="kode_pupuk" required maxlength="5"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none uppercase font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pupuk</label>
                            <input type="text" name="nama_pupuk" id="nama_pupuk" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Subsidi</label>
                                <input type="number" name="harga_subsidi" id="harga_subsidi" required min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok (Kg)</label>
                                <input type="number" name="stok" id="stok" required min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Gambar Produk Pupuk</label>

                            <div class="relative group">
                                <div
                                    class="flex items-center w-full px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl group-hover:border-green-500 transition-colors duration-300">

                                    <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-4">
                                        <i data-lucide="image-plus" class="w-6 h-6"></i>
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-600 group-hover:text-green-700">Pilih foto
                                            pupuk...</p>
                                        <p class="text-xs text-gray-400">PNG, JPG atau JPEG (Maks. 2MB)</p>
                                    </div>

                                    <input type="file" name="img_pupuk" id="img_pupuk" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="button" onclick="closeModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm transition-all">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Stok --}}
    <div id="stockModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Tambah Stok Pusat</h3>
                <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <p id="stockModalPupukName" class="text-sm font-semibold text-violet-700 mb-4 bg-violet-50 p-2 rounded-md">
            </p>

            <form id="stockForm" method="POST" action="">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tambahan Stok (Kg)</label>
                    <input type="number" name="tambahan_stok" required min="1" placeholder="Contoh: 5000"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-violet-500 focus:border-violet-500">
                    <p class="text-xs text-gray-500 mt-1">*Stok ini akan ditambahkan ke total Stok Pusat saat ini.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeStockModal()"
                        class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-violet-600 text-white rounded-lg hover:bg-violet-700">Tambahkan</button>
                </div>
            </form>
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
            methodField.innerHTML = '@method('PATCH')';

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

        document.getElementById('img_pupuk').addEventListener('change', function(e) {
            const fileName = e.target.files[0].name;
            const labelText = this.parentElement.querySelector('.text-sm.font-medium');
            const subText = this.parentElement.querySelector('.text-xs');

            if (fileName) {
                // Ganti teks dengan nama file yang dipilih
                labelText.innerText = "File terpilih: " + fileName;
                labelText.classList.remove('text-gray-600');
                labelText.classList.add('text-green-700');
                subText.innerText = "Klik lagi untuk mengganti file";
            }
        });

        // Fungsi untuk Modal Tambah Stok
        function openStockModal(id, nama_pupuk) {
            const modal = document.getElementById('stockModal');
            const nameLabel = document.getElementById('stockModalPupukName');
            const form = document.getElementById('stockForm');

            // Set teks nama pupuk
            nameLabel.innerText = 'Pupuk: ' + nama_pupuk;

            // Set action form ke URL yang benar
            form.action = `/admin/pupuk/${id}/tambah-stok`;

            // Tampilkan modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeStockModal() {
            document.getElementById('stockModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form setelah ditutup
            document.getElementById('stockForm').reset();
        }
    </script>
@endsection
