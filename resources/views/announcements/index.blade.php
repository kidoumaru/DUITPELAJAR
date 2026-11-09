@extends('layouts.app')

@section('title', 'Pengumuman')
@section('page_title', 'Pengumuman')
@section('page_subtitle', 'Informasi dan pengumuman terbaru dari admin')

@section('content')
<div class="space-y-6">

    <!-- Page Heading -->
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Pengumuman</h1>
        <p class="text-xs text-slate-400 mt-0.5">Tetap up-to-date dengan informasi terbaru dari tim DuitPelajar</p>
    </div>

    <!-- Announcements List -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        @if($announcements->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($announcements as $announcement)
                    <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <!-- Icon -->
                                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 text-sm leading-snug">{{ $announcement->title }}</h3>

                                    <p class="text-[11px] text-slate-400 mt-0.5 mb-2">
                                        {{ $announcement->published_at->locale('id')->translatedFormat('d F Y') }}
                                    </p>

                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                                        {{ Str::limit(strip_tags($announcement->body), 150) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action -->
                            <a href="{{ route('announcements.show', $announcement) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-purple-50 hover:border-purple-200 hover:text-purple-600 text-slate-600 font-semibold text-xs shadow-xs transition-colors shrink-0 self-start sm:self-center">
                                Baca Selengkapnya
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
                {{ $announcements->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Belum Ada Pengumuman</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    Belum ada pengumuman. Pantau terus halaman ini untuk informasi terbaru.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection
