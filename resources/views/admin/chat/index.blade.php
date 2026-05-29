@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dukungan / Chat Pengguna</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola dan balas keluhan atau pertanyaan dari Petani dan Mitra.</p>
        </div>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <ul class="divide-y divide-gray-100">
            @forelse($chats as $c)
                @php
                    $unreadCount = $c->messages->where('is_read', false)->where('sender_id', '!=', Auth::id())->count();
                    $lastMessage = $c->messages->last();
                @endphp
                <li>
                    <a href="{{ route('chat.show', $c->id_chat) }}" class="block hover:bg-gray-50 p-4 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 {{ strtolower($c->user->role) === 'petani' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600' }} rounded-full flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ strtolower($c->user->role) === 'petani' ? 'leaf' : 'store' }}" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <h4 class="text-md font-bold text-gray-800">{{ $c->user->name }}</h4>
                                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ strtolower($c->user->role) === 'petani' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                            {{ ucfirst($c->user->role) }}
                                        </span>
                                        @if($c->status === 'closed')
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Ditutup</span>
                                        @endif
                                        @if($c->handled_by)
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 flex items-center gap-1">
                                                <i data-lucide="user-check" class="w-3 h-3"></i> Ditangani: {{ $c->handler->name ?? 'Admin' }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700 mb-0.5">Topik: {{ $c->topik }}</p>
                                    <p class="text-sm text-gray-500 truncate max-w-xl">
                                        {{ $lastMessage ? $lastMessage->message : 'Belum ada pesan.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col sm:items-end justify-center shrink-0">
                                <span class="text-xs text-gray-500">{{ $c->updated_at->diffForHumans() }}</span>
                                @if($unreadCount > 0)
                                    <span class="mt-1 bg-red-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full inline-block">{{ $unreadCount }} Pesan Baru</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-12 text-center text-gray-500">
                    <i data-lucide="message-square" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                    <p class="text-lg font-medium text-gray-600">Belum ada obrolan masuk.</p>
                    <p class="text-sm">Saat ini tidak ada Petani atau Mitra yang menghubungi dukungan.</p>
                </li>
            @endforelse
        </ul>
        @if($chats->hasPages())
            <div class="p-4 border-t">
                {{ $chats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
