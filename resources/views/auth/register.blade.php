<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — DuitPelajar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0; padding: 0;
            min-height: 100vh;
            display: flex;
            background: #0f0a1e;
            color: #0f0a1e;
        }

        .auth-left {
            display: none;
            flex: 1;
            background: linear-gradient(145deg, #1a0533 0%, #2d1054 50%, #0f0a1e 100%);
            position: relative;
            overflow: hidden;
            padding: 48px;
            flex-direction: column;
            justify-content: space-between;
        }
        @media (min-width: 1024px) { .auth-left { display: flex; } }

        .auth-right {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            min-height: 100vh;
        }
        @media (min-width: 1024px) { .auth-right { max-width: 560px; } }

        .auth-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #e9d5ff;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f0a1e;
            background: #faf5ff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .auth-input:focus {
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147,51,234,.12);
            background: #fff;
        }
        .auth-input::placeholder { color: #9ca3af; }

        .auth-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #e9d5ff;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f0a1e;
            background: #faf5ff;
            outline: none;
            appearance: none;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s;
        }
        .auth-select:focus {
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147,51,234,.12);
            background: #fff;
        }

        .btn-auth {
            width: 100%;
            padding: 13px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            box-shadow: 0 6px 20px rgba(147,51,234,.4);
            transition: transform .2s, box-shadow .2s;
        }
        .btn-auth:hover { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(147,51,234,.5); }
        .btn-auth:active { transform: scale(.98); }

        @keyframes floatBlob { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.04)} }
        .blob { animation: floatBlob 8s ease-in-out infinite; }
    </style>
</head>
<body>

    {{-- ── LEFT: Brand panel ── --}}
    <div class="auth-left">

        <div aria-hidden="true" class="blob" style="position:absolute;top:-100px;right:-100px;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(147,51,234,.35),transparent 65%);pointer-events:none"></div>
        <div aria-hidden="true" style="position:absolute;bottom:-80px;left:-80px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.25),transparent 65%);pointer-events:none"></div>

        {{-- Logo --}}
        <div style="position:relative;z-index:1">
            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:12px;text-decoration:none">
                <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(147,51,234,.5)">
                    <svg style="width:22px;height:22px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <span style="font-size:20px;font-weight:800;color:#fff">DuitPelajar</span>
            </a>
        </div>

        {{-- Center copy --}}
        <div style="position:relative;z-index:1">
            <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);font-weight:900;color:#fff;line-height:1.1;letter-spacing:-.03em;margin:0 0 20px">
                Mulai Perjalanan<br>
                <span style="background:linear-gradient(135deg,#c084fc,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Finansialmu!</span>
            </h2>
            <p style="font-size:15px;color:#9ca3af;line-height:1.7;max-width:340px;margin:0 0 40px">
                Ribuan pelajar sudah lebih cerdas mengelola uang saku mereka. Daftarkan dirimu sekarang, gratis!
            </p>

            {{-- Feature checklist --}}
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach([
                    'Catat transaksi kapan saja & di mana saja',
                    'Atur anggaran dan target tabungan',
                    'Laporan keuangan visual & mudah dipahami',
                    '100% gratis, tidak ada biaya tersembunyi',
                ] as $feat)
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg style="width:11px;height:11px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span style="font-size:14px;color:#d1d5db">{{ $feat }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="position:relative;z-index:1">
            <p style="font-size:12px;color:#4b5563;margin:0">&copy; {{ date('Y') }} DuitPelajar. Gratis untuk pelajar.</p>
        </div>
    </div>

    {{-- ── RIGHT: Form ── --}}
    <div class="auth-right">
        <div style="width:100%;max-width:440px">

            {{-- Header --}}
            <div style="margin-bottom:28px">
                {{-- Mobile logo --}}
                <a href="{{ route('home') }}" class="lg:hidden" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:28px">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center">
                        <svg style="width:18px;height:18px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <span style="font-size:18px;font-weight:800;background:linear-gradient(135deg,#9333ea,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">DuitPelajar</span>
                </a>

                <h1 style="font-size:28px;font-weight:900;color:#0f0a1e;letter-spacing:-.025em;margin:0 0 6px">Buat Akun Gratis</h1>
                <p style="font-size:14px;color:#6b7280;margin:0">Isi data di bawah untuk mulai mengelola keuanganmu</p>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div style="margin-bottom:20px;padding:12px 16px;border-radius:12px;background:#fdf4ff;border:1px solid #e9d5ff">
                    <p style="font-size:13px;font-weight:600;color:#7c3aed;margin:0 0 6px">Gagal mendaftar:</p>
                    <ul style="margin:0;padding:0 0 0 16px">
                        @foreach($errors->all() as $err)
                            <li style="font-size:13px;color:#9333ea;line-height:1.6">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                {{-- Name --}}
                <div style="margin-bottom:16px">
                    <label for="name" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="auth-input" placeholder="Contoh: Budi Santoso">
                </div>

                {{-- Email --}}
                <div style="margin-bottom:16px">
                    <label for="email" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="auth-input" placeholder="budi@contoh.com">
                </div>

                {{-- School level --}}
                <div style="margin-bottom:16px">
                    <label for="school_level" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">Jenjang Pendidikan</label>
                    <div style="position:relative">
                        <select id="school_level" name="school_level" class="auth-select">
                            <option value="SMP"     {{ old('school_level') == 'SMP'     ? 'selected' : '' }}>SMP / MTs</option>
                            <option value="SMA/SMK" {{ old('school_level','SMA/SMK') == 'SMA/SMK' ? 'selected' : '' }}>SMA / SMK / MA</option>
                            <option value="Mahasiswa" {{ old('school_level') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        </select>
                        <div style="position:absolute;right:14px;top:50%;transform:translateY(-50%);pointer-events:none;color:#c084fc">
                            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div style="margin-bottom:16px">
                    <label for="password" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">
                        Kata Sandi
                        <span style="font-weight:400;color:#9ca3af">(min. 8 karakter)</span>
                    </label>
                    <input type="password" id="password" name="password" required
                           class="auth-input" placeholder="••••••••">
                </div>

                {{-- Confirm password --}}
                <div style="margin-bottom:24px">
                    <label for="password_confirmation" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="auth-input" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-auth">
                    Buat Akun Saya — Gratis
                </button>

                {{-- Terms note --}}
                <p style="font-size:12px;color:#9ca3af;text-align:center;margin:12px 0 0;line-height:1.6">
                    Dengan mendaftar, kamu menyetujui penggunaan data sesuai kebijakan privasi DuitPelajar.
                </p>
            </form>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:12px;margin:20px 0">
                <div style="flex:1;height:1px;background:#f3e8ff"></div>
                <span style="font-size:12px;color:#9ca3af">atau</span>
                <div style="flex:1;height:1px;background:#f3e8ff"></div>
            </div>

            {{-- Login link --}}
            <p style="text-align:center;font-size:14px;color:#6b7280;margin:0">
                Sudah punya akun?
                <a href="{{ route('login') }}" style="font-weight:700;color:#9333ea;text-decoration:none" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#9333ea'">
                    Masuk di sini →
                </a>
            </p>

        </div>
    </div>

</body>
</html>
