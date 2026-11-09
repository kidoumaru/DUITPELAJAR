<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = Message::where('user_id', Auth::id())
            ->withCount('replies')
            ->latest()
            ->paginate(15);

        return view('messages.index', compact('messages'));
    }

    public function create(): View
    {
        return view('messages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ], [
            'title.required' => 'Judul pesan wajib diisi.',
            'body.required' => 'Isi pesan wajib diisi.',
        ]);

        Message::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => MessageStatus::New,
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Pesan Anda telah terkirim ke admin.');
    }

    public function show(Message $message): View
    {
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesan ini.');
        }

        $message->load('replies.sender');

        return view('messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message): RedirectResponse
    {
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesan ini.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        MessageReply::create([
            'message_id' => $message->id,
            'sender_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Balasan terkirim.');
    }
}
