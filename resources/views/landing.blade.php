<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DuitPelajar — Kelola Uangmu, Raih Masa Depanmu</title>
    <meta name="description" content="Aplikasi pencatatan keuangan harian untuk pelajar dan mahasiswa Indonesia.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-padding-top: 72px; }
        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            overflow-x: hidden;
            margin: 0; padding: 0;
            color: #0f0a1e;
        }

        /* ── Design tokens ── */
        :root {
            --purple-50:  #faf5ff;
            --purple-100: #f3e8ff;
            --purple-200: #e9d5ff;
            --purple-400: #c084fc;
            --purple-500: #a855f7;
            --purple-600: #9333ea;
            --purple-700: #7e22ce;
            --purple-900: #3b0764;
            --violet-500: #8b5cf6;
            --violet-600: #7c3aed;
            --bg-hero:    #f3e8ff;
            --bg-dark:    #1a0533;
        }

        /* ── Navbar ── */
        #navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(233,213,255,0.6);
            transition: box-shadow .3s;
        }
        #navbar.scrolled { box-shadow: 0 2px 24px rgba(147,51,234,.12); }

        /* ── Mobile nav ── */
        #mobile-nav {
            max-height: 0; overflow: hidden;
            transition: max-height .35s ease, opacity .35s ease;
            opacity: 0;
        }
        #mobile-nav.open { max-height: 480px; opacity: 1; }

        /* ── Gradient text ── */
        .grad-text {
            background: linear-gradient(135deg, #9333ea 0%, #7c3aed 50%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .grad-text-2 {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Purple gradient bg ── */
        .hero-gradient {
            background: linear-gradient(145deg, #f3e8ff 0%, #ede9fe 40%, #faf5ff 100%);
        }

        /* ── Animations ── */
        @keyframes floatA { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
        @keyframes floatB { 0%,100%{transform:translateY(0)} 50%{transform:translateY(10px)} }
        @keyframes floatC { 0%,100%{transform:translateY(0) rotate(-1deg)} 50%{transform:translateY(-8px) rotate(1deg)} }
        @keyframes pulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.5)} }
        @keyframes growBar { from{transform:scaleY(0)} to{transform:scaleY(1)} }
        @keyframes spinSlow { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

        .fa { animation: floatA 5s ease-in-out infinite; }
        .fb { animation: floatB 6s ease-in-out infinite; }
        .fc { animation: floatC 4s ease-in-out infinite; }
        .dot-live { animation: pulseDot 2s ease-in-out infinite; }
        .bar { transform-origin: bottom; animation: growBar .7s ease forwards; }

        /* ── Scroll reveal ── */
        .rv { opacity: 0; transform: translateY(20px); transition: opacity .6s ease, transform .6s ease; }
        .rv.d1 { transition-delay: .10s; }
        .rv.d2 { transition-delay: .20s; }
        .rv.d3 { transition-delay: .30s; }
        .rv.d4 { transition-delay: .40s; }
        .rv.vis { opacity: 1; transform: none; }

        /* ── Cards ── */
        .card {
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid #f3e8ff;
            box-shadow: 0 4px 24px rgba(147,51,234,.07);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(147,51,234,.15);
        }

        /* ── Purple button ── */
        .btn-purple {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 13px 28px; border-radius: 12px;
            font-size: 14px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            box-shadow: 0 6px 20px rgba(147,51,234,.40);
            text-decoration: none;
            transition: transform .2s, box-shadow .2s;
        }
        .btn-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(147,51,234,.50);
        }
        .btn-outline-purple {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 26px; border-radius: 12px;
            font-size: 14px; font-weight: 600; color: #9333ea;
            background: rgba(243,232,255,.7);
            border: 1.5px solid #e9d5ff;
            text-decoration: none;
            transition: background .2s, border-color .2s;
        }
        .btn-outline-purple:hover {
            background: rgba(233,213,255,.9);
            border-color: #c084fc;
        }

        /* ── Stat badge ── */
        .stat-badge {
            background: #fff; border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 8px 32px rgba(147,51,234,.12);
            border: 1px solid #f3e8ff;
        }

        /* ── Section alternating ── */
        .section-light { background: #faf5ff; }
        .section-white { background: #ffffff; }

        /* ── Wave ── */
        .wave svg { display: block; }

        /* ── CTA dark ── */
        .cta-dark {
            background: linear-gradient(135deg, #1a0533 0%, #2d1054 50%, #0f172a 100%);
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #faf5ff; }
        ::-webkit-scrollbar-thumb { background: #c084fc; border-radius: 4px; }
    </style>
</head>
<body>

{{-- ═══ NAVBAR ═══ --}}
<nav id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between" style="height:68px">

            {{-- Logo --}}
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0">
                <div style="width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(147,51,234,.35)">
                    <svg style="width:20px;height:20px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <span class="grad-text" style="font-size:18px;font-weight:800;white-space:nowrap">DuitPelajar</span>
            </a>

            {{-- Desktop links --}}
            <div class="hidden md:flex items-center" style="gap:36px">
                <a href="#beranda" style="font-size:14px;font-weight:500;color:#374151;text-decoration:none" onmouseover="this.style.color='#9333ea'" onmouseout="this.style.color='#374151'">Beranda</a>
                <a href="#fitur"   style="font-size:14px;font-weight:500;color:#374151;text-decoration:none" onmouseover="this.style.color='#9333ea'" onmouseout="this.style.color='#374151'">Fitur</a>
                <a href="#tentang" style="font-size:14px;font-weight:500;color:#374151;text-decoration:none" onmouseover="this.style.color='#9333ea'" onmouseout="this.style.color='#374151'">Tentang</a>
            </div>

            {{-- Desktop CTA --}}
            <div class="hidden md:flex items-center" style="gap:10px;flex-shrink:0">
                <a href="{{ route('login') }}" style="padding:8px 18px;border-radius:10px;font-size:14px;font-weight:600;color:#7c3aed;border:1.5px solid #e9d5ff;text-decoration:none;background:transparent;transition:background .2s" onmouseover="this.style.background='#faf5ff'" onmouseout="this.style.background='transparent'">Masuk</a>
                <a href="{{ route('register') }}" class="btn-purple" style="padding:9px 20px;font-size:14px">Daftar Gratis</a>
            </div>

            {{-- Hamburger --}}
            <button id="hamburger" type="button" aria-label="Menu" style="display:none;padding:8px;border-radius:8px;border:none;background:transparent;cursor:pointer;color:#374151">
                <svg id="icon-open"  style="width:22px;height:22px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="icon-close" style="width:22px;height:22px;display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Mobile nav --}}
        <div id="mobile-nav" style="border-top:1px solid #f3e8ff">
            <div style="padding:12px 0;display:flex;flex-direction:column;gap:4px">
                <a href="#beranda" class="nav-link" style="display:block;padding:10px 12px;border-radius:10px;font-size:14px;font-weight:500;color:#374151;text-decoration:none">Beranda</a>
                <a href="#fitur"   class="nav-link" style="display:block;padding:10px 12px;border-radius:10px;font-size:14px;font-weight:500;color:#374151;text-decoration:none">Fitur</a>
                <a href="#tentang" class="nav-link" style="display:block;padding:10px 12px;border-radius:10px;font-size:14px;font-weight:500;color:#374151;text-decoration:none">Tentang</a>
                <div style="padding-top:8px;display:flex;flex-direction:column;gap:8px">
                    <a href="{{ route('login') }}" class="nav-link" style="display:block;text-align:center;padding:11px;border-radius:12px;font-size:14px;font-weight:600;color:#7c3aed;border:1.5px solid #e9d5ff;text-decoration:none">Masuk</a>
                    <a href="{{ route('register') }}" class="nav-link btn-purple" style="display:block;text-align:center;padding:11px">Daftar Gratis</a>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ═══ HERO ═══ --}}
<section id="beranda" class="hero-gradient" style="padding-top:68px;min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden">

    {{-- Decorative blobs --}}
    <div aria-hidden="true" style="position:absolute;top:-80px;right:-80px;width:500px;height:500px;border-radius:50%;background:linear-gradient(135deg,#e9d5ff,#f3e8ff);opacity:.6;filter:blur(60px);pointer-events:none"></div>
    <div aria-hidden="true" style="position:absolute;bottom:-60px;left:-60px;width:400px;height:400px;border-radius:50%;background:linear-gradient(135deg,#ede9fe,#ddd6fe);opacity:.5;filter:blur(50px);pointer-events:none"></div>
    <div aria-hidden="true" style="position:absolute;top:50%;left:50%;width:300px;height:300px;border-radius:50%;background:#f5f3ff;opacity:.4;filter:blur(80px);transform:translate(-50%,-50%);pointer-events:none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full" style="padding-top:48px;padding-bottom:80px">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            {{-- LEFT --}}
            <div>
                {{-- Badge --}}
                <div class="rv" style="display:inline-flex;align-items:center;gap:8px;padding:7px 16px;border-radius:999px;background:rgba(255,255,255,.8);border:1px solid #e9d5ff;font-size:12px;font-weight:600;color:#7c3aed;margin-bottom:24px;box-shadow:0 2px 12px rgba(147,51,234,.1)">
                    <span class="dot-live" style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#9333ea"></span>
                    Gratis untuk Pelajar &amp; Mahasiswa Indonesia
                </div>

                {{-- Headline --}}
                <h1 class="rv d1" style="font-size:clamp(2.4rem,6vw,4.2rem);font-weight:900;line-height:1.08;letter-spacing:-.03em;color:#0f0a1e;margin:0 0 24px">
                    Kelola Uangmu,<br>
                    <span class="grad-text">Raih Masa</span><br>
                    <span class="grad-text">Depanmu.</span>
                </h1>

                <p class="rv d2" style="font-size:16px;color:#6b7280;line-height:1.75;margin:0 0 36px;max-width:480px">
                    Catat pemasukan, kendalikan pengeluaran, dan capai target tabunganmu. Platform keuangan cerdas khusus pelajar.
                </p>

                {{-- CTA buttons --}}
                <div class="rv d3" style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:48px">
                    <a href="{{ route('register') }}" class="btn-purple" style="font-size:15px;padding:14px 32px">
                        <svg style="width:17px;height:17px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Mulai Sekarang
                    </a>
                    <a href="#fitur" class="btn-outline-purple" style="font-size:15px;padding:14px 28px">
                        Lihat Fitur →
                    </a>
                </div>
            </div>

            {{-- RIGHT: Dashboard mockup + floating badges --}}
            <div class="rv d2" style="display:flex;justify-content:center;position:relative;padding:40px 20px 60px">

                {{-- Floating badges --}}
                <div class="fc" style="position:absolute;top:20px;left:0;background:#fff;border-radius:14px;padding:10px 16px;box-shadow:0 8px 28px rgba(147,51,234,.18);border:1px solid #f3e8ff;font-size:12px;font-weight:700;color:#7c3aed;white-space:nowrap;z-index:2">
                    💸 Catat Gratis Selamanya
                </div>
                <div class="fa" style="position:absolute;bottom:30px;left:8px;background:linear-gradient(135deg,#9333ea,#7c3aed);border-radius:14px;padding:10px 16px;color:#fff;font-size:12px;font-weight:700;white-space:nowrap;box-shadow:0 8px 24px rgba(147,51,234,.4);z-index:2">
                    🎯 Target Tercapai!
                </div>
                <div class="fb" style="position:absolute;top:30px;right:0;background:#fff;border-radius:14px;padding:10px 16px;box-shadow:0 8px 28px rgba(147,51,234,.15);border:1px solid #f3e8ff;font-size:12px;font-weight:700;color:#9333ea;white-space:nowrap;z-index:2">
                    📊 Laporan Otomatis
                </div>

                {{-- Dashboard card --}}
                <div style="position:relative;width:100%;max-width:380px;z-index:1">
                    <div aria-hidden="true" style="position:absolute;inset:-20px;background:linear-gradient(135deg,#e9d5ff,#ddd6fe,#c4b5fd);border-radius:36px;filter:blur(32px);opacity:.4"></div>
                    <div style="position:relative;background:#fff;border-radius:28px;box-shadow:0 24px 64px rgba(147,51,234,.18);border:1px solid #f3e8ff;overflow:hidden">

                        {{-- Top bar --}}
                        <div style="display:flex;align-items:center;gap:6px;background:#faf5ff;border-bottom:1px solid #f3e8ff;padding:10px 16px;border-radius:28px 28px 0 0">
                            <span style="width:11px;height:11px;border-radius:50%;background:#f87171;flex-shrink:0"></span>
                            <span style="width:11px;height:11px;border-radius:50%;background:#fbbf24;flex-shrink:0"></span>
                            <span style="width:11px;height:11px;border-radius:50%;background:#34d399;flex-shrink:0"></span>
                            <span style="flex:1;margin:0 8px;background:#fff;border:1px solid #e9d5ff;border-radius:6px;font-size:10px;color:#9ca3af;padding:2px 8px">duitpelajar.app/dashboard</span>
                        </div>

                        <div style="padding:16px;display:flex;flex-direction:column;gap:12px">

                            {{-- Header --}}
                            <div style="display:flex;align-items:center;justify-content:space-between">
                                <div>
                                    <p style="font-size:11px;color:#9ca3af;margin:0">Selamat pagi,</p>
                                    <p style="font-size:13px;font-weight:700;color:#0f0a1e;margin:0">Budi Pratama 👋</p>
                                </div>
                                <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">B</div>
                            </div>

                            {{-- Saldo card --}}
                            <div style="border-radius:18px;padding:16px;background:linear-gradient(135deg,#9333ea,#7c3aed,#6d28d9)">
                                <p style="font-size:10px;color:rgba(255,255,255,.7);margin:0 0 2px">Total Saldo</p>
                                <p style="font-size:24px;font-weight:800;color:#fff;margin:0 0 12px;letter-spacing:-.02em">Rp 412.000</p>
                                <div style="display:flex;gap:8px">
                                    <div style="flex:1;background:rgba(255,255,255,.15);border-radius:10px;padding:8px 10px;backdrop-filter:blur(4px)">
                                        <p style="font-size:9px;color:rgba(255,255,255,.7);margin:0">Pemasukan</p>
                                        <p style="font-size:12px;font-weight:700;color:#fff;margin:2px 0 0">Rp 600rb</p>
                                    </div>
                                    <div style="flex:1;background:rgba(255,255,255,.15);border-radius:10px;padding:8px 10px;backdrop-filter:blur(4px)">
                                        <p style="font-size:9px;color:rgba(255,255,255,.7);margin:0">Pengeluaran</p>
                                        <p style="font-size:12px;font-weight:700;color:#fff;margin:2px 0 0">Rp 188rb</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bar chart --}}
                            <div style="background:#faf5ff;border-radius:14px;padding:12px">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                    <p style="font-size:10px;font-weight:600;color:#374151;margin:0">Pengeluaran 7 Hari</p>
                                    <span style="font-size:9px;color:#9333ea;background:#f3e8ff;padding:2px 7px;border-radius:999px;font-weight:500">Nov 2026</span>
                                </div>
                                <div style="display:flex;align-items:flex-end;gap:5px;height:44px">
                                    @foreach([35,55,40,70,45,60,50] as $i => $h)
                                        <div class="bar" style="flex:1;border-radius:4px 4px 0 0;height:{{ $h }}%;background:{{ $h>=70 ? 'linear-gradient(to top,#f43f5e,#fb7185)' : 'linear-gradient(to top,#9333ea,#a855f7)' }};animation-delay:{{ $i*0.07 }}s"></div>
                                    @endforeach
                                </div>
                                <div style="display:flex;margin-top:4px">
                                    @foreach(['S','M','S','R','K','J','S'] as $d)
                                        <span style="flex:1;text-align:center;font-size:8px;color:#9ca3af">{{ $d }}</span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Savings --}}
                            <div style="display:flex;align-items:center;gap:10px;background:#f5f3ff;border-radius:14px;padding:10px 12px">
                                <div style="width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg style="width:15px;height:15px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <p style="font-size:10px;font-weight:600;color:#6d28d9;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Sepatu Olahraga</p>
                                    <div style="width:100%;height:5px;background:#ddd6fe;border-radius:999px;margin:4px 0">
                                        <div style="width:34%;height:5px;background:linear-gradient(to right,#9333ea,#a855f7);border-radius:999px"></div>
                                    </div>
                                    <p style="font-size:9px;color:#a855f7;margin:0">Rp 120rb / Rp 350rb</p>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:#7c3aed;flex-shrink:0">34%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Wave bottom --}}
    <div style="position:absolute;bottom:0;left:0;right:0;line-height:0">
        <svg viewBox="0 0 1440 56" xmlns="http://www.w3.org/2000/svg" style="display:block;width:100%;height:56px" preserveAspectRatio="none">
            <path d="M0,28 C480,60 960,0 1440,28 L1440,56 L0,56 Z" fill="#ffffff"/>
        </svg>
    </div>
