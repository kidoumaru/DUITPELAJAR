@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Keuangan')
@section('page_subtitle', 'Pantau ringkasan uang saku, pemasukan, dan pengeluaran harianmu')

@section('content')
<div class="space-y-6">

    <!-- Greeting Banner -->
    <div class="relative overflow-hidden rounded-3xl text-white p-6 sm:p-8 shadow-xl" style="background:linear-gradient(135deg,#1a0533 0%,#3b0764 40%,#6d28d9 100%)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-medium text-white/90 mb-3 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $formattedDate }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    {{ $greeting }}, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-sm text-purple-100/90 mt-1 max-w-xl">
                    @if($totalBalance >= 0)
                        Semangat belajar dan menabung! Kelola setiap rupiah uang sakumu dengan penuh kesadaran.
                    @else
                        Perhatian: Pengeluaranmu melebihi total pemasukan. Yuk mulai lebih hemat!
                    @endif
                </p>
            </div>

            <!-- Quick Action Buttons in Banner -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs shadow-md shadow-emerald-700/20 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Pemasukan
                </a>
                <a href="{{ route('transactions.create', ['type' => 'expense']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-semibold text-xs shadow-md shadow-rose-700/20 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                    </svg>
                    + Pengeluaran
                </a>
            </div>
        </div>

        <!-- Decorative background elements -->
        <div class="absolute -right-8 -bottom-12 w-64 h-64 rounded-full blur-2xl pointer-events-none" style="background:rgba(147,51,234,.25)"></div>
        <div class="absolute right-40 -top-10 w-48 h-48 rounded-full blur-xl pointer-events-none" style="background:rgba(192,132,252,.15)"></div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Saldo -->
        <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Saldo</span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,rgba(147,51,234,.15),rgba(124,58,237,.1))">
                    <svg class="w-5 h-5" style="color:#9333ea" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black {{ $totalBalance >= 0 ? 'text-slate-900' : 'text-rose-600' }} tracking-tight">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-400 mt-1.5">Akumulasi seluruh pemasukan - pengeluaran</p>
        </div>

        <!-- Card 2: Pemasukan Bulan Ini -->
        <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Pemasukan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 tracking-tight">
                Rp {{ number_format($monthIncome, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-400 mt-1.5">Uang saku, beasiswa, hadiah, dll.</p>
        </div>

        <!-- Card 3: Pengeluaran Bulan Ini -->
        <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Pengeluaran</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-rose-600 tracking-tight">
                Rp {{ number_format($monthExpense, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-400 mt-1.5">Jajan, ongkos, alat tulis, dll.</p>
        </div>

        <!-- Card 4: Sisa -->
        <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">
                    {{ $monthlyBudgetAmount > 0 ? 'Sisa Anggaran' : 'Sisa Uang Saku' }}
                </span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,rgba(147,51,234,.12),rgba(124,58,237,.08))">
                    <svg class="w-5 h-5" style="color:#9333ea" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            @if($monthlyBudgetAmount > 0)
                <div class="text-3xl font-black tracking-tight {{ $budgetRemaining == 0 && $monthExpense >= $monthlyBudgetAmount ? 'text-rose-600' : 'text-purple-600' }}">
                    Rp {{ number_format($budgetRemaining, 0, ',', '.') }}
                </div>
                <div class="mt-2.5 w-full bg-purple-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full {{ $budgetPercentage >= 100 ? 'bg-rose-500' : ($budgetPercentage >= 75 ? 'bg-amber-500' : '') }}" style="width: {{ $budgetPercentage }}%;{{ $budgetPercentage < 75 ? 'background:linear-gradient(to right,#9333ea,#7c3aed)' : '' }}"></div>
                </div>
                <p class="text-xs text-slate-400 mt-1.5">Terpakai {{ $budgetPercentage }}% dari pagu anggaran</p>
            @else
                <div class="text-3xl font-black tracking-tight {{ $monthCashFlow >= 0 ? 'text-purple-600' : 'text-rose-600' }}">
                    Rp {{ number_format($monthCashFlow, 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-400 mt-1.5">Pemasukan dikurangi pengeluaran bulan ini</p>
            @endif
        </div>
    </div>

    <!-- Quick Navigation Bar -->
    <div class="bg-white rounded-2xl p-4 border border-purple-100 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider px-2 py-1 bg-slate-100 rounded-lg">Aksi Cepat:</span>
            <a href="{{ route('transactions.create') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800 px-3 py-1.5 rounded-lg hover:bg-purple-50 transition-colors">
                + Tambah Transaksi
            </a>
            <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-slate-600 hover:text-purple-600 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                🎯 Atur Anggaran
            </a>
            <a href="{{ route('savings-goals.index') }}" class="text-xs font-semibold text-slate-600 hover:text-purple-600 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                🐷 Target Tabungan
            </a>
            <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-slate-600 hover:text-purple-600 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                📈 Laporan Keuangan
            </a>
        </div>
        <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-purple-600 hover:underline flex items-center gap-1">
            Lihat Semua Transaksi
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Cash Flow Last 6 Months (Span 2 cols on lg) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Perbandingan Arus Kas (6 Bulan Terakhir)</h3>
                    <p class="text-xs text-slate-400">Pemasukan vs Pengeluaran per bulan</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                        <span class="w-3 h-3 rounded-md bg-emerald-500 inline-block"></span> Pemasukan
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                        <span class="w-3 h-3 rounded-md bg-rose-500 inline-block"></span> Pengeluaran
                    </span>
                </div>
            </div>
            <div class="h-72 w-full relative">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Expense by Category This Month (1 col) -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-base text-slate-800">Pengeluaran per Kategori</h3>
                <p class="text-xs text-slate-400 mb-4">Distribusi pengeluaran bulan ini</p>

                @if($categoryBreakdown->count() > 0)
                    <div class="h-56 w-full relative flex items-center justify-center">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2 max-h-40 overflow-y-auto pr-1">
                        @foreach($categoryBreakdown->take(4) as $cat)
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2 truncate text-slate-600">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat->category_color }}"></span>
                                    <span class="truncate">{{ $cat->category_name }}</span>
                                </span>
                                <span class="font-semibold text-slate-800 shrink-0">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-center p-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-600">Belum Ada Pengeluaran</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Catat pengeluaran bulan ini untuk melihat distribusinya.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Daily Expense Trend 30 Days -->
    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base text-slate-800">Tren Pengeluaran Harian (30 Hari Terakhir)</h3>
                <p class="text-xs text-slate-400">Pantau fluktuasi belanjamu setiap hari</p>
            </div>
        </div>
        <div class="h-56 w-full relative">
            <canvas id="dailyTrendChart"></canvas>
        </div>
    </div>

    <!-- Bottom Section: Recent Transactions & Savings Goals -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Transactions (2 cols on lg) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Transaksi Terbaru</h3>
                    <p class="text-xs text-slate-400">Catatan keuangan terakhirmu</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($recentTransactions->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($recentTransactions as $tx)
                        <div class="py-3.5 flex items-center justify-between gap-4 hover:bg-slate-50/60 rounded-xl px-2 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-xs" style="background-color: {{ $tx->category ? $tx->category->color . '15' : '#f1f5f9' }}; color: {{ $tx->category ? $tx->category->color : '#64748b' }};">
                                    @if($tx->type === 'income')
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $tx->title }}</p>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium" style="background-color: {{ $tx->category ? $tx->category->color . '20' : '#e2e8f0' }}; color: {{ $tx->category ? $tx->category->color : '#475569' }};">
                                            {{ $tx->category ? $tx->category->name : 'Umum' }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $tx->date->locale('id')->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="font-extrabold text-sm {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }} {{ $tx->formatted_amount }}
                                </span>
                                <div class="flex items-center justify-end gap-1.5 mt-1">
                                    <a href="{{ route('transactions.edit', $tx) }}" class="text-[11px] text-slate-400 hover:text-purple-600 p-0.5" title="Ubah Transaksi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="py-12 px-4 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Belum Ada Transaksi Tercatat</h4>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-4">
                        Ayo mulai catat uang saku pertama atau jajan harianmu agar keuanganmu selalu terpantau rapi!
                    </p>
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Catat Transaksi Pertama
                    </a>
                </div>
            @endif
        </div>

        <!-- Savings Goals Glance (1 col on lg) -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-800">Target Tabungan</h3>
                        <p class="text-xs text-slate-400">Raih impian belanjamu</p>
                    </div>
                    <a href="{{ route('savings-goals.index') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800">
                        Kelola &rarr;
                    </a>
                </div>

                @if($savingsGoals->count() > 0)
                    <div class="space-y-4">
                        @foreach($savingsGoals as $goal)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="flex items-center justify-between mb-1.5">
                                    <h4 class="font-bold text-xs text-slate-800 truncate">{{ $goal->name }}</h4>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $goal->status === 'achieved' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $goal->status === 'achieved' ? 'Tercapai 🎉' : $goal->percentage . '%' }}
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 mb-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $goal->percentage }}%; background:linear-gradient(to right,#9333ea,#7c3aed)"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500">
                                    <span>{{ $goal->formatted_current }}</span>
                                    <span class="text-slate-400">dari {{ $goal->formatted_target }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 mx-auto flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Belum Ada Target</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 mb-3">Punya barang impian? Sepatu baru, laptop, atau buku?</p>
                        <a href="{{ route('savings-goals.index') }}" class="text-xs font-semibold text-purple-600 hover:underline">
                            + Buat Target Tabungan
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('savings-goals.index') }}" class="text-xs font-medium text-slate-500 hover:text-purple-600">
                    Lihat semua target tabungan &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Cash Flow 6 Months Chart
    const cashFlowData = @json($monthsChart);
    const cfLabels = cashFlowData.map(item => item.label);
    const cfIncome = cashFlowData.map(item => item.income);
    const cfExpense = cashFlowData.map(item => item.expense);

    const cfCtx = document.getElementById('cashFlowChart');
    if (cfCtx) {
        new Chart(cfCtx, {
            type: 'bar',
            data: {
                labels: cfLabels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: cfIncome,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    },
                    {
                        label: 'Pengeluaran',
                        data: cfExpense,
                        backgroundColor: '#f43f5e',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + ' rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Category Breakdown Chart
    const catData = @json($categoryBreakdown);
    const catCtx = document.getElementById('categoryChart');
    if (catCtx && catData.length > 0) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catData.map(c => c.category_name),
                datasets: [{
                    data: catData.map(c => c.total),
                    backgroundColor: catData.map(c => c.category_color || '#3b82f6'),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Daily Trend Chart
    const dailyData = @json($dailyTrend);
    const dailyCtx = document.getElementById('dailyTrendChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [{
                    label: 'Pengeluaran Harian',
                    data: dailyData.map(d => d.expense),
                    borderColor: '#9333ea',
                    backgroundColor: 'rgba(147, 51, 234, 0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#9333ea',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Pengeluaran: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + ' rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
