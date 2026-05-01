@extends('layouts.app') @section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Permintaan Pupuk</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau status permintaan stok pupuk Anda ke distributor.</p>
            </div>

            <a href="{{ route('mitra.pupuk_tersedia') }}"
                class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Permintaan Baru
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal Permintaan</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Catatan</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="pl-10 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $req)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ \Carbon\Carbon::parse($req->tgl_permintaan)->format('d F Y') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-gray-600">
                                {{ $req->catatan ?? 'Tidak ada catatan' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span
                                    class="px-3 py-1 font-semibold rounded-full text-xs
                            {{ $req->status_permintaan == 'pending'
                                ? 'bg-yellow-100 text-yellow-800'
                                : ($req->status_permintaan == 'diproses'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($req->status_permintaan == 'diterima'
                                        ? 'bg-gray-100 text-gray-600 border border-gray-200'
                                        : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($req->status_permintaan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button
                                        onclick="lihatDetail('{{ $req->id_permintaan }}', '{{ $req->status_permintaan }}')"
                                        class="bg-violet-500 hover:bg-violet-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-200">
                                        Detail
                                    </button>

                                    @if ($req->status_permintaan == 'pending')
                                        <button disabled
                                            class="bg-gray-300 text-gray-500 cursor-not-allowed px-3 py-1 rounded-md shadow-sm flex items-center gap-1 transition-all"
                                            title="Menunggu persetujuan Admin">
                                            Terima
                                        </button>
                                    @elseif ($req->status_permintaan == 'diproses')
                                        <form action="{{ route('mitra.permintaan.terima', $req->id_permintaan) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin pupuk sudah diterima di gudang mitra?')">
                                            @csrf
                                            <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md shadow-sm flex items-center gap-1 transition-all">
                                                Terima
                                            </button>
                                        </form>
                                    @elseif ($req->status_permintaan == 'diterima')
                                        <span
                                            class="bg-gray-100 text-gray-600 px-3 py-1 rounded-md border border-gray-200 flex items-center gap-1 cursor-default">
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-500 bg-white">
                                Belum ada riwayat permintaan pupuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalDetail" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Detail Permintaan</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
            </div>

            <div id="statusBadgeModal" class="mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full">
            </div>

            <div id="detailPupukContainer" class="space-y-3 bg-gray-50 p-4 rounded border border-gray-200">
                <p class="text-gray-500 text-sm text-center">Memuat detail...</p>
            </div>

            <div class="flex justify-end mt-6 border-t pt-4">
                <button onclick="closeModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-medium">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function lihatDetail(id_permintaan, status) {
            const modal = document.getElementById('modalDetail');
            const container = document.getElementById('detailPupukContainer');
            const badge = document.getElementById('statusBadgeModal');

            // Tampilkan Modal
            modal.classList.remove('hidden');
            container.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">Memuat data dari server...</p>';

            // Set warna badge status
            badge.innerText = status.toUpperCase();
            if (status === 'pending') {
                badge.className =
                    'mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800';
            } else if (status === 'disetujui') {
                badge.className =
                    'mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800';
            } else if (status === 'diproses') {
                badge.className =
                    'mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800';
            } else if (status === 'diterima') {
                badge.className =
                    'mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 border border-gray-200';
            } else {
                badge.className =
                    'mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';
            }

            // Ambil data detail menggunakan Fetch API
            fetch(`/mitra/permintaan/${id_permintaan}/detail`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';

                    // Header Kolom
                    container.innerHTML += `
                    <div class="flex font-bold text-sm text-gray-600 border-b pb-2 mb-2">
                        <div class="w-1/2">Jenis Pupuk</div>
                        <div class="w-1/4 text-center">Diminta</div>
                        <div class="w-1/4 text-right">Disetujui</div>
                    </div>
                `;

                    // Looping data pupuk
                    data.forEach(item => {
                        // Tentukan warna teks disetujui (Jika pending, strip '-' saja. Jika disetujui, warna hijau)
                        let textDisetujui = status === 'pending' ? '<span class="text-gray-400">-</span>' :
                            `<span class="text-green-600 font-bold">${item.jml_disetujui} Kg</span>`;

                        container.innerHTML += `
                        <div class="flex items-center text-sm py-2 border-b border-gray-100 last:border-0">
                            <div class="w-1/2 font-medium text-gray-800">${item.nama_pupuk}</div>
                            <div class="w-1/4 text-center text-gray-700">${item.jml_diminta} Kg</div>
                            <div class="w-1/4 text-right">${textDisetujui}</div>
                        </div>
                    `;
                    });
                })
                .catch(err => {
                    container.innerHTML = '<p class="text-red-500 text-sm text-center py-4">Gagal memuat data.</p>';
                });
        }

        function closeModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }

        // Tutup jika user klik area gelap luar modal
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modalDetail');
            if (event.target === modal) {
                closeModal();
            }
        });
    </script>
@endsection
