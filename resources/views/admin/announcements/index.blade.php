@extends('layouts.admin')

@section('title', 'Pengumuman')
@section('page_title', 'Pengumuman')
@section('page_subtitle', 'Buat dan kelola pengumuman untuk pengguna DuitPelajar')

@section('content')
<div class="space-y-6">

    <!-- Top Bar -->
    <div class="flex justify-end">
        <button type="button" onclick="openModal('modal-add-announcement')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Pengumuman
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Judul</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Tanggal Publikasi</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500">Penulis</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($announcements as $announcement)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <!-- Judul -->
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800 text-xs max-w-xs truncate">{{ $announcement->title }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 max-w-xs truncate">{{ Str::limit(strip_tags($announcement->body), 60) }}</p>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                @if($announcement->status->value === 'published')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Terbit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal Publikasi -->
                            <td class="px-5 py-4 text-xs text-slate-600 whitespace-nowrap">
                                @if($announcement->published_at)
                                    {{ $announcement->published_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            <!-- Penulis -->
                            <td class="px-5 py-4 text-xs text-slate-600 whitespace-nowrap">
                                {{ $announcement->author?->name ?? '—' }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit -->
                                    <button type="button"
                                        onclick="editAnnouncement(
                                            {{ $announcement->id }},
                                            '{{ addslashes($announcement->title) }}',
                                            '{{ addslashes($announcement->body) }}',
                                            '{{ $announcement->status->value }}',
                                            '{{ $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '' }}'
                                        )"
                                        class="p-1.5 text-slate-400 hover:text-purple-600 rounded-lg hover:bg-purple-50 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Hapus -->
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus pengumuman \'{{ addslashes($announcement->title) }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-sm text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                    <span>Belum ada pengumuman.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Modal Tambah Pengumuman --}}
<div id="modal-add-announcement" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    onclick="closeModal('modal-add-announcement')">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-slate-800 text-base">Buat Pengumuman</h3>
            <button type="button" onclick="closeModal('modal-add-announcement')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Judul <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Judul pengumuman"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Isi Pengumuman <span class="text-rose-500">*</span></label>
                <textarea name="body" required rows="4" placeholder="Tulis isi pengumuman di sini..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <select name="status" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 bg-white">
                        <option value="draft">Draft</option>
                        <option value="published">Terbit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Publikasi</label>
                    <input type="datetime-local" name="published_at"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-add-announcement')"
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

{{-- Modal Edit Pengumuman --}}
<div id="modal-edit-announcement" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    onclick="closeModal('modal-edit-announcement')">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 class="font-bold text-slate-800 text-base">Edit Pengumuman</h3>
            <button type="button" onclick="closeModal('modal-edit-announcement')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-announcement-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Judul <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_ann_title" name="title" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Isi Pengumuman <span class="text-rose-500">*</span></label>
                <textarea id="edit_ann_body" name="body" required rows="4"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <select id="edit_ann_status" name="status" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 bg-white">
                        <option value="draft">Draft</option>
                        <option value="published">Terbit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Publikasi</label>
                    <input type="datetime-local" id="edit_ann_published_at" name="published_at"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition-colors">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit-announcement')"
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

    function editAnnouncement(id, title, body, status, publishedAt) {
        document.getElementById('edit-announcement-form').action = '/admin/announcements/' + id;
        document.getElementById('edit_ann_title').value = title;
        document.getElementById('edit_ann_body').value = body;
        document.getElementById('edit_ann_status').value = status;
        document.getElementById('edit_ann_published_at').value = publishedAt;
        openModal('modal-edit-announcement');
    }
</script>
@endpush
