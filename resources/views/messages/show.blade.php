@extends('layouts.app')

@section('title', $message->title)
@section('page_title', 'Detail Pesan')
@section('page_subtitle', 'Percakapan dengan admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Back Link -->
    <div>
        <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Pesan
        </a>
    </div>

    <!-- Thread Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-lg font-bold text-slate-800 leading-snug">{{ $message->title }}</h1>
                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                    <!-- Status Badge -->
                    @php $statusVal = $message->status->value; @endphp
                    @if($statusVal === 'new')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Baru
                        </span>
                    @elseif($statusVal === 'processing')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Diproses
                        </span>
                    @elseif($statusVal === 'done')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Baru
                        </span>
                    @endif

                    <span class="text-[11px] text-slate-400">
                        Dikirim {{ $message->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Original Message Body -->
        <div class="mt-4 pt-4 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-500 mb-2">Pesan Awal</p>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-sm text-slate-700 leading-relaxed">
                {!! nl2br(e($message->body)) !!}
            </div>
        </div>
    </div>

    <!-- Replies Thread -->
    @if($message->replies && count($message->replies) > 0)
        <div class="space-y-3">
            <p class="text-xs font-semibold text-slate-500 px-1">Balasan ({{ count($message->replies) }})</p>

            @foreach($message->replies as $reply)
                @php
                    $isFromUser = $reply->sender_id === $message->user_id;
                @endphp

                <div class="flex {{ $isFromUser ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] sm:max-w-[75%]">
                        <!-- Sender Label -->
                        <p class="text-[11px] font-semibold mb-1 {{ $isFromUser ? 'text-right text-purple-600' : 'text-left text-slate-500' }}">
                            {{ $reply->sender->name ?? 'Admin' }}
                        </p>

                        <!-- Bubble -->
                        <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                            {{ $isFromUser
                                ? 'bg-gradient-to-br from-indigo-600 to-blue-600 text-white rounded-tr-sm shadow-sm shadow-purple-200'
                                : 'bg-white border border-slate-200 text-slate-700 rounded-tl-sm shadow-xs'
                            }}">
                            {!! nl2br(e($reply->body)) !!}
                        </div>

                        <!-- Timestamp -->
                        <p class="text-[10px] text-slate-400 mt-1 {{ $isFromUser ? 'text-right' : 'text-left' }}">
                            {{ $reply->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Reply Form -->
    @if($message->status->value !== 'done')
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <p class="text-xs font-semibold text-slate-700 mb-3">Tulis Balasan</p>

            <form action="{{ route('messages.reply', $message) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <textarea
                        name="body"
                        rows="4"
                        required
                        placeholder="Tulis balasanmu di sini..."
                        class="w-full px-4 py-2.5 rounded-xl border @error('body') border-rose-400 bg-rose-50/30 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 placeholder-slate-400 transition-colors resize-none"
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
            <p class="text-xs font-semibold text-emerald-700">Percakapan ini telah ditandai selesai oleh admin.</p>
        </div>
    @endif

</div>
@endsection
