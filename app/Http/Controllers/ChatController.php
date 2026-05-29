<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (in_array(strtolower($user->role), ['admin', 'superadmin'])) {
            $chats = Chat::with(['user'])->orderBy('updated_at', 'desc')->paginate(10);
            return view('admin.chat.index', compact('chats'), ['activeMenu' => 'chat']);
        } else {
            $chats = Chat::where('id_user', $user->id_user)->orderBy('updated_at', 'desc')->get();
            return view('chat.index', compact('chats'), ['activeMenu' => 'chat']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'topik' => 'required|string',
            'pesan' => 'required|string',
        ]);

        $chat = Chat::create([
            'id_user' => Auth::id(),
            'topik' => $request->topik,
            'status' => 'open',
        ]);

        ChatMessage::create([
            'id_chat' => $chat->id_chat,
            'sender_id' => Auth::id(),
            'message' => $request->pesan,
        ]);

        return redirect()->route('chat.show', $chat->id_chat)->with('success', 'Obrolan berhasil dimulai.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $chat = Chat::with(['messages.sender'])->findOrFail($id);

        if (!in_array(strtolower($user->role), ['admin', 'superadmin']) && $chat->id_user !== $user->id_user) {
            abort(403, 'Unauthorized action.');
        }

        // Mark messages as read
        if (in_array(strtolower($user->role), ['admin', 'superadmin'])) {
            ChatMessage::where('id_chat', $id)->where('sender_id', '!=', $user->id_user)->update(['is_read' => true]);
            $view = 'admin.chat.show';
        } else {
            ChatMessage::where('id_chat', $id)->where('sender_id', '!=', $user->id_user)->update(['is_read' => true]);
            $view = 'chat.show';
        }

        return view($view, compact('chat'), ['activeMenu' => 'chat']);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = Chat::findOrFail($id);
        $user = Auth::user();

        if (!in_array(strtolower($user->role), ['admin', 'superadmin']) && $chat->id_user !== $user->id_user) {
            abort(403, 'Unauthorized action.');
        }

        ChatMessage::create([
            'id_chat' => $chat->id_chat,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Update chat updated_at timestamp to bring it to top
        $chat->touch();

        return back();
    }

    public function closeChat($id)
    {
        $chat = Chat::findOrFail($id);
        if (in_array(strtolower(Auth::user()->role), ['admin', 'superadmin'])) {
            $chat->update(['status' => 'closed']);
        }
        return back()->with('success', 'Chat telah ditutup.');
    }

    public function takeOver($id)
    {
        $chat = Chat::findOrFail($id);
        if (in_array(strtolower(Auth::user()->role), ['admin', 'superadmin'])) {
            $chat->update(['handled_by' => Auth::id()]);
            return back()->with('success', 'Chat sekarang ditangani oleh Anda.');
        }
        return back();
    }
}
