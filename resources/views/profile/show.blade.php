@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Profil')
@section('page_subtitle', 'Kelola informasi identitas akun dan keamanan kata sandi')

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Card with User Badge -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs flex flex-col sm:flex-row items-center gap-5">
        <div class="relative group">
            @if($user->photo)
                <img src="{{ Storage::url($user->photo) }}" alt="Foto Profil" class="w-20 h-20 rounded-3xl object-cover shadow-lg">
            @else
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-extrabold text-3xl flex items-center justify-center shadow-lg shadow-purple-200 shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="text-center sm:text-left flex-1 min-w-0">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <h1 class="text-xl font-bold text-slate-800">{{ $user->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700">
                    {{ $user->school_level ?? 'Pelajar' }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $user->email }}</p>
            <p class="text-[11px] text-slate-400 mt-1">
                Bergabung sejak: {{ $user->created_at->locale('id')->translatedFormat('d F Y') }}
            </p>
        </div>
    </div>

    <!-- Form 0: Upload Foto Profil -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <div class="pb-4 border-b border-slate-100 mb-5">
            <h2 class="text-base font-bold text-slate-800">Foto Profil</h2>
            <p class="text-xs text-slate-400 mt-0.5">Unggah foto profil dalam format JPEG, PNG, atau WebP. Maks. 2 MB.</p>
        </div>

        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="shrink-0">
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" id="photo-preview" alt="Foto saat ini" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                    @else
                        <div id="photo-preview-placeholder" class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-purple-600 to-violet-600 text-white font-bold text-2xl flex items-center justify-center">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <img id="photo-preview" src="#" alt="Preview" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 hidden">
                    @endif
                </div>
                <div class="flex-1 space-y-2">
                    <label for="photo" class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Foto Baru</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png,image/webp"
                        class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer"
                        onchange="previewPhoto(this)">
                    @error('photo')
                        <p class="text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400">Format: JPEG, JPG, PNG, WebP. Ukuran maks. 2 MB. Dimensi min. 50×50 piksel.</p>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    Simpan Foto Profil
                </button>
            </div>
        </form>
    </div>

    <!-- Form 1: Ubah Informasi Profil -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <div class="pb-4 border-b border-slate-100 mb-5">
            <h2 class="text-base font-bold text-slate-800">Informasi Pribadi</h2>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui nama lengkap dan status jenjang sekolahmu</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                <input type="email" id="email" value="{{ $user->email }}" disabled
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                <p class="text-[11px] text-slate-400 mt-1">Email digunakan sebagai pengenal akun dan tidak dapat diubah sembarangan.</p>
            </div>

            <div>
                <label for="school_level" class="block text-xs font-semibold text-slate-700 mb-1.5">Jenjang Pendidikan</label>
                <select id="school_level" name="school_level"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 bg-white">
                    <option value="SMP" {{ old('school_level', $user->school_level) == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                    <option value="SMA/SMK" {{ old('school_level', $user->school_level) == 'SMA/SMK' ? 'selected' : '' }}>SMA / SMK / MA</option>
                    <option value="Mahasiswa" {{ old('school_level', $user->school_level) == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa (Perguruan Tinggi)</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-xs transition-all active:scale-95" style="background:linear-gradient(135deg,#9333ea,#7c3aed);box-shadow:0 4px 14px rgba(147,51,234,.4)"">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- Form 2: Ganti Kata Sandi -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xs">
        <div class="pb-4 border-b border-slate-100 mb-5">
            <h2 class="text-base font-bold text-slate-800">Keamanan &amp; Kata Sandi</h2>
            <p class="text-xs text-slate-400 mt-0.5">Pastikan kata sandi barumu kuat dan tidak mudah ditebak</p>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                <input type="password" id="current_password" name="current_password" required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Baru (Min. 8 Karakter) <span class="text-rose-500">*</span></label>
                <input type="password" id="password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs shadow-md shadow-slate-300 transition-all transform active:scale-95">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone: Logout -->
    <div class="bg-rose-50/50 rounded-3xl p-6 border border-rose-100 flex items-center justify-between">
        <div>
            <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider">Keluar dari Sesi Ini</h3>
            <p class="text-xs text-rose-600/80 mt-0.5">Keluar dari perangkat ini untuk mengamankan akun keuanganmu</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition-colors">
                Keluar Akun
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
function previewPhoto(input) {
    const placeholder = document.getElementById('photo-preview-placeholder');
    const preview = document.getElementById('photo-preview');
    if (!preview) return;

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = 2 * 1024 * 1024; // 2 MB

        if (file.size > maxSize) {
            alert('Ukuran file melebihi 2 MB. Silakan pilih file yang lebih kecil.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (placeholder) placeholder.classList.add('hidden');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
