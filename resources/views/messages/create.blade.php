@extends('layouts.app')

@section('title', 'Kirim Pesan')
@section('page_title', 'Kirim Pesan ke Admin')
@section('page_subtitle', 'Sampaikan pertanyaan atau kendala yang kamu alami')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Formulir Pesan Baru</h1>
                <p class="text-xs text-slate-400 mt-0.5">Admin akan membalas pesanmu secepatnya</p>
            </div>
            <a href="{{ route('messages.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('messages.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Judul Pesan <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    autofocus
                    placeholder="Misal: Tidak bisa masuk akun, Pertanyaan tentang fitur anggaran..."
                    class="w-full px-4 py-2.5 rounded-xl border @error('title') border-rose-400 bg-rose-50/30 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 placeholder-slate-400 transition-colors"
                >
                @error('title')
                    <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Body -->
            <div>
                <label for="body" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Isi Pesan <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="body"
                    name="body"
                    rows="5"
                    required
                    placeholder="Tuliskan detail pertanyaan atau masalah yang ingin kamu sampaikan ke admin..."
                    class="w-full px-4 py-2.5 rounded-xl border @error('body') border-rose-400 bg-rose-50/30 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-sm text-slate-800 placeholder-slate-400 transition-colors resize-none"
                >{{ old('body') }}</textarea>
                @error('body')
                    <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1">Semakin detail pesanmu, semakin cepat admin bisa membantu.</p>
            </div>

            <!-- Actions -->
            <div class="pt-3 flex items-center justify-end gap-3">
                <a href="{{ route('messages.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Pesan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
