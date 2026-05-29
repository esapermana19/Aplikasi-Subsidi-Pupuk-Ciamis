@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto flex flex-col h-[calc(100vh-100px)]">
    <!-- Header Chat -->
    <div class="bg-white border-b border-x rounded-t-xl shadow-sm p-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('chat.index') }}" class="text-gray-500 hover:text-gray-800 bg-gray-100 p-2 rounded-full transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $chat->user->name }} <span class="text-sm font-normal text-gray-500">({{ ucfirst($chat->user->role) }})</span></h2>
                <p class="text-sm text-gray-500">
                    Topik: {{ $chat->topik }} • 
                    @if($chat->status === 'closed')
                        <span class="text-red-500 font-medium">Ditutup</span>
                    @else
                        <span class="text-green-500 font-medium">Aktif</span>
                    @endif
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @if(!$chat->handled_by && $chat->status === 'open')
            <form action="{{ route('chat.take_over', $chat->id_chat) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium flex items-center gap-2 transition-colors">
                    <i data-lucide="hand" class="w-4 h-4"></i> Ambil Alih Chat
                </button>
            </form>
            @endif

            @if($chat->status === 'open')
            <form action="{{ route('chat.close', $chat->id_chat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup percakapan ini? Pengguna tidak akan bisa membalas lagi jika sudah ditutup.')">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-sm bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-medium flex items-center gap-2 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Tandai Selesai
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($chat->handled_by && $chat->handled_by !== Auth::id())
        <div class="bg-yellow-50 border-b border-x border-yellow-200 p-3 flex items-center justify-center gap-2 text-yellow-800 text-sm font-medium">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
            Chat ini sedang ditangani oleh Admin {{ $chat->handler->name ?? 'Lainnya' }}. Anda tetap bisa melihat dan membalas jika diperlukan.
        </div>
    @elseif($chat->handled_by && $chat->handled_by === Auth::id())
        <div class="bg-blue-50 border-b border-x border-blue-200 p-3 flex items-center justify-center gap-2 text-blue-800 text-sm font-medium">
            <i data-lucide="info" class="w-4 h-4"></i>
            Anda bertanggung jawab menangani chat ini.
        </div>
    @endif

    <!-- Messages Area -->
    <div class="flex-1 bg-gray-50 border-x p-4 overflow-y-auto" id="chatContainer">
        <div class="space-y-4">
            @foreach($chat->messages as $msg)
                @php
                    $isMe = in_array(strtolower($msg->sender->role), ['admin', 'superadmin']);
                @endphp
                
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="flex gap-3 max-w-[80%] {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">
                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $isMe ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-600' }}">
                            <i data-lucide="{{ $isMe ? 'shield' : 'user' }}" class="w-4 h-4"></i>
                        </div>
                        
                        <!-- Bubble -->
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <span class="text-xs text-gray-500 mb-1 mx-1">{{ $msg->sender->name }}</span>
                            <div class="px-4 py-2 rounded-2xl {{ $isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white border text-gray-800 rounded-tl-none shadow-sm' }}">
                                <p class="whitespace-pre-wrap">{{ $msg->message }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400 mt-1 mx-1">{{ $msg->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Input Area -->
    <div class="bg-white border rounded-b-xl shadow-sm p-4 shrink-0">
        @if($chat->status === 'open')
            <form action="{{ route('chat.reply', $chat->id_chat) }}" method="POST" class="flex items-end gap-3">
                @csrf
                <div class="flex-1 relative">
                    <textarea name="message" required rows="2" class="w-full border-gray-300 rounded-xl resize-none py-3 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik balasan Anda di sini..."></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white p-3 md:px-5 md:py-3 rounded-xl hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 h-[50px]">
                    <span class="hidden md:inline font-medium">Kirim</span>
                    <i data-lucide="send" class="w-5 h-5"></i>
                </button>
            </form>
        @else
            <div class="text-center p-3 bg-gray-50 rounded-lg text-gray-500 text-sm border">
                Percakapan ini telah ditutup.
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto scroll to bottom
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
</script>
@endpush
@endsection
