@extends('layouts.app')

@section('title', 'Pesan dan Bantuan')
@section('page_title', 'Pesan dan Bantuan')
@section('page_subtitle', 'Hubungi admin jika butuh bantuan')

@section('content')
<div class="space-y-6">

    <!-- Page Heading & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Pesan dan Bantuan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Riwayat pesan yang telah kamu kirim ke admin</p>
        </div>

        <a href="{{ route('messages.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            + Kirim Pesan
        </a>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        @if($messages->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($messages as $message)
                    <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <!-- Icon -->
                                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 text-sm truncate">{{ $message->title }}</p>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <!-- Status Badge -->
                                        @if($message->status->value === 'new')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Baru
                                            </span>
                                        @elseif($message->status->value === 'processing')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Diproses
                                            </span>
                                        @elseif($message->status->value === 'done')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Baru
                                            </span>
                                        @endif

                                        <!-- Date -->
                                        <span class="text-[11px] text-slate-400">
                                            {{ $message->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                        </span>

                                        <!-- Replies Count -->
                                        @if($message->replies_count > 0)
                                            <span class="text-[11px] text-slate-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                                {{ $message->replies_count }} balasan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action -->
                            <a href="{{ route('messages.show', $message) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-purple-50 hover:border-purple-200 hover:text-purple-600 text-slate-600 font-semibold text-xs shadow-xs transition-colors shrink-0">
                                Lihat Detail
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $messages->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Belum Ada Pesan</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-5">
                    Belum ada pesan. Kirim pesan ke admin untuk mendapatkan bantuan.
                </p>
                <a href="{{ route('messages.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Kirim Pesan Sekarang
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
