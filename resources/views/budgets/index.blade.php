@extends('layouts.app')

@section('title', 'Anggaran Bulanan')
@section('page_title', 'Anggaran Bulanan')
@section('page_subtitle', 'Batasi pengeluaranmu agar tidak boros dan uang saku tetap aman')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Month Picker -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-3xl font-black tracking-tight text-slate-800">Anggaran {{ $monthName }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola batas belanja bulanan dan per kategori</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Month/Year Selector Form -->
            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2">
                <select name="month" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white shadow-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::createFromDate($year, $m, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <select name="year" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white shadow-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold shadow-xs transition-colors">
                    Pilih
                </button>
            </form>

            <button type="button" onclick="openModal('modal-set-budget')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                + Atur Anggaran
            </button>
        </div>
    </div>

    <!-- Warning / Exceeded Banners if spending is near or beyond budget -->
    @if($exceededCount > 0)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-rose-900 uppercase tracking-wide">Peringatan: Anggaran Terlampaui!</h4>
                <p class="text-xs text-rose-700 mt-0.5">
                    Terdapat {{ $exceededCount }} pos anggaran yang pengeluarannya telah melebihi batas 100%. Tahan pengeluaranmu hingga bulan depan ya!
                </p>
            </div>
        </div>
    @elseif($warningCount > 0)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wide">Peringatan: Anggaran Mendekati Batas</h4>
                <p class="text-xs text-amber-700 mt-0.5">
                    Pengeluaran pada {{ $warningCount }} pos anggaran sudah mencapai lebih dari 75% batas kuota. Hati-hati jangan sampai boncos!
                </p>
            </div>
        </div>
    @endif

    <!-- Overall Monthly Budget Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Ringkasan Bulan Ini</span>
                <h2 class="text-lg font-bold text-slate-800 mt-0.5">Anggaran Keseluruhan Bulan {{ $monthName }}</h2>
            </div>
            @if($overallBudget)
                <div class="flex items-center gap-2">
                    <button type="button" onclick="editBudget('', {{ $overallBudget->amount }})" class="text-xs font-semibold text-purple-600 hover:text-purple-800 px-3 py-1.5 rounded-lg hover:bg-purple-50">
                        Ubah Pagu
                    </button>
                    <form action="{{ route('budgets.destroy', $overallBudget) }}" method="POST" onsubmit="return confirm('Hapus anggaran keseluruhan bulan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 px-3 py-1.5 rounded-lg hover:bg-rose-50">
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if($totalBudgetLimit > 0)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 my-6">
                <div>
                    <span class="text-xs font-semibold text-slate-400">Total Batas Anggaran</span>
                    <p class="text-2xl font-black text-slate-800 mt-1">Rp {{ number_format($totalBudgetLimit, 0, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400">Total Pengeluaran Aktual</span>
                    <p class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($overallSpent, 0, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400">Sisa Anggaran Tersedia</span>
                    <p class="text-2xl font-black {{ $overallSpent >= $totalBudgetLimit ? 'text-rose-600' : 'text-emerald-600' }} mt-1">
                        Rp {{ number_format($overallRemaining ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Big Progress Bar -->
            <div>
                <div class="flex items-center justify-between text-xs font-bold mb-2">
                    <span class="text-slate-600">Tingkat Penggunaan Anggaran</span>
                    <span class="{{ $overallPercentage >= 100 ? 'text-rose-600' : ($overallPercentage >= 75 ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ $overallPercentage }}%
                        @if($overallPercentage >= 100) (Terlampaui!) @elseif($overallPercentage >= 75) (Mendekati Batas) @else (Aman) @endif
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3.5 p-0.5 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $overallPercentage >= 100 ? 'bg-rose-500' : ($overallPercentage >= 75 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ min(100, $overallPercentage) }}%"></div>
                </div>
            </div>
        @else
            <!-- If no budget is set for this month -->
            <div class="py-8 text-center">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 mx-auto flex items-center justify-center mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Belum Ada Anggaran untuk Bulan {{ $monthName }}</h4>
                <p class="text-xs text-slate-400 max-w-md mx-auto mt-1 mb-4">
                    Menetapkan anggaran bulanan membantu kamu membatasi pengeluaran agar uang sakumu cukup hingga akhir bulan!
                </p>
                <button type="button" onclick="openModal('modal-set-budget')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    + Tetapkan Anggaran Bulan Ini
                </button>
            </div>
        @endif
    </div>

    <!-- Category Budgets Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base text-slate-800">Anggaran per Kategori</h3>
                <p class="text-xs text-slate-400">Batasi belanja spesifik seperti makanan, pulsa, atau nongkrong</p>
            </div>
        </div>

        @if($categoryBudgets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($categoryBudgets as $cb)
                    <div class="bg-white rounded-3xl p-5 border border-purple-100 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shadow-xs shrink-0" style="background-color: {{ $cb->category ? $cb->category->color . '20' : '#f1f5f9' }}; color: {{ $cb->category ? $cb->category->color : '#475569' }};">
                                        •
                                    </span>
                                    <h4 class="font-bold text-sm text-slate-800 truncate">{{ $cb->category ? $cb->category->name : 'Umum' }}</h4>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $cb->percentage >= 100 ? 'bg-rose-100 text-rose-700' : ($cb->percentage >= 75 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ $cb->percentage }}%
                                </span>
                            </div>

                            <div class="space-y-1 mb-3">
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-400">Terpakai:</span>
                                    <span class="font-bold text-rose-600">Rp {{ number_format($cb->spent_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-400">Batas Kuota:</span>
                                    <span class="font-semibold text-slate-700">Rp {{ number_format($cb->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-400">Sisa Kuota:</span>
                                    <span class="font-bold {{ $cb->spent_amount >= $cb->amount ? 'text-rose-600' : 'text-emerald-600' }}">
                                        Rp {{ number_format($cb->remaining_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-100 rounded-full h-2 mb-4 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $cb->percentage >= 100 ? 'bg-rose-500' : ($cb->percentage >= 75 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ min(100, $cb->percentage) }}%"></div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" onclick="editBudget('{{ $cb->category_id }}', {{ $cb->amount }})" class="text-xs font-semibold text-purple-600 hover:text-purple-800">
                                Ubah Nominal
                            </button>
                            <form action="{{ route('budgets.destroy', $cb) }}" method="POST" onsubmit="return confirm('Hapus anggaran kategori ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center rounded-3xl bg-white border border-purple-100">
                <p class="text-xs text-slate-400">Belum ada anggaran per kategori yang diset untuk bulan ini.</p>
                <button type="button" onclick="openModal('modal-set-budget')" class="mt-2 text-xs font-semibold text-purple-600 hover:underline">
                    + Tambah Anggaran Kategori (Misal: Makanan, Transportasi, Hiburan)
                </button>
            </div>
        @endif
    </div>

</div>

<!-- Modal Atur Anggaran -->
<div id="modal-set-budget" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-base text-slate-800">Atur Batas Anggaran</h3>
            <button type="button" onclick="closeModal('modal-set-budget')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label for="budget_category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Pos Anggaran</label>
                <select id="budget_category_id" name="category_id"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 bg-white">
                    <option value="">Semua Pengeluaran (Anggaran Keseluruhan)</option>
                    @foreach($expenseCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Kosongkan kategori untuk mengatur pagu pengeluaran total bulan ini.</p>
            </div>

            <div>
                <label for="budget_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Anggaran (Rp) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                        Rp
                    </div>
                    <input type="number" id="budget_amount" name="amount" min="1000" step="1000" required
                        placeholder="Contoh: 300000"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('modal-set-budget')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-white font-semibold text-xs" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)">
                    Simpan Anggaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editBudget(categoryId, amount) {
    document.getElementById('budget_category_id').value = categoryId;
    document.getElementById('budget_amount').value = amount;
    openModal('modal-set-budget');
}
</script>
@endpush
