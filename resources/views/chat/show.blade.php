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
                <h2 class="text-lg font-bold text-gray-800">{{ $chat->topik }}</h2>
                <p class="text-sm text-gray-500">
                    ID Tiket: #{{ str_pad($chat->id_chat, 5, '0', STR_PAD_LEFT) }} • 
                    @if($chat->status === 'closed')
                        <span class="text-red-500 font-medium">Ditutup</span>
                    @else
                        <span class="text-green-500 font-medium">Aktif</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 bg-gray-50 border-x p-4 overflow-y-auto" id="chatContainer">
        <div class="space-y-4">
            @foreach($chat->messages as $msg)
                @php
                    $isMe = $msg->sender_id === Auth::id();
                    $isAdmin = in_array(strtolower($msg->sender->role), ['admin', 'superadmin']);
                @endphp
                
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="flex gap-3 max-w-[80%] {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">
                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $isAdmin ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                            <i data-lucide="{{ $isAdmin ? 'shield' : 'user' }}" class="w-4 h-4"></i>
                        </div>
                        
                        <!-- Bubble -->
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <span class="text-xs text-gray-500 mb-1 mx-1">{{ $msg->sender->name }}</span>
                            <div class="px-4 py-2 rounded-2xl {{ $isMe ? 'bg-green-600 text-white rounded-tr-none' : 'bg-white border text-gray-800 rounded-tl-none shadow-sm' }}">
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
                    <textarea name="message" required rows="2" class="w-full border-gray-300 rounded-xl resize-none py-3 px-4 focus:ring-green-500 focus:border-green-500" placeholder="Ketik pesan Anda di sini..."></textarea>
                </div>
                <button type="submit" class="bg-green-600 text-white p-3 md:px-5 md:py-3 rounded-xl hover:bg-green-700 transition-colors flex items-center justify-center gap-2 h-[50px]">
                    <span class="hidden md:inline font-medium">Kirim</span>
                    <i data-lucide="send" class="w-5 h-5"></i>
                </button>
            </form>
        @else
            <div class="text-center p-3 bg-gray-50 rounded-lg text-gray-500 text-sm border">
                Percakapan ini telah ditutup oleh Admin. Anda dapat membuat obrolan baru jika diperlukan.
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
