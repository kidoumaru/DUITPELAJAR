@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')
@section('page_subtitle', 'Pengumuman baru dan balasan pesan dari Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <!-- Header + Mark All Read -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-800">Semua Notifikasi</h2>
        @if($notifications->total() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 hover:bg-purple-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tandai Semua Sudah Dibaca
                </button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notif)
        <div class="bg-white rounded-2xl border {{ $notif->read_at ? 'border-slate-100' : 'border-purple-100 shadow-sm' }} p-4 flex items-start gap-4 transition-all">
            <!-- Icon -->
            <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center
                {{ $notif->type === 'announcement_new' ? 'bg-amber-50 text-amber-600' : 'bg-purple-50 text-purple-600' }}">
                @if($notif->type === 'announcement_new')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                @endif
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 {{ $notif->read_at ? '' : 'text-purple-700' }}">
                            {{ $notif->title }}
                        </p>
                        @if($notif->body)
                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $notif->body }}</p>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(! $notif->read_at)
                        <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-purple-500 mt-1"></span>
                    @endif
                </div>

                @if($notif->url)
                    <form action="{{ route('notifications.read', $notif) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="text-xs font-medium text-purple-600 hover:text-purple-800 hover:underline">
                            Lihat detail →
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl border border-slate-100 p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 mb-1">Belum ada notifikasi</h3>
            <p class="text-xs text-slate-400">Kamu akan mendapat notifikasi saat ada pengumuman baru atau balasan dari Admin.</p>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
