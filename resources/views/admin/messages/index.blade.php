@extends('layouts.admin')

@section('title', 'Pesan dan Bantuan')
@section('page_title', 'Pesan dan Bantuan')
@section('page_subtitle', 'Kelola pesan dan pertanyaan dari pengguna')

@section('content')
<div class="space-y-6">

    <!-- Filter Status -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.messages.index') }}" class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 mr-1">Filter:</span>

            @php
                $statuses = [
                    '' => 'Semua',
                    'new' => 'Baru',
                    'processing' => 'Diproses',
                    'done' => 'Selesai',
                ];
            @endphp

            @foreach($statuses as $value => $label)
                @php $isActive = request('status', '') === $value; @endphp
                <button type="submit" name="status" value="{{ $value }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors"
                    @if($isActive)
                        style="background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;box-shadow:0 2px 8px rgba(147,51,234,.3)"
                    @else
                        style="background:#f1f5f9;color:#475569"
                        onmouseover="this.style.background='#e2e8f0'"
                        onmouseout="this.style.background='#f1f5f9'"
                    @endif>
                    {{ $label }}
                </button>
            @endforeach
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Judul</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Pengguna</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Tanggal</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($messages as $message)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <!-- Judul -->
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800 text-xs max-w-xs truncate">{{ $message->title }}</p>
                                @if($message->replies->count() > 0)
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $message->replies->count() }} balasan
                                    </p>
                                @endif
                            </td>

                            <!-- Pengguna -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-500 to-blue-400 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($message->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-slate-700 font-medium">{{ $message->user?->name ?? '—' }}</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                @php $statusValue = $message->status->value; @endphp
                                @if($statusValue === 'new')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Baru
                                    </span>
                                @elseif($statusValue === 'processing')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Diproses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $message->created_at->locale('id')->translatedFormat('d M Y') }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Lihat -->
                                    <a href="{{ route('admin.messages.show', $message) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 text-[11px] font-semibold transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </a>

                                    <!-- Tandai Selesai -->
                                    @if($message->status->value !== 'done')
                                        <form action="{{ route('admin.messages.done', $message) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[11px] font-semibold transition-colors"
                                                title="Tandai Selesai">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Selesai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-sm text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span>
                                        @if(request('status'))
                                            Tidak ada pesan dengan status ini.
                                        @else
                                            Belum ada pesan dari pengguna.
                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $messages->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
