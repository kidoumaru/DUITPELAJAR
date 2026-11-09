@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')
@section('page_subtitle', 'Kelola akun pengguna DuitPelajar')

@section('content')
<div class="space-y-6">

    <!-- Page Heading -->
    <div>
        <h1 class="text-xl sm:text-3xl font-black tracking-tight text-slate-800">Manajemen Pengguna</h1>
        <p class="text-xs text-slate-400 mt-0.5">Kelola akun pengguna DuitPelajar</p>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-purple-100 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pengguna..."
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 text-xs text-slate-800 placeholder-slate-400 bg-purple-50/30">
            </div>

            <div class="flex items-center gap-2">
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Reset
                    </a>
                @endif
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-semibold text-xs transition-colors" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 3px 10px rgba(147,51,234,.35)"">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xs overflow-hidden">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Nama</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4">Jenjang</th>
                            <th class="py-3.5 px-4">Peran</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Nama -->
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0 shadow-xs">
                                            {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 text-sm truncate">{{ $user->name }}</p>
                                            <p class="text-[11px] text-slate-400 truncate sm:hidden">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $user->email }}
                                </td>

                                <!-- Jenjang -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($user->school_level)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-medium text-slate-600">
                                            {{ $user->school_level }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>

                                <!-- Peran -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
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
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
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
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Lihat -->
                                        <a href="{{ route('admin.users.show', $user) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-purple-600 hover:bg-purple-50 transition-colors" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        <!-- Nonaktifkan / Aktifkan -->
                                        @if($user->is_active)
                                            <form action="{{ route('admin.users.deactivate', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Nonaktifkan" onclick="return confirm('Nonaktifkan akun \'{{ $user->name }}\'? Pengguna tidak akan dapat masuk.')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="Aktifkan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Hapus (disembunyikan untuk admin) -->
                                        @if(!($user->role && $user->role->value === 'admin'))
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus akun \'{{ $user->name }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 bg-purple-50/30">
                {{ $users->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Tidak Ada Pengguna Ditemukan</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-5">
                    @if(request('search'))
                        Tidak ada pengguna yang cocok dengan pencarian "{{ request('search') }}".
                    @else
                        Belum ada pengguna yang terdaftar di aplikasi.
                    @endif
                </p>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Hapus Pencarian
                    </a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