</section>



{{-- ═══ FITUR ═══ --}}
<section id="fitur" style="padding:96px 0;background:#faf5ff">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div style="text-align:center;margin-bottom:64px">
            <span class="rv" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:#f3e8ff;border:1px solid #e9d5ff;font-size:12px;font-weight:600;color:#9333ea;margin-bottom:16px">
                <svg style="width:13px;height:13px;fill:#9333ea;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                Fitur Unggulan
            </span>
            <h2 class="rv d1" style="font-size:clamp(2rem,4.5vw,3rem);font-weight:900;color:#0f0a1e;line-height:1.1;letter-spacing:-.025em;margin:0 0 16px">
                Semua yang Kamu Butuhkan<br>
                <span class="grad-text">dalam Satu Tempat</span>
            </h2>
            <p class="rv d2" style="font-size:16px;color:#6b7280;max-width:520px;margin:0 auto;line-height:1.7">
                Dirancang khusus untuk kebutuhan keuangan pelajar Indonesia.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $feats = [
                ['#f3e8ff','#9333ea','linear-gradient(135deg,#9333ea,#7c3aed)','Catat Transaksi','Rekam setiap pemasukan dan pengeluaran dengan cepat dan akurat.','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['#ede9fe','#7c3aed','linear-gradient(135deg,#7c3aed,#6d28d9)','Anggaran Bulanan','Atur limit pengeluaran per kategori, pantau sisa anggaran.','M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['#ddd6fe','#6d28d9','linear-gradient(135deg,#6d28d9,#5b21b6)','Target Tabungan','Buat target dan pantau progres menuju impianmu.','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['#f5f3ff','#a855f7','linear-gradient(135deg,#a855f7,#9333ea)','Laporan Keuangan','Visualisasi kondisi keuangan dengan grafik yang informatif.','M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ];
            @endphp
            @foreach($feats as $idx => [$ibg,$ic,$grad,$title,$desc,$path])
                <div class="card rv {{ ['','d1','d2','d3'][$idx] }}" style="padding:28px 24px">
                    <div style="width:52px;height:52px;border-radius:16px;background:{{ $ibg }};display:flex;align-items:center;justify-content:center;margin-bottom:18px;border:1.5px solid rgba(0,0,0,.04)">
                        <svg style="width:24px;height:24px;color:{{ $ic }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                        </svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#0f0a1e;margin:0 0 8px">{{ $title }}</h3>
                    <p style="font-size:13px;color:#6b7280;line-height:1.65;margin:0">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ PREVIEW ═══ --}}
