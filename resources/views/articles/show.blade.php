@extends('layouts.app')

@section('title', $article->title)
@section('page_title', 'Artikel Edukasi')
@section('page_subtitle', 'Baca artikel pilihan untuk pelajar')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Back Link -->
    <div>
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Artikel
        </a>
    </div>

    <!-- Article Card -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        <!-- Thumbnail -->
        @if($article->thumbnail)
            <div class="aspect-video overflow-hidden bg-slate-100">
                <img
                    src="{{ Storage::url($article->thumbnail) }}"
                    alt="{{ $article->title }}"
                    class="w-full h-full object-cover"
                >
            </div>
        @endif

        <div class="p-6 sm:p-8">
            <!-- Category Badge -->
            @if($article->category)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-600 border border-purple-100 mb-4">
                    {{ $article->category }}
                </span>
            @endif

            <!-- Title -->
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 leading-snug mb-4">
                {{ $article->title }}
            </h1>

            <!-- Meta: Author & Date -->
            <div class="flex flex-wrap items-center gap-4 pb-5 border-b border-slate-100 mb-6">
                @if($article->author)
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($article->author->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-xs font-semibold text-slate-700">{{ $article->author->name }}</span>
                    </div>
                @endif

                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $article->published_at->locale('id')->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <!-- Article Content -->
            <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed text-sm">
                {!! nl2br(e($article->content)) !!}
            </div>
        </div>
    </div>

    <!-- Back to List -->
    <div class="pb-2">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs shadow-xs transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Lihat Semua Artikel
        </a>
    </div>

</div>
@endsection
