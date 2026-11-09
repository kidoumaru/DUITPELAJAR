@extends('layouts.admin')

@section('title', 'Statistik Aplikasi')
@section('page_title', 'Statistik Aplikasi')
@section('page_subtitle', 'Analisis pertumbuhan dan aktivitas DuitPelajar')

@section('content')
<div class="space-y-6">

    <!-- Page Intro -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Ringkasan Aktivitas Aplikasi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau pertumbuhan pengguna dan aktivitas konten DuitPelajar secara menyeluruh.</p>
        </div>
    </div>

    <!-- Aggregate Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: Total Transaksi -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Seluruh transaksi yang tercatat</p>
        </div>

        <!-- Card 2: Pesan Bantuan -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pesan Bantuan</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($helpMessages, 0, ',', '.') }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Percakapan bantuan masuk</p>
        </div>

        <!-- Card 3: Artikel Terbit -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Artikel Terbit</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($publishedArticles, 0, ',', '.') }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Artikel edukasi yang dipublikasikan</p>
        </div>

        <!-- Card 4: Pengumuman Terbit -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengumuman Terbit</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($publishedAnnouncements, 0, ',', '.') }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Pengumuman yang dipublikasikan</p>
        </div>

    </div>

    <!-- User Growth Chart -->
    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base text-slate-800">Pertumbuhan Pengguna (12 Bulan Terakhir)</h3>
                <p class="text-xs text-slate-400">Jumlah pengguna baru yang terdaftar per bulan</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                <span class="w-3 h-3 rounded-md bg-purple-500 inline-block"></span> Pengguna Baru
            </span>
        </div>
        <div class="h-72 w-full relative">
            <canvas id="statsUserGrowthChart"></canvas>
        </div>
    </div>

    <!-- Users by Period Table -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Pengguna Berdasarkan Periode</h3>
            <p class="text-xs text-slate-400">Rekap jumlah pengguna yang terdaftar dalam setahun terakhir</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Periode</th>
                        <th class="px-6 py-3.5 text-right">Jumlah Pengguna</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($usersByPeriod as $row)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-slate-700">{{ $row->period }}</td>
                            <td class="px-6 py-3.5 text-right font-semibold text-slate-900">{{ number_format($row->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-10 text-center text-sm text-slate-400">Belum ada data pendaftaran pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const growthData = @json($userGrowth);
    const growthCtx = document.getElementById('statsUserGrowthChart');
    if (growthCtx && growthData.length > 0) {
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: growthData.map(item => item.label),
                datasets: [{
                    label: 'Pengguna Baru',
                    data: growthData.map(item => item.count),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#6366f1',
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
                                return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID').format(context.raw) + ' pengguna';
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
                            precision: 0,
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID').format(value);
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
