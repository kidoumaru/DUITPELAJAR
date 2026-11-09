@extends('layouts.app')

@section('title', 'Daftar Transaksi')
@section('page_title', 'Catatan Transaksi')
@section('page_subtitle', 'Kelola semua pemasukan dan pengeluaran keuanganmu')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Summary Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Daftar Transaksi</h1>
            <p class="text-xs text-slate-400 mt-0.5">Semua rekaman arus kas pribadimu</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.export', request()->all()) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-xs transition-colors" title="Ekspor data saat ini ke CSV">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor CSV
            </a>

            <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                + Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Filter & Summary Bar Card -->
    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs space-y-5">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama transaksi atau catatan..."
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30">
            </div>

            <!-- Filter Type -->
            <div>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
                    <option value="">Semua Jenis</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan Saja</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran Saja</option>
                </select>
            </div>

            <!-- Filter Category -->
            <div>
                <select name="category_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->type === 'income' ? '🟢' : '🔴' }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Date Preset -->
            <div>
                <select name="date_preset" onchange="toggleCustomDates(this.value)" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
                    <option value="this_month" {{ $datePreset == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="today" {{ $datePreset == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="this_week" {{ $datePreset == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="this_year" {{ $datePreset == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="all" {{ $datePreset == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                    <option value="custom" {{ $datePreset == 'custom' ? 'selected' : '' }}>Pilih Rentang Tanggal</option>
                </select>
            </div>

            <!-- Custom Date Inputs (hidden unless custom selected) -->
            <div id="custom-date-container" class="{{ $datePreset == 'custom' ? '' : 'hidden' }} lg:col-span-3 grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800">
                </div>
            </div>

            <!-- Sort By & Action Buttons -->
            <div class="{{ $datePreset == 'custom' ? 'lg:col-span-2' : 'lg:col-span-5' }} flex flex-wrap items-center justify-between gap-3 pt-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-medium">Urutkan:</span>
                    <select name="sort" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-700 bg-white">
                        <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                        <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Nominal Terbesar</option>
                        <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Nominal Terkecil</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('transactions.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-4 py-1.5 rounded-lg text-white text-xs font-semibold transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Summary Strip for Filtered Data -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-semibold text-emerald-700">Total Pemasukan Filter</span>
                    <p class="text-base font-extrabold text-emerald-800 mt-0.5">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                    ↓
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-semibold text-rose-700">Total Pengeluaran Filter</span>
                    <p class="text-base font-extrabold text-rose-800 mt-0.5">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                    ↑
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-purple-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-semibold text-slate-600">Selisih Arus Kas</span>
                    <p class="text-base font-extrabold {{ $netAmount >= 0 ? 'text-purple-600' : 'text-rose-600' }} mt-0.5">
                        Rp {{ number_format($netAmount, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                    =
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions List Table -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Transaksi</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4 text-right">Nominal</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Transaction Title & Notes -->
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background-color: {{ $tx->category ? $tx->category->color . '20' : '#f1f5f9' }}; color: {{ $tx->category ? $tx->category->color : '#64748b' }};">
                                            @if($tx->type === 'income')
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 text-sm truncate">{{ $tx->title }}</p>
                                            @if($tx->notes)
                                                <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ $tx->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium" style="background-color: {{ $tx->category ? $tx->category->color . '15' : '#f1f5f9' }}; color: {{ $tx->category ? $tx->category->color : '#475569' }};">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $tx->category ? $tx->category->color : '#94a3b8' }}"></span>
                                        {{ $tx->category ? $tx->category->name : 'Umum' }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $tx->date->locale('id')->translatedFormat('d M Y') }}
                                </td>

                                <!-- Payment Method -->
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-medium">
                                        {{ $tx->payment_method ?? 'Tunai' }}
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap font-extrabold text-sm {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }} {{ $tx->formatted_amount }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-purple-600 hover:bg-purple-50 transition-colors" title="Ubah">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi \'{{ $tx->title }}\'? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $transactions->links() }}
            </div>
        @else
            <!-- Empty state -->
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Tidak Ada Transaksi Ditemukan</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-5">
                    Tidak ada transaksi yang cocok dengan kriteria filter pencarian Anda, atau Anda belum pernah mencatat transaksi.
                </p>
                <div class="flex items-center justify-center gap-3">
                    @if(request()->hasAny(['search', 'type', 'category_id', 'start_date', 'end_date']))
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                            Hapus Filter
                        </a>
                    @endif
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Catat Transaksi Sekarang
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleCustomDates(val) {
    const box = document.getElementById('custom-date-container');
    if (val === 'custom') {
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
}
</script>
@endpush
