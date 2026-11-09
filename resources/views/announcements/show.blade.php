@extends('layouts.app')

@section('title', $announcement->title)
@section('page_title', 'Pengumuman')
@section('page_subtitle', 'Detail pengumuman')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Back Link -->
    <div>
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Pengumuman
        </a>
    </div>

    <!-- Announcement Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <!-- Header -->
        <div class="pb-5 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <span class="text-[11px] font-semibold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-100">
                    Pengumuman Resmi
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 leading-snug">
                {{ $announcement->title }}
            </h1>

            <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Diterbitkan {{ $announcement->published_at->locale('id')->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <!-- Body Content -->
        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed text-sm">
            {!! nl2br(e($announcement->body)) !!}
        </div>
    </div>

    <!-- Back to List -->
    <div class="pb-2">
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs shadow-xs transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Lihat Semua Pengumuman
        </a>
    </div>

</div>
@endsection
