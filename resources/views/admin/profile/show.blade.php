@extends('layouts.admin')

@section('title', 'Profil Admin')
@section('page_title', 'Profil Admin')
@section('page_subtitle', 'Kelola informasi akun dan keamanan administrator')

@section('content')
<div class="space-y-6">

    <!-- Page Heading -->
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Profil Admin</h1>
        <p class="text-xs text-slate-400 mt-0.5">Kelola informasi akun dan keamanan administrator</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ================================================
             CARD 1: Informasi Profil
             ================================================ -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Informasi Profil</h3>
                        <p class="text-[11px] text-slate-400">Perbarui nama dan alamat email akun Anda</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <!-- Nama -->
                <div>
                    <label for="profile_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        id="profile_name"
                        name="name"
                        value="{{ old('name', $admin->name) }}"
                        required
                        placeholder="Nama lengkap Anda"
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('name') border-rose-400 bg-rose-50/50 @else border-slate-200 bg-purple-50/30 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400">
                    @error('name')
                        <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="profile_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <input
                        type="email"
                        id="profile_email"
                        name="email"
                        value="{{ old('email', $admin->email) }}"
                        required
                        placeholder="email@contoh.com"
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('email') border-rose-400 bg-rose-50/50 @else border-slate-200 bg-purple-50/30 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400">
                    @error('email')
                        <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-1">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- ================================================
             CARD 3: Foto Profil
             ================================================ -->
        <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden h-fit">
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Foto Profil</h3>
                        <p class="text-[11px] text-slate-400">Ubah foto profil Anda</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5 space-y-5">
                <!-- Current Photo / Initials -->
                <div class="flex flex-col items-center gap-3">
                    @if($admin->photo)
                        <img
                            src="{{ asset('storage/' . $admin->photo) }}"
                            alt="{{ $admin->name }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-purple-100 shadow-sm">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-extrabold text-2xl flex items-center justify-center shadow-sm">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-center">
                        <p class="font-bold text-sm text-slate-800">{{ $admin->name }}</p>
                        @if($admin->role)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-100 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                Administrator
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Upload Form -->
                <form action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label for="photo_upload" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pilih Foto Baru
                        </label>
                        <input
                            type="file"
                            id="photo_upload"
                            name="photo"
                            accept="image/*"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 file:font-semibold file:text-xs hover:file:bg-purple-100 transition-colors cursor-pointer">
                        @error('photo')
                            <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, GIF. Maks. 2MB.</p>
                    </div>
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Unggah Foto
                    </button>
                </form>
            </div>
        </div>

        <!-- ================================================
             CARD 2: Ganti Kata Sandi
             ================================================ -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Ganti Kata Sandi</h3>
                        <p class="text-[11px] text-slate-400">Pastikan kata sandi baru minimal 8 karakter</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.profile.password') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <!-- Kata Sandi Saat Ini -->
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Saat Ini
                    </label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        placeholder="Masukkan kata sandi saat ini"
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('current_password') border-rose-400 bg-rose-50/50 @else border-slate-200 bg-purple-50/30 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400">
                    @error('current_password')
                        <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Kata Sandi Baru -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Minimal 8 karakter"
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50/50 @else border-slate-200 bg-purple-50/30 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400">
                    @error('password')
                        <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Konfirmasi Kata Sandi Baru -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        placeholder="Ulangi kata sandi baru"
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('password_confirmation') border-rose-400 bg-rose-50/50 @else border-slate-200 bg-purple-50/30 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400">
                    @error('password_confirmation')
                        <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-1">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
