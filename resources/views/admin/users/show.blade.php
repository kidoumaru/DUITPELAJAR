@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('page_title', 'Detail Pengguna')
@section('page_subtitle', 'Informasi profil pengguna')

@section('content')
<div class="space-y-6">

    <!-- Page Heading & Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Detail Pengguna</h1>
            <p class="text-xs text-slate-400 mt-0.5">Informasi profil pengguna</p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-xs transition-colors w-fit">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- User Detail Card -->
    <div class="bg-white rounded-2xl border border-purple-100 shadow-xs overflow-hidden">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-purple-600 to-violet-600 px-6 py-8 sm:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm text-white font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-md">
                    {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-white truncate">{{ $user->name }}</h2>
                    <p class="text-sm text-purple-100 truncate">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <!-- Peran Badge -->
                        @if($user->role && $user->role->value === 'admin')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                Admin
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-white/70"></span>
                                User
                            </span>
                        @endif

                        <!-- Status Badge -->
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-400/20 backdrop-blur-sm text-emerald-50 border border-emerald-300/40">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-rose-400/20 backdrop-blur-sm text-rose-50 border border-rose-300/40">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Rows -->
        <div class="p-6 sm:p-8">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">

                <!-- Nama -->
                <div>
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Nama
                    </dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-800">{{ $user->name }}</dd>
                </div>

                <!-- Email -->
                <div>
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email
                    </dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-800 break-all">{{ $user->email }}</dd>
                </div>

                <!-- Jenjang Sekolah -->
                <div>
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        Jenjang Sekolah
                    </dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-800">
                        @if($user->school_level)
                            <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium">{{ $user->school_level }}</span>
                        @else
                            <span class="text-slate-400 font-normal">Belum diisi</span>
                        @endif
                    </dd>
                </div>

                <!-- Peran -->
                <div>
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Peran
                    </dt>
                    <dd class="mt-1.5">
                        @if($user->role && $user->role->value === 'admin')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                Admin
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                User
                            </span>
                        @endif
                    </dd>
                </div>

                <!-- Status -->
                <div>
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status
                    </dt>
                    <dd class="mt-1.5">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Nonaktif
                            </span>
                        @endif
                    </dd>
                </div>

                <!-- Tanggal Daftar -->
                <div>
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tanggal Daftar
                    </dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-800">
                        {{ $user->created_at->locale('id')->translatedFormat('d F Y') }}
                    </dd>
                </div>

            </dl>

            <!-- Bottom Action -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>

                @if($user->is_active)
                    <form action="{{ route('admin.users.deactivate', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold text-xs transition-colors" onclick="return confirm('Nonaktifkan akun \'{{ $user->name }}\'? Pengguna tidak akan dapat masuk.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Nonaktifkan Akun
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold text-xs transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Aktifkan Akun
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