<section style="padding:96px 0;background:#fff">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Mockup --}}
            <div class="rv" id="prev-left" style="display:flex;justify-content:center">
                <div style="position:relative;width:100%;max-width:400px">
                    <div aria-hidden="true" style="position:absolute;inset:-20px;background:linear-gradient(135deg,#e9d5ff,#ddd6fe,#c4b5fd);border-radius:36px;filter:blur(40px);opacity:.35"></div>
                    <div style="position:relative;background:#fff;border-radius:28px;box-shadow:0 24px 64px rgba(147,51,234,.2);border:1px solid #f3e8ff;overflow:hidden">
                        <div style="padding:20px;background:linear-gradient(135deg,#9333ea,#7c3aed,#6d28d9)">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                                <div>
                                    <p style="font-size:10px;color:rgba(255,255,255,.65);font-weight:500;margin:0">Dashboard Keuangan</p>
                                    <p style="font-size:14px;color:#fff;font-weight:700;margin:0">November 2026</p>
                                </div>
                                <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg style="width:16px;height:16px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                            </div>
                            <div style="background:rgba(255,255,255,.12);border-radius:14px;padding:12px;text-align:center">
                                <p style="font-size:10px;color:rgba(255,255,255,.65);margin:0">Total Saldo</p>
                                <p style="font-size:26px;font-weight:800;color:#fff;margin:2px 0 0;letter-spacing:-.02em">Rp 412.000</p>
                            </div>
                        </div>
                        <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                <div style="background:#fdf4ff;border-radius:14px;padding:12px">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                        <div style="width:20px;height:20px;border-radius:6px;background:#9333ea;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <svg style="width:10px;height:10px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        </div>
                                        <span style="font-size:9px;font-weight:600;color:#6d28d9">Pemasukan</span>
                                    </div>
                                    <p style="font-size:13px;font-weight:700;color:#7c3aed;margin:0">Rp 600.000</p>
                                </div>
                                <div style="background:#fff1f2;border-radius:14px;padding:12px">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                        <div style="width:20px;height:20px;border-radius:6px;background:#f43f5e;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <svg style="width:10px;height:10px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        </div>
                                        <span style="font-size:9px;font-weight:600;color:#9f1239">Pengeluaran</span>
                                    </div>
                                    <p style="font-size:13px;font-weight:700;color:#e11d48;margin:0">Rp 188.000</p>
                                </div>
                            </div>
                            <div>
                                <p style="font-size:11px;font-weight:600;color:#374151;margin:0 0 8px">Transaksi Terakhir</p>
                                @foreach([
                                    ['Uang Saku Bulanan','Pemasukan','+Rp 600.000','#f3e8ff','#9333ea'],
                                    ['Makan Siang','Makan','-Rp 22.000','#fff1f2','#f43f5e'],
                                    ['Ongkos Angkot','Transport','-Rp 10.000','#fffbeb','#d97706'],
                                ] as [$t,$c,$a,$bg,$ic])
                                    <div style="display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid #faf5ff">
                                        <div style="width:26px;height:26px;border-radius:8px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <svg style="width:12px;height:12px;color:{{ $ic }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                        <div style="flex:1;min-width:0">
                                            <p style="font-size:10px;font-weight:600;color:#0f0a1e;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $t }}</p>
                                            <p style="font-size:9px;color:#9ca3af;margin:0">{{ $c }}</p>
                                        </div>
                                        <span style="font-size:10px;font-weight:700;color:{{ str_starts_with($a,'+') ? '#9333ea' : '#e11d48' }};flex-shrink:0">{{ $a }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Copy --}}
            <div class="rv" id="prev-right" style="text-align:center">
                <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:#f3e8ff;border:1px solid #e9d5ff;font-size:12px;font-weight:600;color:#9333ea;margin-bottom:16px">
                    👁 Preview Aplikasi
                </span>
                <h2 style="font-size:clamp(1.8rem,4vw,2.6rem);font-weight:900;color:#0f0a1e;line-height:1.15;letter-spacing:-.025em;margin:0 0 18px">
                    Pantau Keuanganmu<br>
                    <span class="grad-text">dengan Lebih Mudah</span>
                </h2>
                <p style="font-size:15px;color:#6b7280;line-height:1.7;margin:0 0 28px;max-width:420px;margin-left:auto;margin-right:auto">
                    Dashboard intuitif yang menampilkan semua informasi keuanganmu dalam satu layar.
                </p>
                <ul style="list-style:none;padding:0;margin:0 auto 36px;max-width:360px;display:flex;flex-direction:column;gap:12px;text-align:left">
                    @foreach(['Ringkasan saldo real-time','Grafik pengeluaran visual','Riwayat transaksi lengkap'] as $txt)
                        <li style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:500;color:#374151">
                            <div style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg style="width:11px;height:11px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            {{ $txt }}
                        </li>
                    @endforeach
                </ul>
                <div style="display:flex;justify-content:center">
                    <a href="{{ route('register') }}" class="btn-purple">Coba Sekarang →</a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══ MANFAAT ═══ --}}
