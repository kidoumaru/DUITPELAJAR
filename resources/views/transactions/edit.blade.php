@extends('layouts.app')

@section('title', 'Ubah Transaksi')
@section('page_title', 'Ubah Transaksi')
@section('page_subtitle', 'Perbarui detail transaksi yang telah tercatat')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Ubah Transaksi</h1>
                <p class="text-xs text-slate-400 mt-0.5">ID Transaksi: #{{ $transaction->id }}</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Transaction Type Toggle -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Jenis Transaksi</label>
                <div class="grid grid-cols-2 gap-3 p-1 rounded-2xl bg-slate-100 border border-slate-200/60">
                    <label id="type-expense-label" class="cursor-pointer">
                        <input type="radio" name="type" value="expense" id="radio-expense"
                            {{ old('type', $transaction->type) == 'expense' ? 'checked' : '' }}
                            onchange="updateTypeMode('expense')" class="sr-only">
                        <div class="py-2.5 px-4 text-center rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 {{ old('type', $transaction->type) == 'expense' ? 'bg-white text-rose-600 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            Pengeluaran
                        </div>
                    </label>

                    <label id="type-income-label" class="cursor-pointer">
                        <input type="radio" name="type" value="income" id="radio-income"
                            {{ old('type', $transaction->type) == 'income' ? 'checked' : '' }}
                            onchange="updateTypeMode('income')" class="sr-only">
                        <div class="py-2.5 px-4 text-center rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 {{ old('type', $transaction->type) == 'income' ? 'bg-white text-emerald-600 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            Pemasukan
                        </div>
                    </label>
                </div>
            </div>

            <!-- Transaction Title -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Transaksi <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $transaction->title) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 placeholder-slate-400">
            </div>

            <!-- Amount with Live Preview -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="amount" class="text-xs font-semibold text-slate-700">Nominal (Rupiah) <span class="text-rose-500">*</span></label>
                    <span id="amount-preview" class="text-xs font-bold text-purple-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                        Rp
                    </div>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" min="1" step="1" required
                        oninput="formatRupiahPreview(this.value)"
                        class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-base font-bold text-slate-800">
                </div>
            </div>

            <!-- Category Selector -->
            <div>
                <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Transaksi <span class="text-rose-500">*</span></label>
                <select id="category_id" name="category_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-type="{{ $cat->type }}"
                            {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->type === 'income' ? '[Pemasukan]' : '[Pengeluaran]' }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date & Payment Method Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800">
                </div>

                <div>
                    <label for="payment_method" class="block text-xs font-semibold text-slate-700 mb-1.5">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 bg-white">
                        <option value="Tunai" {{ old('payment_method', $transaction->payment_method) == 'Tunai' ? 'selected' : '' }}>Tunai / Cash</option>
                        <option value="Transfer Bank" {{ old('payment_method', $transaction->payment_method) == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="GoPay" {{ old('payment_method', $transaction->payment_method) == 'GoPay' ? 'selected' : '' }}>GoPay</option>
                        <option value="OVO" {{ old('payment_method', $transaction->payment_method) == 'OVO' ? 'selected' : '' }}>OVO</option>
                        <option value="DANA" {{ old('payment_method', $transaction->payment_method) == 'DANA' ? 'selected' : '' }}>DANA</option>
                        <option value="ShopeePay" {{ old('payment_method', $transaction->payment_method) == 'ShopeePay' ? 'selected' : '' }}>ShopeePay</option>
                        <option value="Lainnya" {{ old('payment_method', $transaction->payment_method) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 placeholder-slate-400">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 flex items-center justify-end gap-3">
                <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    Perbarui Transaksi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function formatRupiahPreview(val) {
    const num = parseInt(val, 10);
    const label = document.getElementById('amount-preview');
    if (!isNaN(num) && num > 0) {
        label.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
    } else {
        label.textContent = 'Rp 0';
    }
}

function updateTypeMode(type) {
    const expenseLabel = document.querySelector('#type-expense-label div');
    const incomeLabel = document.querySelector('#type-income-label div');

    if (type === 'expense') {
        expenseLabel.className = 'py-2.5 px-4 text-center rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 bg-white text-rose-600 shadow-sm border border-slate-200/50';
        incomeLabel.className = 'py-2.5 px-4 text-center rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-500 hover:text-slate-700';
    } else {
        incomeLabel.className = 'py-2.5 px-4 text-center rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 bg-white text-emerald-600 shadow-sm border border-slate-200/50';
        expenseLabel.className = 'py-2.5 px-4 text-center rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-500 hover:text-slate-700';
    }

    const select = document.getElementById('category_id');
    const options = select.querySelectorAll('option');
    options.forEach(opt => {
        if (!opt.value) return;
        const optType = opt.getAttribute('data-type');
        if (optType === type) {
            opt.style.display = '';
            opt.disabled = false;
        } else {
            opt.style.display = 'none';
            opt.disabled = true;
        }
    });

    const currentOpt = select.selectedOptions[0];
    if (currentOpt && currentOpt.value && currentOpt.getAttribute('data-type') !== type) {
        select.value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const checkedRadio = document.querySelector('input[name="type"]:checked');
    if (checkedRadio) {
        updateTypeMode(checkedRadio.value);
    }
    const amountInput = document.getElementById('amount');
    if (amountInput && amountInput.value) {
        formatRupiahPreview(amountInput.value);
    }
});
</script>
@endpush
