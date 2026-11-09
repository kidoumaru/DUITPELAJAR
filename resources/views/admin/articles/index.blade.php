@extends('layouts.admin')

@section('title', 'Artikel Edukasi')
@section('page_title', 'Artikel Edukasi')
@section('page_subtitle', 'Kelola konten artikel edukasi keuangan')

@section('content')
<div class="space-y-6">

    <!-- Page Heading & Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Artikel Edukasi</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola konten artikel edukasi keuangan</p>
        </div>
        <button
            type="button"
            onclick="document.getElementById('modal-add-article').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)" w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Buat Artikel
        </button>
    </div>

    <!-- Articles Table Card -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        @if($articles->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Judul</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Tanggal Publikasi</th>
                            <th class="py-3.5 px-4">Penulis</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($articles as $article)
                            <tr class="hover:bg-slate-50/80 transition-colors">

                                <!-- Judul -->
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        @if($article->thumbnail)
                                            <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                                 alt="{{ $article->title }}"
                                                 class="w-10 h-10 rounded-lg object-cover shrink-0 border border-slate-100">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-purple-50 text-indigo-400 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <p class="font-semibold text-slate-800 text-sm line-clamp-2 max-w-xs">{{ $article->title }}</p>
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($article->category)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-medium text-slate-600">
                                            {{ $article->category }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($article->status->value === 'published')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Terbit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <!-- Tanggal Publikasi -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    @if($article->published_at)
                                        {{ \Carbon\Carbon::parse($article->published_at)->locale('id')->translatedFormat('d M Y, H:i') }}
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>

                                <!-- Penulis -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($article->author)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-bold text-[9px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($article->author->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="text-slate-600 text-[11px]">{{ $article->author->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <!-- Edit -->
                                        <button
                                            type="button"
                                            onclick="editArticle(
                                                {{ $article->id }},
                                                '{{ addslashes($article->title) }}',
                                                '{{ addslashes($article->content) }}',
                                                '{{ addslashes($article->category ?? '') }}',
                                                '{{ $article->status->value }}',
                                                '{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('Y-m-d\TH:i') : '' }}'
                                            )"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-purple-600 hover:bg-purple-50 transition-colors"
                                            title="Edit Artikel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Hapus -->
                                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                title="Hapus Artikel"
                                                onclick="return confirm('Hapus artikel \'{{ addslashes($article->title) }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $articles->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Belum Ada Artikel</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-5">
                    Mulai buat artikel edukasi keuangan pertama untuk para pelajar.
                </p>
                <button
                    type="button"
                    onclick="document.getElementById('modal-add-article').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Artikel Pertama
                </button>
            </div>
        @endif
    </div>

</div>

<!-- ============================================================
     MODAL: Tambah Artikel
     ============================================================ -->
<div id="modal-add-article"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Buat Artikel Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi form di bawah untuk menambahkan artikel edukasi</p>
            </div>
            <button
                type="button"
                onclick="document.getElementById('modal-add-article').classList.add('hidden')"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf

            <!-- Judul -->
            <div>
                <label for="add_title" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Judul <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="add_title"
                    name="title"
                    required
                    placeholder="Masukkan judul artikel"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30">
            </div>

            <!-- Konten -->
            <div>
                <label for="add_content" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Konten <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="add_content"
                    name="content"
                    required
                    rows="5"
                    placeholder="Tulis konten artikel di sini..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30 resize-none"></textarea>
            </div>

            <!-- Kategori -->
            <div>
                <label for="add_category" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="add_category"
                    name="category"
                    required
                    placeholder="Tips Menabung"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30">
            </div>

            <!-- Status -->
            <div>
                <label for="add_status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select
                    id="add_status"
                    name="status"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
                    <option value="published">Terbit</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <!-- Tanggal Publikasi -->
            <div>
                <label for="add_published_at" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Publikasi <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input
                    type="datetime-local"
                    id="add_published_at"
                    name="published_at"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
            </div>

            <!-- Gambar Thumbnail -->
            <div>
                <label for="add_thumbnail" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Gambar Thumbnail <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input
                    type="file"
                    id="add_thumbnail"
                    name="thumbnail"
                    accept="image/*"
                    class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 file:font-semibold file:text-xs hover:file:bg-purple-100 transition-colors cursor-pointer">
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-2 pt-2">
                <button
                    type="button"
                    onclick="document.getElementById('modal-add-article').classList.add('hidden')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Artikel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: Edit Artikel
     ============================================================ -->
<div id="modal-edit-article"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Edit Artikel</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi artikel edukasi</p>
            </div>
            <button
                type="button"
                onclick="document.getElementById('modal-edit-article').classList.add('hidden')"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="form-edit-article" action="" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')

            <!-- Judul -->
            <div>
                <label for="edit_title" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Judul <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="edit_title"
                    name="title"
                    required
                    placeholder="Masukkan judul artikel"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30">
            </div>

            <!-- Konten -->
            <div>
                <label for="edit_content" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Konten <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="edit_content"
                    name="content"
                    required
                    rows="5"
                    placeholder="Tulis konten artikel di sini..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30 resize-none"></textarea>
            </div>

            <!-- Kategori -->
            <div>
                <label for="edit_category" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="edit_category"
                    name="category"
                    required
                    placeholder="Tips Menabung"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30">
            </div>

            <!-- Status -->
            <div>
                <label for="edit_status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select
                    id="edit_status"
                    name="status"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
                    <option value="published">Terbit</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <!-- Tanggal Publikasi -->
            <div>
                <label for="edit_published_at" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Publikasi <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input
                    type="datetime-local"
                    id="edit_published_at"
                    name="published_at"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 bg-purple-50/30">
            </div>

            <!-- Gambar Thumbnail -->
            <div>
                <label for="edit_thumbnail" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Gambar Thumbnail
                </label>
                <input
                    type="file"
                    id="edit_thumbnail"
                    name="thumbnail"
                    accept="image/*"
                    class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 file:font-semibold file:text-xs hover:file:bg-purple-100 transition-colors cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti gambar thumbnail.</p>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-2 pt-2">
                <button
                    type="button"
                    onclick="document.getElementById('modal-edit-article').classList.add('hidden')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Perbarui Artikel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editArticle(id, title, content, category, status, published_at) {
        const form = document.getElementById('form-edit-article');
        form.action = '/admin/articles/' + id;

        document.getElementById('edit_title').value = title;
        document.getElementById('edit_content').value = content;
        document.getElementById('edit_category').value = category;
        document.getElementById('edit_published_at').value = published_at;

        const statusSelect = document.getElementById('edit_status');
        for (let i = 0; i < statusSelect.options.length; i++) {
            statusSelect.options[i].selected = (statusSelect.options[i].value === status);
        }

        document.getElementById('edit_thumbnail').value = '';
        document.getElementById('modal-edit-article').classList.remove('hidden');
    }
</script>
@endpush