<section id="tentang" style="padding:96px 0;background:#faf5ff">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div style="text-align:center;margin-bottom:64px">
            <span class="rv" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:#f3e8ff;border:1px solid #e9d5ff;font-size:12px;font-weight:600;color:#9333ea;margin-bottom:16px">❤️ Mengapa DuitPelajar?</span>
            <h2 class="rv d1" style="font-size:clamp(2rem,4.5vw,3rem);font-weight:900;color:#0f0a1e;line-height:1.1;letter-spacing:-.025em;margin:0 0 14px">
                Bangun Kebiasaan Finansial<br>
                <span class="grad-text">Sejak Dini</span>
            </h2>
            <p class="rv d2" style="font-size:16px;color:#6b7280;max-width:480px;margin:0 auto;line-height:1.7">Investasi terbaik dimulai dari kebiasaan mencatat sejak sekarang.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
            $benefits = [
                ['🎯','#f3e8ff','Lebih Disiplin','Sadar ke mana uang pergi setiap harinya sehingga pengeluaran lebih terkontrol.',['Sadar pola pengeluaran','Hindari pemborosan','Kontrol anggaran'],'linear-gradient(135deg,#9333ea,#7c3aed)',''],
                ['💡','#ede9fe','Lebih Terencana','Laporan visual membantu memahami kondisi keuangan dan menemukan peluang hemat.',['Analisis per kategori','Grafik visual jelas','Temukan pola boros'],'linear-gradient(135deg,#7c3aed,#6d28d9)','d1'],
                ['🌱','#ddd6fe','Lebih Mandiri','Kebiasaan mengelola keuangan sejak pelajar adalah fondasi kebebasan finansial.',['Fondasi kebiasaan baik','Siap masa depan','Mandiri finansial'],'linear-gradient(135deg,#6d28d9,#5b21b6)','d2'],
            ];
            @endphp
            @foreach($benefits as [$emoji,$ibg,$title,$desc,$pts,$grad,$delay])
                <div class="card rv {{ $delay }}" style="padding:28px 24px;display:flex;flex-direction:column">
                    <div style="width:60px;height:60px;border-radius:18px;background:{{ $ibg }};display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:26px">{{ $emoji }}</div>
                    <h3 style="font-size:16px;font-weight:700;color:#0f0a1e;text-align:center;margin:0 0 10px">{{ $title }}</h3>
                    <p style="font-size:13px;color:#6b7280;line-height:1.65;text-align:center;margin:0 0 18px;flex:1">{{ $desc }}</p>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px">
                        @foreach($pts as $p)
                            <li style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151">
                                <div style="width:20px;height:20px;border-radius:50%;background:{{ $grad }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg style="width:10px;height:10px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $p }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ CTA ═══ --}}
