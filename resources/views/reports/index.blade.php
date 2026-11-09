@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page_title', 'Laporan Keuangan')
@section('page_subtitle', 'Analisis komprehensif arus kas, kebiasaan belanja, dan rekap keuangan')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-3xl font-black tracking-tight text-slate-800">Laporan & Analisis</h1>
            <p class="text-xs text-slate-400 mt-0.5">Periode: {{ $formattedRange }}</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.export', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-200 transition-all transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Laporan (CSV)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Pilih Periode:</span>
                <select name="preset" onchange="toggleReportCustom(this.value)" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white">
                    <option value="this_month" {{ $preset == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="this_week" {{ $preset == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="last_month" {{ $preset == 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                    <option value="this_year" {{ $preset == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="all" {{ $preset == 'all' ? 'selected' : '' }}>Semua Riwayat</option>
                    <option value="custom" {{ $preset == 'custom' ? 'selected' : '' }}>Rentang Kustom...</option>
                </select>
            </div>

            <div id="report-custom-dates" class="{{ $preset == 'custom' ? '' : 'hidden' }} flex items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                Tampilkan Laporan
            </button>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Pemasukan -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Pemasukan</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Uang masuk pada periode ini</p>
        </div>

        <!-- Card 2: Pengeluaran -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Total Pengeluaran</span>
            <p class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Belanja & jajan yang dikeluarkan</p>
        </div>

        <!-- Card 3: Selisih (Arus Kas Bersih) -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Arus Kas Bersih (Net)</span>
            <p class="text-2xl font-black {{ $netCashFlow >= 0 ? 'text-purple-600' : 'text-rose-600' }} mt-1">
                Rp {{ number_format($netCashFlow, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Pemasukan dikurangi pengeluaran</p>
        </div>

        <!-- Card 4: Jumlah Transaksi -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Banyaknya Transaksi</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalTransactionsCount }} Transaksi</p>
            <p class="text-[11px] text-slate-400 mt-1">Total pencatatan dalam periode</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Period Cash Flow Line/Bar Chart -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Grafik Arus Kas Periode Ini</h3>
                    <p class="text-xs text-slate-400">Tren pemasukan dan pengeluaran</p>
                </div>
            </div>
            <div class="h-72 w-full relative">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        <!-- Category Doughnut -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-base text-slate-800">Proporsi Kategori Belanja</h3>
                <p class="text-xs text-slate-400 mb-4">Kemana saja uang sakumu terpakai</p>

                @if($categoryBreakdown->count() > 0)
                    <div class="h-56 w-full relative flex items-center justify-center">
                        <canvas id="reportCategoryChart"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-center text-xs text-slate-400">
                        Tidak ada transaksi pengeluaran pada periode ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detailed Recaps: Two Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Rekap Kategori -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <h3 class="font-bold text-base text-slate-800 mb-1">Rekap per Kategori Pengeluaran</h3>
            <p class="text-xs text-slate-400 mb-4">Daftar kategori belanja dan persentase terhadap total pengeluaran</p>

            @if($categoryBreakdown->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($categoryBreakdown as $c)
                        @php
                            $pct = $totalExpense > 0 ? round(($c->total / $totalExpense) * 100, 1) : 0;
                        @endphp
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0" style="background-color: {{ $c->color }}20; color: {{ $c->color }};">
                                    •
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $c->name }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $c->count }}x transaksi</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-xs font-extrabold text-slate-800">Rp {{ number_format($c->total, 0, ',', '.') }}</p>
                                <span class="text-[10px] font-semibold text-purple-600">{{ $pct }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada data pengeluaran dalam periode ini.</p>
            @endif
        </div>

        <!-- Rekap Harian -->
        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
            <h3 class="font-bold text-base text-slate-800 mb-1">Rekap Harian</h3>
            <p class="text-xs text-slate-400 mb-4">Rincian aktivitas pemasukan & pengeluaran tiap hari</p>

            @if($dailyRecap->count() > 0)
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
                    @foreach($dailyRecap as $day)
                        @php
                            $dayDate = Carbon\Carbon::parse($day->date);
                            $diff = $day->total_income - $day->total_expense;
                        @endphp
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-800">
                                    {{ $dayDate->locale('id')->translatedFormat('l, d F Y') }}
                                </p>
                                <span class="text-[10px] text-slate-400">{{ $day->total_count }} Transaksi</span>
                            </div>

                            <div class="text-right space-y-0.5">
                                <div class="flex items-center justify-end gap-2 text-xs">
                                    <span class="text-emerald-600 font-semibold">+{{ number_format($day->total_income, 0, ',', '.') }}</span>
                                    <span class="text-slate-300">/</span>
                                    <span class="text-rose-600 font-semibold">-{{ number_format($day->total_expense, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-[10px] font-bold {{ $diff >= 0 ? 'text-purple-600' : 'text-rose-500' }}">
                                    Net: Rp {{ number_format($diff, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada rekaman transaksi dalam periode ini.</p>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleReportCustom(val) {
    const box = document.getElementById('report-custom-dates');
    if (val === 'custom') {
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Report Flow Chart
    const dates = @json($chartDates);
    const inc = @json($chartIncome);
    const exp = @json($chartExpense);

    const rCtx = document.getElementById('reportChart');
    if (rCtx) {
        new Chart(rCtx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: inc,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran',
                        data: exp,
                        backgroundColor: '#f43f5e',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    },
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
                        grid: { display: false },
                        ticks: { maxTicksLimit: 12 }
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

    // 2. Report Category Doughnut
    const catData = @json($categoryBreakdown);
    const rcCtx = document.getElementById('reportCategoryChart');
    if (rcCtx && catData.length > 0) {
        new Chart(rcCtx, {
            type: 'doughnut',
            data: {
                labels: catData.map(c => c.name),
                datasets: [{
                    data: catData.map(c => c.total),
                    backgroundColor: catData.map(c => c.color || '#3b82f6'),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    },
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
});
</script>
@endpush
