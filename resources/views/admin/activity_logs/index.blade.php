@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas Admin')
@section('page_title', 'Riwayat Aktivitas Admin')
@section('page_subtitle', 'Rekam jejak tindakan penting yang dilakukan oleh administrator')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="bg-white rounded-2xl border border-purple-100 p-4 shadow-xs flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tindakan</label>
            <select name="action" class="px-3 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                <option value="">Semua Tindakan</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                        {{ ucfirst($act) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Objek</label>
            <select name="subject_type" class="px-3 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                <option value="">Semua Objek</option>
                @foreach($subjectTypes as $type)
                    <option value="{{ $type }}" {{ request('subject_type') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 text-white text-xs font-semibold hover:bg-purple-700 transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['action', 'subject_type']))
            <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 rounded-xl text-slate-500 text-xs font-medium hover:bg-slate-100 transition-colors">
                Reset
            </a>
        @endif
    </form>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Waktu</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Admin</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tindakan</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Objek</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="px-5 py-3.5 text-xs text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-medium text-slate-700">{{ $log->admin->name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $actionColors = [
                                        'created' => 'bg-emerald-50 text-emerald-700',
                                        'updated' => 'bg-blue-50 text-blue-700',
                                        'activated' => 'bg-teal-50 text-teal-700',
                                        'deactivated' => 'bg-amber-50 text-amber-700',
                                        'deleted' => 'bg-rose-50 text-rose-700',
                                        'published' => 'bg-purple-50 text-purple-700',
                                    ];
                                    $actionLabels = [
                                        'created' => 'Dibuat',
                                        'updated' => 'Diperbarui',
                                        'activated' => 'Diaktifkan',
                                        'deactivated' => 'Dinonaktifkan',
                                        'deleted' => 'Dihapus',
                                        'published' => 'Diterbitkan',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $actionColors[$log->action] ?? 'bg-slate-50 text-slate-600' }}">
                                    {{ $actionLabels[$log->action] ?? ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-xs text-slate-500 capitalize">{{ $log->subject_type }}</span>
                                <span class="block text-xs font-medium text-slate-700 truncate max-w-[180px]">{{ $log->subject_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 max-w-xs">
                                {{ $log->description ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">
                                Belum ada riwayat aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $logs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