<section style="padding:96px 16px;background:#fff">
    <div style="max-width:900px;margin:0 auto">
        <div class="rv" style="border-radius:32px;padding:72px 40px;text-align:center;position:relative;overflow:hidden;background:linear-gradient(145deg,#0f0a1e 0%,#1e0a3c 50%,#0a0118 100%)">
            <div aria-hidden="true" style="position:absolute;top:-80px;right:-80px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(147,51,234,.4),transparent 70%);pointer-events:none"></div>
            <div aria-hidden="true" style="position:absolute;bottom:-60px;left:-60px;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.3),transparent 70%);pointer-events:none"></div>
            <div style="position:relative">
                <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(147,51,234,.2);font-size:12px;font-weight:600;color:#c084fc;margin-bottom:20px;border:1px solid rgba(147,51,234,.3)">
                    🎉 Gratis Selamanya untuk Pelajar
                </span>
                <h2 style="font-size:clamp(1.8rem,4.5vw,3rem);font-weight:900;color:#fff;line-height:1.1;letter-spacing:-.025em;margin:0 0 16px">
                    Siap Kendalikan<br>
                    <span style="background:linear-gradient(135deg,#c084fc,#a855f7,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Keuanganmu?</span>
                </h2>
                <p style="font-size:15px;color:#9ca3af;line-height:1.7;max-width:440px;margin:0 auto 36px">
                    Bergabung dengan pelajar Indonesia yang sudah lebih cerdas mengelola uang saku.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center">
                    <a href="{{ route('register') }}" class="btn-purple" style="font-size:15px;padding:14px 32px">
                        Daftar Gratis Sekarang
                        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 28px;border-radius:12px;font-size:15px;font-weight:600;color:#c084fc;border:1.5px solid rgba(192,132,252,.3);text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(147,51,234,.12)'" onmouseout="this.style.background='transparent'">
                        Sudah Punya Akun
                    </a>
                </div>
                <p style="margin-top:20px;font-size:12px;color:#6b7280">Tidak perlu kartu kredit. Tidak ada biaya tersembunyi.</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══ FOOTER ═══ --}}
<footer style="background:#0f0a1e;padding:48px 16px">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8" style="margin-bottom:36px">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:18px;height:18px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <span style="font-size:17px;font-weight:800;color:#c084fc">DuitPelajar</span>
                </div>
                <p style="font-size:13px;color:#4b5563;line-height:1.7;max-width:240px;margin:0">Keuangan cerdas untuk pelajar dan mahasiswa Indonesia.</p>
            </div>
            <div>
                <h4 style="font-size:14px;font-weight:600;color:#e9d5ff;margin:0 0 14px">Navigasi</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px">
                    @foreach([['#beranda','Beranda'],['#fitur','Fitur'],['#tentang','Tentang']] as [$href,$lab])
                        <li><a href="{{ $href }}" style="font-size:13px;color:#4b5563;text-decoration:none" onmouseover="this.style.color='#c084fc'" onmouseout="this.style.color='#4b5563'">{{ $lab }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 style="font-size:14px;font-weight:600;color:#e9d5ff;margin:0 0 14px">Akun</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px">
                    <li><a href="{{ route('login') }}"    style="font-size:13px;color:#4b5563;text-decoration:none" onmouseover="this.style.color='#c084fc'" onmouseout="this.style.color='#4b5563'">Masuk</a></li>
                    <li><a href="{{ route('register') }}" style="font-size:13px;color:#4b5563;text-decoration:none" onmouseover="this.style.color='#c084fc'" onmouseout="this.style.color='#4b5563'">Daftar Gratis</a></li>
                </ul>
            </div>
        </div>
        <div style="border-top:1px solid #1f1438;padding-top:24px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px">
            <p style="font-size:12px;color:#374151;margin:0">&copy; {{ date('Y') }} DuitPelajar. All Rights Reserved.</p>
            <p style="font-size:12px;color:#1f1438;margin:0">Dibuat dengan ❤️ untuk pelajar Indonesia</p>
        </div>
    </div>
</footer>

<script>
(function () {
    'use strict';

    var hamburger = document.getElementById('hamburger');
    function checkMobile() { hamburger.style.display = window.innerWidth < 768 ? 'block' : 'none'; }
    checkMobile();
    window.addEventListener('resize', checkMobile, { passive: true });

    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function () { navbar.classList.toggle('scrolled', window.scrollY > 16); }, { passive: true });

    var mobileNav = document.getElementById('mobile-nav');
    var iconOpen   = document.getElementById('icon-open');
    var iconClose  = document.getElementById('icon-close');
    hamburger.addEventListener('click', function () {
        var open = mobileNav.classList.toggle('open');
        iconOpen.style.display  = open ? 'none'  : 'block';
        iconClose.style.display = open ? 'block' : 'none';
    });
    document.querySelectorAll('.nav-link').forEach(function (l) {
        l.addEventListener('click', function () {
            mobileNav.classList.remove('open');
            iconOpen.style.display = 'block';
            iconClose.style.display = 'none';
        });
    });

    function fixOrder() {
        var l = document.getElementById('prev-left');
        var r = document.getElementById('prev-right');
        if (!l || !r) return;
        if (window.innerWidth >= 1024) { l.style.order = '1'; r.style.order = '2'; }
        else { l.style.order = '2'; r.style.order = '1'; }
    }
    fixOrder();
    window.addEventListener('resize', fixOrder, { passive: true });

    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('vis'); obs.unobserve(e.target); } });
    }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.rv').forEach(function (el) { obs.observe(el); });

    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var el = document.getElementById(this.getAttribute('href').slice(1));
            if (!el) return;
            e.preventDefault();
            window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 76, behavior: 'smooth' });
        });
    });
}());
</script>

</body>
</html>
