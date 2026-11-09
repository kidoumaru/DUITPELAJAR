@extends('layouts.app')

@section('title', 'Target Tabungan')
@section('page_title', 'Target Tabungan Impian')
@section('page_subtitle', 'Nabung sedikit demi sedikit untuk wujudkan barang atau cita-citamu')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Overview -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-3xl font-black tracking-tight text-slate-800">Target Tabungan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola impian belanja: laptop, sepatu, buku, atau dana darurat</p>
        </div>

        <button type="button" onclick="openModal('modal-add-goal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            + Target Tabungan Baru
        </button>
    </div>

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Sasaran Target</span>
            <p class="text-3xl font-black tracking-tight text-slate-900 mt-1">Rp {{ number_format($totalTarget, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Akumulasi seluruh target tabunganmu</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Dana Terkumpul</span>
            <p class="text-3xl font-black tracking-tight text-emerald-600 mt-1">Rp {{ number_format($totalCollected, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Simpanan yang sudah disisihkan</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Target Berjalan</span>
            <p class="text-3xl font-black tracking-tight text-purple-600 mt-1">{{ $inProgressCount }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Sedang dalam proses pengumpulan dana</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-xs">
            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Target Tercapai 🎉</span>
            <p class="text-3xl font-black tracking-tight text-blue-600 mt-1">{{ $achievedCount }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Impian yang sudah sukses terkumpul</p>
        </div>
    </div>

    <!-- Goals List Grid -->
    @if($goals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($goals as $goal)
                <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <!-- Header & Badge -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-bold text-base text-slate-800 line-clamp-1">{{ $goal->name }}</h3>
                                @if($goal->target_date)
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Target: {{ $goal->target_date->locale('id')->translatedFormat('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <span class="shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-full {{ $goal->status === 'achieved' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $goal->status === 'achieved' ? 'Tercapai 🎉' : $goal->percentage . '%' }}
                            </span>
                        </div>

                        @if($goal->notes)
                            <p class="text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mb-4 line-clamp-2">
                                {{ $goal->notes }}
                            </p>
                        @endif

                        <!-- Amounts -->
                        <div class="space-y-1 mb-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">Terkumpul:</span>
                                <span class="font-bold text-emerald-600">{{ $goal->formatted_current }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">Target Akhir:</span>
                                <span class="font-semibold text-slate-700">{{ $goal->formatted_target }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">Kurang:</span>
                                <span class="font-bold text-slate-500">
                                    Rp {{ number_format($goal->remaining_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2.5 mb-4 overflow-hidden">
                            <div class="h-2.5 rounded-full bg-gradient-to-r {{ $goal->status === 'achieved' ? 'from-emerald-500 to-teal-500' : 'from-indigo-500 to-blue-500' }} transition-all duration-500"
                                style="width: {{ $goal->percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Bottom Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="openDepositModal({{ $goal->id }}, '{{ addslashes($goal->name) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-600 font-semibold text-xs transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            + Setor Dana
                        </button>

                        <div class="flex items-center gap-1">
                            <button type="button" onclick="openEditModal({{ $goal->id }}, '{{ addslashes($goal->name) }}', {{ $goal->target_amount }}, {{ $goal->current_amount }}, '{{ $goal->target_date ? $goal->target_date->format('Y-m-d') : '' }}', '{{ addslashes($goal->notes ?? '') }}')" class="p-1.5 text-slate-400 hover:text-purple-600 rounded-lg hover:bg-slate-100 transition-colors" title="Ubah Target">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            <form action="{{ route('savings-goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('Hapus target tabungan \'{{ $goal->name }}\'?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus Target">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="py-16 px-4 text-center bg-white rounded-3xl border border-purple-100">
            <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h4 class="font-bold text-slate-800 text-base">Belum Ada Target Tabungan</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-5">
                Ingin beli sepatu baru, buku olimpiade, ganti smartphone, atau bayar biaya kursus? Buat target tabunganmu sekarang dan capai perlahan!
            </p>
            <button type="button" onclick="openModal('modal-add-goal')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Target Pertamamu
            </button>
        </div>
    @endif

</div>

<!-- Modal: Tambah Target Baru -->
<div id="modal-add-goal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-base text-slate-800">Target Tabungan Baru</h3>
            <button type="button" onclick="closeModal('modal-add-goal')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('savings-goals.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="goal_name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Target Impian <span class="text-rose-500">*</span></label>
                <input type="text" id="goal_name" name="name" required placeholder="Misal: Sepatu Futsal, Buku Tulis Semester 2"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="goal_target_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Target (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" id="goal_target_amount" name="target_amount" min="1000" step="1000" required placeholder="Contoh: 350000"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="goal_current_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Saldo Awal yang Sudah Ada (Rp)</label>
                <input type="number" id="goal_current_amount" name="current_amount" min="0" step="1000" value="0" placeholder="0 jika baru mulai"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="goal_target_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Target Tanggal Tercapai (Opsional)</label>
                <input type="date" id="goal_target_date" name="target_date"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="goal_notes" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Motivasi</label>
                <textarea id="goal_notes" name="notes" rows="2" placeholder="Tuliskan motivasi nabungmu..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('modal-add-goal')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-white font-semibold text-xs" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)">
                    Simpan Target
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Setor Dana Tabungan (Explicit Allocation) -->
<div id="modal-deposit" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 class="font-bold text-base text-slate-800">Setor Simpanan Tabungan</h3>
                <p id="deposit-goal-title" class="text-xs text-purple-600 font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-deposit')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="deposit-form" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="deposit_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Setoran (Rp) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                        Rp
                    </div>
                    <input type="number" id="deposit_amount" name="amount" min="1000" step="1000" required placeholder="Contoh: 20000"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                </div>
            </div>

            <!-- Explicit Checkbox to Prevent Double Counting -->
            <div class="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-100/80">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="record_as_expense" value="1" checked class="mt-0.5 w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-indigo-900 block">Catat sebagai Transaksi Pengeluaran</span>
                        <span class="text-[11px] text-purple-700/80 block mt-0.5 leading-relaxed">
                            Jika dicentang, sistem otomatis mencatat transaksi pengeluaran (kategori: Tabungan) sehingga saldo dompetmu berkurang sesuai dana yang disimpan.
                        </span>
                    </div>
                </label>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('modal-deposit')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-200">
                    Setor Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Ubah Target -->
<div id="modal-edit-goal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-base text-slate-800">Ubah Target Tabungan</h3>
            <button type="button" onclick="closeModal('modal-edit-goal')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="edit-goal-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_goal_name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Target <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_goal_name" name="name" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="edit_goal_target_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Target (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" id="edit_goal_target_amount" name="target_amount" min="1000" step="1000" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="edit_goal_current_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Terkumpul (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" id="edit_goal_current_amount" name="current_amount" min="0" step="1000" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="edit_goal_target_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Target Tanggal</label>
                <input type="date" id="edit_goal_target_date" name="target_date"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="edit_goal_notes" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan</label>
                <textarea id="edit_goal_notes" name="notes" rows="2"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('modal-edit-goal')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-white font-semibold text-xs" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)">
                    Perbarui Target
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDepositModal(id, title) {
    document.getElementById('deposit-form').action = `/savings-goals/${id}/deposit`;
    document.getElementById('deposit-goal-title').textContent = title;
    document.getElementById('deposit_amount').value = '';
    openModal('modal-deposit');
}

function openEditModal(id, name, target, current, targetDate, notes) {
    document.getElementById('edit-goal-form').action = `/savings-goals/${id}`;
    document.getElementById('edit_goal_name').value = name;
    document.getElementById('edit_goal_target_amount').value = target;
    document.getElementById('edit_goal_current_amount').value = current;
    document.getElementById('edit_goal_target_date').value = targetDate || '';
    document.getElementById('edit_goal_notes').value = notes || '';
    openModal('modal-edit-goal');
}
</script>
@endpush
