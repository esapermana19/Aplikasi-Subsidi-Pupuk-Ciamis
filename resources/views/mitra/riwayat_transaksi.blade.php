@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-green-700">Riwayat Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar penyerahan pupuk yang telah selesai.</p>
        </div>
        <a href="{{ url('/mitra/scan') }}" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
            + Scan QR Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b">
                        <th class="p-4 font-semibold">Tgl Penyerahan</th>
                        <th class="p-4 font-semibold">ID Transaksi</th>
                        <th class="p-4 font-semibold">Nama Petani</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($riwayat as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-4 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="p-4 text-sm font-mono text-gray-600">
                                {{ $item->id_transaksi }}
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-800">
                                {{ $item->petani->nama_petani ?? '-' }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    Selesai / Diambil
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">
                                Belum ada riwayat penyerahan pupuk saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection