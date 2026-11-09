@extends('layouts.admin')

@section('title', 'Detail Pesan')
@section('page_title', 'Detail Pesan')
@section('page_subtitle', 'Baca dan balas pesan dari pengguna')

@section('content')
<div class="space-y-6 max-w-3xl">

    <!-- Back Link -->
    <div>
        <a href="{{ route('admin.messages.index') }}"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-purple-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Pesan
        </a>
    </div>

    <!-- Detail Pesan -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-800 text-base">{{ $message->title }}</h2>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-blue-400 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($message->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-700">{{ $message->user?->name ?? '—' }}</span>
                            <span class="text-[11px] text-slate-400 ml-1.5">{{ $message->user?->email ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <!-- Status Badge -->
                    @php $statusValue = $message->status->value; @endphp
                    @if($statusValue === 'new')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            Baru
                        </span>
                    @elseif($statusValue === 'processing')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Diproses
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Selesai
                        </span>
                    @endif

                    <!-- Tandai Selesai -->
                    @if($statusValue !== 'done')
                        <form action="{{ route('admin.messages.done', $message) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold shadow-sm shadow-emerald-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai Selesai
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Isi pesan asli -->
        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Pesan</p>
            <div class="bg-slate-50 rounded-xl px-4 py-3.5 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $message->body }}</div>
            <p class="text-[11px] text-slate-400 mt-2">
                Dikirim {{ $message->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
            </p>
        </div>
    </div>

    <!-- Percakapan / Balasan -->
    @if($message->replies->count() > 0)
        <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-700 text-sm">Percakapan</h3>
            </div>
            <div class="px-6 py-5 space-y-4">
                @foreach($message->replies as $reply)
                    @php $isUserMessage = $reply->sender_id === $message->user_id; @endphp

                    @if($isUserMessage)
                        {{-- Bubble kiri: pesan dari pengguna --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-slate-400 to-slate-500 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                {{ strtoupper(substr($reply->sender?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="max-w-[75%]">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-xs font-semibold text-slate-700">{{ $reply->sender?->name ?? 'Pengguna' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $reply->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <div class="bg-slate-100 rounded-2xl rounded-tl-sm px-4 py-3 text-xs text-slate-700 leading-relaxed whitespace-pre-wrap">
                                    {{ $reply->body }}
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Bubble kanan: balasan dari admin --}}
                        <div class="flex items-start gap-3 flex-row-reverse">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                {{ strtoupper(substr($reply->sender?->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="max-w-[75%]">
                                <div class="flex items-baseline justify-end gap-2 mb-1">
                                    <span class="text-[10px] text-slate-400">{{ $reply->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                                    <span class="text-xs font-semibold text-purple-600">{{ $reply->sender?->name ?? 'Admin' }}</span>
                                </div>
                                <div class="bg-purple-600 rounded-2xl rounded-tr-sm px-4 py-3 text-xs text-white leading-relaxed whitespace-pre-wrap">
                                    {{ $reply->body }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Form Balas (hanya jika belum selesai) -->
    @if($message->status->value !== 'done')
        <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-700 text-sm">Tulis Balasan</h3>
            </div>
            <div class="px-6 py-5">
                <form action="{{ route('admin.messages.reply', $message) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Balasan <span class="text-rose-500">*</span></label>
                        <textarea name="body" required rows="4" placeholder="Tulis balasan untuk pengguna..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors resize-none"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Percakapan sudah selesai -->
        <div class="bg-slate-50 rounded-2xl border border-purple-100 px-6 py-5 text-center">
            <div class="flex items-center justify-center gap-2 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-semibold">Percakapan ini sudah ditandai selesai.</span>
            </div>
        </div>
    @endif

</div>
@endsection
