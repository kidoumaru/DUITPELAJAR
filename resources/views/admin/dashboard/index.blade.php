@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Admin')
@section('page_subtitle', 'Ringkasan aktivitas aplikasi DuitPelajar')

@section('content')
<div class="space-y-6">

    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

        <!-- Total Pengguna -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background:linear-gradient(135deg,rgba(147,51,234,.15),rgba(124,58,237,.1))">
                <svg class="w-6 h-6" style="color:#9333ea" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalUsers, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Total Pengguna</p>
        </div>

        <!-- Pengguna Aktif -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-black text-emerald-600 tracking-tight">{{ number_format($activeUsers, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Pengguna Aktif</p>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(59,130,246,.1)">
                <svg class="w-6 h-6" style="color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Total Transaksi</p>
        </div>

        <!-- Kategori Pemasukan -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                </svg>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($incomeCategories, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Kategori Pemasukan</p>
        </div>

        <!-- Kategori Pengeluaran -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                </svg>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($expenseCategories, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Kategori Pengeluaran</p>
        </div>

        <!-- Pesan Masuk -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="text-3xl font-black text-amber-600 tracking-tight">{{ number_format($incomingMessages, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Pesan Masuk</p>
        </div>

        <!-- Pengumuman Terbit -->
        <div class="bg-white rounded-2xl border border-purple-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="box-shadow:0 2px 12px rgba(147,51,234,.07)">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(147,51,234,.1)">
                <svg class="w-6 h-6" style="color:#9333ea" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <div class="text-3xl font-black" style="color:#9333ea">{{ number_format($publishedAnnouncements, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1.5">Pengumuman Terbit</p>
        </div>

    </div>

    <!-- Grafik Pertumbuhan Pengguna -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base text-slate-800">Pertumbuhan Pengguna</h3>
                <p class="text-xs text-slate-400">Jumlah pengguna baru per bulan</p>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-600">
                <span class="w-3 h-3 rounded-md bg-purple-500 inline-block"></span> Pengguna Baru
            </div>
        </div>

        @if(!empty($userGrowth))
            <div class="h-72 w-full relative">
                <canvas id="userGrowthChart"></canvas>
            </div>
        @else
            <div class="h-72 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-600">Belum Ada Data</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Data pertumbuhan pengguna akan muncul di sini.</p>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<!-- Chart.js: gunakan CDN sebagai fallback jika belum dimuat via Vite (app.js) -->
<script>
if (typeof window.Chart === 'undefined') {
    (function () {
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
        s.async = false;
        document.head.appendChild(s);
    })();
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function renderUserGrowthChart() {
        if (typeof window.Chart === 'undefined') {
            return setTimeout(renderUserGrowthChart, 50);
        }

        var userGrowth = @json($userGrowth);
        var ugCtx = document.getElementById('userGrowthChart');

        if (ugCtx && userGrowth.length > 0) {
            new Chart(ugCtx, {
                type: 'line',
                data: {
                    labels: userGrowth.map(function (item) { return item.label; }),
                    datasets: [{
                        label: 'Pengguna Baru',
                        data: userGrowth.map(function (item) { return item.count; }),
                        borderColor: '#9333ea',
                        backgroundColor: 'rgba(147, 51, 234, 0.08)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#9333ea'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Pengguna: ' + new Intl.NumberFormat('id-ID').format(context.raw);
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
                                precision: 0,
                                callback: function (value) {
                                    return new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    renderUserGrowthChart();
});
</script>
@endpush
