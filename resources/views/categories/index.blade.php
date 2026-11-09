@extends('layouts.app')

@section('title', 'Kategori Transaksi')
@section('page_title', 'Manajemen Kategori')
@section('page_subtitle', 'Kelola kelompok kategori untuk pemasukan dan pengeluaranmu')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Kategori Keuangan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kategori yang tersedia untuk transaksi Anda</p>
        </div>
    </div>

    <!-- Two-Column Layout: Income vs Expense -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column 1: Kategori Pemasukan -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        ↓
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-slate-800">Kategori Pemasukan</h2>
                        <span class="text-[11px] text-slate-400">Total: {{ $incomeCategories->count() }} Kategori</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                    Pemasukan
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($incomeCategories as $cat)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shadow-xs shrink-0" style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                •
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $cat->name }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column 2: Kategori Pengeluaran -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                        ↑
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-slate-800">Kategori Pengeluaran</h2>
                        <span class="text-[11px] text-slate-400">Total: {{ $expenseCategories->count() }} Kategori</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[11px] font-semibold">
                    Pengeluaran
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($expenseCategories as $cat)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shadow-xs shrink-0" style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                •
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $cat->name }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
