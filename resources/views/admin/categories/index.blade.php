@extends('layouts.admin')

@section('title', 'Manajemen Kategori')
@section('page_title', 'Manajemen Kategori')
@section('page_subtitle', 'Kelola kategori pemasukan dan pengeluaran global')

@section('content')
<div class="space-y-6">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex items-center gap-2">
            <select name="type" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 bg-white">
                <option value="">Semua Jenis</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </form>

        <button type="button" onclick="openModal('modal-add-category')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95"
            style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Nama</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Jenis</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Transaksi</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <!-- Nama -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                                        style="background-color:{{ $cat->color }}20; color:{{ $cat->color }}">
                                        •
                                    </span>
                                    <span class="font-semibold text-slate-800 text-xs">{{ $cat->name }}</span>
                                </div>
                            </td>

                            <!-- Jenis -->
                            <td class="px-5 py-4">
                                @if($cat->type === 'income')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">Pemasukan</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">Pengeluaran</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                @if($cat->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">Nonaktif</span>
                                @endif
                            </td>

                            <!-- Transaksi -->
                            <td class="px-5 py-4 text-xs text-slate-600">{{ number_format($cat->transactions_count) }}</td>

                            <!-- Aksi -->
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit -->
                                    <button type="button"
                                        onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->type }}', '{{ addslashes($cat->icon) }}', '{{ $cat->color }}', {{ $cat->is_active ? 1 : 0 }})"
                                        class="p-1.5 text-slate-400 hover:text-purple-600 rounded-lg hover:bg-purple-50 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Toggle aktif/nonaktif -->
                                    @if($cat->is_active)
                                        <form action="{{ route('admin.categories.deactivate', $cat) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-colors" title="Nonaktifkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.categories.activate', $cat) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition-colors" title="Aktifkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Hapus (hanya jika tidak ada transaksi) -->
                                    @if($cat->transactions_count == 0)
                                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Hapus kategori \'{{ addslashes($cat->name) }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-sm text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>Belum ada kategori.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Modal Tambah Kategori --}}
<div id="modal-add-category" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    onclick="closeModal('modal-add-category')">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-slate-800 text-base">Tambah Kategori</h3>
            <button type="button" onclick="closeModal('modal-add-category')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Makan & Minum"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" checked class="peer sr-only">
                        <div class="py-2 px-3 text-center rounded-xl text-xs font-bold border border-slate-200 peer-checked:bg-rose-50 peer-checked:border-rose-300 peer-checked:text-rose-700 text-slate-600 transition-all">
                            Pengeluaran
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" class="peer sr-only">
                        <div class="py-2 px-3 text-center rounded-xl text-xs font-bold border border-slate-200 peer-checked:bg-emerald-50 peer-checked:border-emerald-300 peer-checked:text-emerald-700 text-slate-600 transition-all">
                            Pemasukan
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ikon (teks)</label>
                    <input type="text" name="icon" placeholder="tag"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Warna</label>
                    <input type="color" name="color" value="#3b82f6"
                        class="w-full h-10 rounded-xl border border-slate-200 p-0.5 cursor-pointer">
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked
                    class="w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500/30">
                <label for="add_is_active" class="text-xs font-semibold text-slate-700">Aktifkan kategori</label>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-add-category')"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Kategori --}}
<div id="modal-edit-category" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    onclick="closeModal('modal-edit-category')">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-slate-800 text-base">Edit Kategori</h3>
            <button type="button" onclick="closeModal('modal-edit-category')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-category-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" id="edit_type_expense" name="type" value="expense" class="peer sr-only">
                        <div class="py-2 px-3 text-center rounded-xl text-xs font-bold border border-slate-200 peer-checked:bg-rose-50 peer-checked:border-rose-300 peer-checked:text-rose-700 text-slate-600 transition-all">
                            Pengeluaran
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" id="edit_type_income" name="type" value="income" class="peer sr-only">
                        <div class="py-2 px-3 text-center rounded-xl text-xs font-bold border border-slate-200 peer-checked:bg-emerald-50 peer-checked:border-emerald-300 peer-checked:text-emerald-700 text-slate-600 transition-all">
                            Pemasukan
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ikon</label>
                    <input type="text" id="edit_icon" name="icon"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Warna</label>
                    <input type="color" id="edit_color" name="color"
                        class="w-full h-10 rounded-xl border border-slate-200 p-0.5 cursor-pointer">
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                    class="w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500/30">
                <label for="edit_is_active" class="text-xs font-semibold text-slate-700">Aktifkan kategori</label>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit-category')"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = '';
    }

    function editCategory(id, name, type, icon, color, isActive) {
        document.getElementById('edit-category-form').action = '/admin/categories/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_icon').value = icon;
        document.getElementById('edit_color').value = color;
        document.getElementById('edit_is_active').checked = isActive === 1;

        if (type === 'income') {
            document.getElementById('edit_type_income').checked = true;
        } else {
            document.getElementById('edit_type_expense').checked = true;
        }

        openModal('modal-edit-category');
    }
</script>
@endpush
