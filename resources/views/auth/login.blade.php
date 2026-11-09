<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — DuitPelajar</title>
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

        /* ── Left panel ── */
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

        /* ── Right panel ── */
        .auth-right {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            min-height: 100vh;
        }
        @media (min-width: 1024px) { .auth-right { max-width: 520px; } }

        /* ── Input ── */
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

        .auth-input-icon {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border-radius: 12px;
            border: 1.5px solid #e9d5ff;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f0a1e;
            background: #faf5ff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .auth-input-icon:focus {
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147,51,234,.12);
            background: #fff;
        }
        .auth-input-icon::placeholder { color: #9ca3af; }

        /* ── Button ── */
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
        .btn-auth:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(147,51,234,.5);
        }
        .btn-auth:active { transform: scale(.98); }

        /* ── Blob ── */
        @keyframes floatBlob { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.04)} }
        .blob { animation: floatBlob 8s ease-in-out infinite; }
    </style>
</head>
<body>

    {{-- ── LEFT: Brand panel ── --}}
    <div class="auth-left">

        {{-- Background decorations --}}
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
                Selamat Datang<br>
                <span style="background:linear-gradient(135deg,#c084fc,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Kembali!</span>
            </h2>
            <p style="font-size:15px;color:#9ca3af;line-height:1.7;max-width:340px;margin:0 0 40px">
                Lanjutkan perjalanan finansialmu. Pantau keuangan, kelola anggaran, dan capai targetmu bersama DuitPelajar.
            </p>

            {{-- Stats --}}
            <div style="display:flex;gap:24px">
                @foreach([['500+','Pengguna'],['22','Kategori'],['4.9★','Rating']] as [$n,$l])
                    <div style="background:rgba(255,255,255,.07);border-radius:14px;padding:14px 18px;border:1px solid rgba(255,255,255,.1);backdrop-filter:blur(8px)">
                        <p style="font-size:20px;font-weight:800;color:#c084fc;margin:0">{{ $n }}</p>
                        <p style="font-size:12px;color:#9ca3af;margin:2px 0 0">{{ $l }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom note --}}
        <div style="position:relative;z-index:1">
            <p style="font-size:12px;color:#4b5563;margin:0">&copy; {{ date('Y') }} DuitPelajar. Gratis untuk pelajar.</p>
        </div>
    </div>

    {{-- ── RIGHT: Form ── --}}
    <div class="auth-right">
        <div style="width:100%;max-width:400px">

            {{-- Header --}}
            <div style="margin-bottom:32px">
                {{-- Mobile logo --}}
                <a href="{{ route('home') }}" class="lg:hidden" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:28px">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center">
                        <svg style="width:18px;height:18px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <span style="font-size:18px;font-weight:800;background:linear-gradient(135deg,#9333ea,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">DuitPelajar</span>
                </a>

                <h1 style="font-size:28px;font-weight:900;color:#0f0a1e;letter-spacing:-.025em;margin:0 0 6px">Masuk ke Akun</h1>
                <p style="font-size:14px;color:#6b7280;margin:0">Masukkan email dan kata sandi yang terdaftar</p>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div style="margin-bottom:20px;padding:12px 16px;border-radius:12px;background:#f0fdf4;border:1px solid #bbf7d0;display:flex;align-items:center;gap:10px">
                    <svg style="width:16px;height:16px;color:#16a34a;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p style="font-size:13px;color:#15803d;margin:0">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('info'))
                <div style="margin-bottom:20px;padding:12px 16px;border-radius:12px;background:#eff6ff;border:1px solid #bfdbfe;display:flex;align-items:center;gap:10px">
                    <svg style="width:16px;height:16px;color:#2563eb;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p style="font-size:13px;color:#1d4ed8;margin:0">{{ session('info') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom:20px;padding:12px 16px;border-radius:12px;background:#fef2f2;border:1px solid #fecaca;display:flex;align-items:center;gap:10px">
                    <svg style="width:16px;height:16px;color:#dc2626;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p style="font-size:13px;color:#dc2626;margin:0">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div style="margin-bottom:20px;padding:12px 16px;border-radius:12px;background:#fdf4ff;border:1px solid #e9d5ff">
                    <p style="font-size:13px;font-weight:600;color:#7c3aed;margin:0 0 4px">Gagal masuk:</p>
                    <p style="font-size:13px;color:#9333ea;margin:0">{{ $errors->first() }}</p>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                {{-- Email --}}
                <div style="margin-bottom:18px">
                    <label for="email" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">
                        Alamat Email
                    </label>
                    <div style="position:relative">
                        <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;color:#c084fc">
                            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="auth-input-icon" placeholder="nama@email.com">
                    </div>
                </div>

                {{-- Password --}}
                <div style="margin-bottom:18px">
                    <label for="password" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px">
                        Kata Sandi
                    </label>
                    <div style="position:relative">
                        <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;color:#c084fc">
                            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="auth-input-icon" placeholder="••••••••">
                    </div>
                </div>

                {{-- Remember --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#6b7280">
                        <input type="checkbox" name="remember"
                               style="width:16px;height:16px;border-radius:5px;accent-color:#9333ea;cursor:pointer">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="btn-auth">Masuk ke DuitPelajar</button>
            </form>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:12px;margin:24px 0">
                <div style="flex:1;height:1px;background:#f3e8ff"></div>
                <span style="font-size:12px;color:#9ca3af">atau</span>
                <div style="flex:1;height:1px;background:#f3e8ff"></div>
            </div>

            {{-- Register link --}}
            <p style="text-align:center;font-size:14px;color:#6b7280;margin:0">
                Belum punya akun?
                <a href="{{ route('register') }}" style="font-weight:700;color:#9333ea;text-decoration:none" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#9333ea'">
                    Daftar Sekarang →
                </a>
            </p>

        </div>
    </div>

</body>
</html>
