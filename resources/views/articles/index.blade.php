@extends('layouts.app')

@section('title', 'Artikel Edukasi')
@section('page_title', 'Artikel Edukasi')
@section('page_subtitle', 'Tips dan panduan mengelola keuangan untuk pelajar')

@section('content')
<div class="space-y-6">

    <!-- Page Heading -->
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Artikel Edukasi</h1>
        <p class="text-xs text-slate-400 mt-0.5">Perkaya wawasan finansialmu dengan tips dari para ahli</p>
    </div>

    @if($articles->count() > 0)
        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($articles as $article)
                <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">
                    <!-- Thumbnail -->
                    @if($article->thumbnail)
                        <div class="aspect-video overflow-hidden bg-slate-100">
                            <img
                                src="{{ Storage::url($article->thumbnail) }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    @else
                        <div class="aspect-video bg-gradient-to-br from-indigo-50 to-blue-50 flex items-center justify-center">
                            <svg class="w-12 h-12 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-1">
                        <!-- Category Badge -->
                        @if($article->category)
                            <span class="inline-flex self-start items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-600 border border-purple-100 mb-3">
                                {{ $article->category }}
                            </span>
                        @endif

                        <!-- Title -->
                        <h3 class="font-bold text-slate-800 text-sm leading-snug mb-2 line-clamp-2 flex-1">
                            {{ $article->title }}
                        </h3>

                        <!-- Date & Read Link -->
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                            <span class="text-[11px] text-slate-400">
                                {{ $article->published_at->locale('id')->translatedFormat('d M Y') }}
                            </span>

                            <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                                Baca
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-2xl px-6 py-4 border border-purple-100 shadow-xs">
            {{ $articles->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-3xl border border-purple-100 shadow-xs py-16 px-4 text-center">
            <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <h4 class="font-bold text-slate-800 text-base">Belum Ada Artikel</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                Belum ada artikel yang diterbitkan. Kembali lagi nanti untuk membaca tips keuangan terbaru.
            </p>
        </div>
    @endif

</div>
@endsection
