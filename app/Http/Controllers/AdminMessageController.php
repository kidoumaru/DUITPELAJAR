<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageStatus;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Message::with(['user', 'replies']);

        if ($request->filled('status') && in_array($request->status, ['new', 'processing', 'done'])) {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function show(Message $message): View
    {
        $message->load(['user', 'replies.sender']);

        return view('admin.messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
        ], [
            'body.required' => 'Balasan wajib diisi.',
        ]);

        MessageReply::create([
            'message_id' => $message->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        $message->update(['status' => MessageStatus::Processing]);

        // Notify the message owner about the admin reply.
        UserNotification::create([
            'user_id' => $message->user_id,
            'type' => 'message_reply',
            'title' => 'Admin Membalas Pesanmu',
            'body' => "Admin telah membalas pesanmu: \"{$message->title}\"",
            'url' => route('messages.show', $message),
        ]);

        return redirect()->route('admin.messages.show', $message)
            ->with('success', 'Balasan terkirim.');
    }

    public function markDone(Message $message): RedirectResponse
    {
        $message->update(['status' => MessageStatus::Done]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Percakapan ditandai selesai.');
    }
}
