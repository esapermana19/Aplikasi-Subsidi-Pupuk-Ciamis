@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Chat Bantuan</h2>
            <p class="text-gray-500 text-sm mt-1">Sampaikan kendala atau pertanyaan Anda kepada Admin.</p>
        </div>
        
        <div x-data="{ chatOpen: false, topic: '', message: '', role: '{{ strtolower(Auth::user()->role) }}' }">
            <button @click="chatOpen = true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i> Chat Baru
            </button>

            <!-- Modal Chat Baru -->
            <div x-show="chatOpen" x-cloak class="fixed inset-0 z-[60] bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
                <div @click.outside="chatOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i data-lucide="headset" class="w-6 h-6 text-green-500"></i> Mulai Chat Baru
                        </h3>
                        <button @click="chatOpen = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('chat.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Topik Kendala</label>
                            <select name="topik" x-model="topic" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="" disabled>-- Pilih Topik --</option>
                                <option value="Transaksi">Transaksi</option>
                                <option value="Informasi Akun">Informasi Akun</option>
                                <template x-if="role === 'mitra'">
                                    <option value="Informasi Permintaan Pupuk">Informasi Permintaan Pupuk</option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan Pertama</label>
                            <textarea name="pesan" x-model="message" required rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="Tuliskan kendala Anda secara detail..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" @click="chatOpen = false" class="px-4 py-2 font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" :disabled="!topic || !message" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i> Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <ul class="divide-y divide-gray-100">
            @forelse($chats as $c)
                @php
                    $unreadCount = $c->messages->where('is_read', false)->where('sender_id', '!=', Auth::id())->count();
                @endphp
                <li>
                    <a href="{{ route('chat.show', $c->id_chat) }}" class="block hover:bg-gray-50 p-4 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0">
                                    <i data-lucide="message-square" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="text-md font-bold text-gray-800 flex items-center gap-2">
                                        {{ $c->topik }}
                                        @if($c->status === 'closed')
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Ditutup</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-500 truncate mt-0.5 max-w-md">
                                        {{ $c->messages->last()->message ?? 'Belum ada pesan.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-gray-500">{{ $c->updated_at->diffForHumans() }}</span>
                                @if($unreadCount > 0)
                                    <span class="mt-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-8 text-center text-gray-500">
                    <i data-lucide="message-circle" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                    <p>Belum ada riwayat percakapan dengan Admin.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
