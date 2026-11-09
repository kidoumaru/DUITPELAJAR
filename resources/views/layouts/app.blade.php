<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — DuitPelajar</title>
    @php use Illuminate\Support\Facades\Storage; @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f3ff;
            color: #0f0a1e;
            margin: 0; padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Design tokens ── */
        :root {
            --purple-50:  #faf5ff;
            --purple-100: #f3e8ff;
            --purple-200: #e9d5ff;
            --purple-400: #c084fc;
            --purple-500: #a855f7;
            --purple-600: #9333ea;
            --purple-700: #7c3aed;
            --purple-900: #3b0764;
        }

        /* ── Top Navbar ── */
        #app-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 9000;
            background: rgba(15,10,30,.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(147,51,234,.25);
            height: 64px;
            display: flex;
            align-items: center;
        }

        /* ── Mobile drawer ── */
        #mobile-drawer-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 9500;
            background: rgba(15,10,30,.6);
            backdrop-filter: blur(4px);
        }
        #mobile-drawer-overlay.open { display: block; }
        #mobile-drawer {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 280px; z-index: 9600;
            background: #0f0a1e;
            border-right: 1px solid rgba(147,51,234,.2);
            transform: translateX(-100%);
            transition: transform .3s ease;
            display: flex; flex-direction: column;
            overflow-y: auto;
        }
        #mobile-drawer.open { transform: translateX(0); }

        /* ── Nav link ── */
        .nav-link {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 14px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            white-space: nowrap;
            transition: color .15s, background .15s;
        }
        .nav-link:hover { color: #fff; background: rgba(147,51,234,.2); }
        .nav-link.active { color: #fff; background: linear-gradient(135deg,rgba(147,51,234,.5),rgba(124,58,237,.4)); }
        .nav-link svg { flex-shrink: 0; opacity: .7; }
        .nav-link.active svg { opacity: 1; }

        /* ── Drawer nav link ── */
        .drawer-link {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 500;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            transition: color .15s, background .15s;
        }
        .drawer-link:hover { color: #fff; background: rgba(147,51,234,.2); }
        .drawer-link.active { color: #fff; background: linear-gradient(135deg,#9333ea,#7c3aed); }
        .drawer-link svg { flex-shrink: 0; opacity: .7; }
        .drawer-link.active svg { opacity: 1; }

        /* ── Main content ── */
        #app-content {
            flex: 1;
            margin-top: 64px;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 64px);
        }

        /* ── Flash alerts ── */
        .flash-alert { animation: slideDown .3s ease; }
        @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:none; } }

        /* ── Cards & buttons shared ── */
        .app-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f3e8ff;
            box-shadow: 0 2px 12px rgba(147,51,234,.06);
        }
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            padding: 10px 20px; border-radius: 10px;
            font-size: 13px; font-weight: 600; color: #fff;
            background: linear-gradient(135deg,#9333ea,#7c3aed);
            box-shadow: 0 4px 14px rgba(147,51,234,.35);
            border: none; cursor: pointer; text-decoration: none;
            transition: transform .2s, box-shadow .2s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(147,51,234,.45); }
        .btn-primary:active { transform: scale(.97); }

        /* ── Mobile bottom nav ── */
        #bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            z-index: 8000;
            background: rgba(15,10,30,.97);
            border-top: 1px solid rgba(147,51,234,.2);
            display: none;
            padding: 6px 8px 8px;
        }
        @media (max-width: 767px) { #bottom-nav { display: flex; } }

        .bottom-nav-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; gap: 3px;
            padding: 4px 2px; border-radius: 10px;
            font-size: 10px; font-weight: 500;
            color: rgba(255,255,255,.45);
            text-decoration: none;
            transition: color .15s, background .15s;
        }
        .bottom-nav-item:hover, .bottom-nav-item.active {
            color: #c084fc;
        }
        .bottom-nav-item svg { width: 20px; height: 20px; }

        /* ── FAB ── */
        .fab {
            width: 44px; height: 44px; border-radius: 14px;
            background: linear-gradient(135deg,#9333ea,#7c3aed);
            display: flex; align-items: center; justify-content: center;
            color: #fff; box-shadow: 0 4px 16px rgba(147,51,234,.5);
            text-decoration: none; margin-top: -10px;
        }

        /* ── Page heading ── */
        .page-heading {
            padding: 24px 24px 0;
        }
        @media (min-width: 640px) { .page-heading { padding: 28px 32px 0; } }

        /* ── Content padding ── */
        .content-pad { padding: 20px 16px 80px; }
        @media (min-width: 640px) { .content-pad { padding: 24px 32px 40px; } }

        /* ── Responsive desktop nav: hide overflow ── */
        #desktop-nav-scroll {
            display: none;
            overflow-x: auto;
            scrollbar-width: none;
        }
        #desktop-nav-scroll::-webkit-scrollbar { display: none; }
        @media (min-width: 768px) { #desktop-nav-scroll { display: flex; } }

        /* ── Single tab blocker ── */
        html.single-tab-blocked body > *:not(#single-tab-blocker) { visibility: hidden !important; }
        #single-tab-blocker {
            position: fixed; inset: 0; z-index: 999999;
            display: flex; align-items: center; justify-content: center;
            padding: 24px; background: #0f0a1e; visibility: visible !important;
        }
    </style>
</head>
<body>

{{-- ═══════════════════ TOP NAVBAR ═══════════════════ --}}
<nav id="app-navbar">
    <div style="max-width:1400px;margin:0 auto;padding:0 16px;width:100%;display:flex;align-items:center;gap:16px">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:9px;text-decoration:none;flex-shrink:0;margin-right:4px">
            <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(147,51,234,.4)">
                <svg style="width:18px;height:18px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <span style="font-size:16px;font-weight:800;background:linear-gradient(135deg,#c084fc,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;white-space:nowrap">DuitPelajar</span>
        </a>

        {{-- Desktop nav links (scrollable) --}}
        <div id="desktop-nav-scroll" style="align-items:center;gap:2px;flex:1">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Transaksi
            </a>
            <a href="{{ route('budgets.index') }}" class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Anggaran
            </a>
            <a href="{{ route('savings-goals.index') }}" class="nav-link {{ request()->routeIs('savings-goals.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Tabungan
            </a>
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori
            </a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
            <a href="{{ route('announcements.index') }}" class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Pengumuman
            </a>
            <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/></svg>
                Artikel
            </a>
            <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Bantuan
            </a>
        </div>

        {{-- Right actions --}}
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:auto">

            {{-- + Transaksi (desktop) --}}
            <a href="{{ route('transactions.create') }}" class="hidden md:inline-flex btn-primary" style="padding:8px 16px;font-size:12px">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                + Transaksi
            </a>

            {{-- Notification bell --}}
            <a href="{{ route('notifications.index') }}" style="position:relative;width:36px;height:36px;border-radius:10px;background:rgba(147,51,234,.15);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.7);text-decoration:none;transition:background .15s;flex-shrink:0" onmouseover="this.style.background='rgba(147,51,234,.3)'" onmouseout="this.style.background='rgba(147,51,234,.15)'">
                <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @php $navUnread = Auth::check() ? Auth::user()->notifications()->whereNull('read_at')->count() : 0; @endphp
                @if($navUnread > 0)
                    <span style="position:absolute;top:-2px;right:-2px;width:16px;height:16px;background:#f43f5e;border-radius:50%;font-size:9px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center">{{ $navUnread > 9 ? '9+' : $navUnread }}</span>
                @endif
            </a>

            {{-- Avatar / Profile dropdown trigger --}}
            <div style="position:relative">
                <button id="user-menu-btn" type="button" style="display:flex;align-items:center;gap:8px;padding:4px 10px 4px 4px;border-radius:999px;background:rgba(147,51,234,.15);border:none;cursor:pointer;transition:background .15s;flex-shrink:0" onmouseover="this.style.background='rgba(147,51,234,.25)'" onmouseout="this.style.background='rgba(147,51,234,.15)'">
                    @if(Auth::user()->photo)
                        <img src="{{ Storage::url(Auth::user()->photo) }}" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0">
                    @else
                        <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}</div>
                    @endif
                    <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,.8);max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="hidden md:block">{{ Auth::user()->name }}</span>
                    <svg style="width:14px;height:14px;color:rgba(255,255,255,.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="hidden md:block"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Dropdown --}}
                <div id="user-menu-dropdown" style="display:none;position:absolute;top:calc(100% + 8px);right:0;min-width:200px;background:#1a0533;border:1px solid rgba(147,51,234,.25);border-radius:14px;box-shadow:0 16px 48px rgba(0,0,0,.4);overflow:hidden;z-index:1000">
                    <div style="padding:12px 16px;border-bottom:1px solid rgba(147,51,234,.15)">
                        <p style="font-size:13px;font-weight:600;color:#fff;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->name }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->email }}</p>
                    </div>
                    <div style="padding:6px">
                        <a href="{{ route('profile.show') }}" style="display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:10px;font-size:13px;color:rgba(255,255,255,.75);text-decoration:none;transition:background .15s" onmouseover="this.style.background='rgba(147,51,234,.2)'" onmouseout="this.style.background='transparent'">
                            <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <div style="height:1px;background:rgba(147,51,234,.1);margin:4px 0"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="width:100%;display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:10px;font-size:13px;color:#f87171;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s" onmouseover="this.style.background='rgba(248,113,113,.1)'" onmouseout="this.style.background='transparent'">
                                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Hamburger (mobile only) --}}
            <button id="hamburger-btn" type="button" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:rgba(147,51,234,.15);border:none;cursor:pointer;color:rgba(255,255,255,.7)" class="md:hidden">
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</nav>

{{-- ═══════════════════ MOBILE DRAWER ═══════════════════ --}}
<div id="mobile-drawer-overlay" onclick="closeMobileDrawer()"></div>
<div id="mobile-drawer">
    {{-- Header --}}
    <div style="padding:20px 20px 16px;border-bottom:1px solid rgba(147,51,234,.15);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:16px;height:16px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <span style="font-size:15px;font-weight:800;background:linear-gradient(135deg,#c084fc,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">DuitPelajar</span>
        </div>
        <button onclick="closeMobileDrawer()" style="width:28px;height:28px;border-radius:8px;background:rgba(147,51,234,.15);border:none;cursor:pointer;color:rgba(255,255,255,.6);display:flex;align-items:center;justify-content:center">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- User info --}}
    <div style="padding:14px 20px;border-bottom:1px solid rgba(147,51,234,.1);flex-shrink:0">
        <div style="display:flex;align-items:center;gap:10px;background:rgba(147,51,234,.12);border-radius:12px;padding:10px 12px">
            @if(Auth::user()->photo)
                <img src="{{ Storage::url(Auth::user()->photo) }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0">
            @else
                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}</div>
            @endif
            <div style="min-width:0">
                <p style="font-size:13px;font-weight:600;color:#fff;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->name }}</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">{{ Auth::user()->school_level ?? 'Pelajar' }}</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav style="padding:12px 14px;flex:1;overflow-y:auto">
        @php
        $drawerLinks = [
            ['dashboard',        route('dashboard'),           'Dashboard',   'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
            ['transactions.*',   route('transactions.index'),  'Transaksi',   'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['budgets.*',        route('budgets.index'),       'Anggaran Bulanan','M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['savings-goals.*',  route('savings-goals.index'), 'Target Tabungan','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['categories.*',     route('categories.index'),    'Kategori',    'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
            ['reports.*',        route('reports.index'),       'Laporan Keuangan','M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['announcements.*',  route('announcements.index'), 'Pengumuman',  'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
            ['articles.*',       route('articles.index'),      'Artikel Edukasi','M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253'],
            ['messages.*',       route('messages.index'),      'Pesan & Bantuan','M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
            ['notifications.*',  route('notifications.index'), 'Notifikasi',  'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
            ['profile.*',        route('profile.show'),        'Profil Saya', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ];
        @endphp
        <div style="display:flex;flex-direction:column;gap:2px">
            @foreach($drawerLinks as [$pattern, $href, $label, $path])
                <a href="{{ $href }}" class="drawer-link {{ request()->routeIs($pattern) ? 'active' : '' }}" onclick="closeMobileDrawer()">
                    <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                    {{ $label }}
                    @if($pattern === 'notifications.*' && ($navUnread ?? 0) > 0)
                        <span style="margin-left:auto;background:#f43f5e;border-radius:999px;font-size:10px;font-weight:700;color:#fff;padding:1px 7px">{{ $navUnread > 9 ? '9+' : $navUnread }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Logout --}}
    <div style="padding:12px 14px 20px;border-top:1px solid rgba(147,51,234,.1);flex-shrink:0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:12px;font-size:14px;font-weight:500;color:#f87171;background:rgba(248,113,113,.08);border:none;cursor:pointer;transition:background .15s" onmouseover="this.style.background='rgba(248,113,113,.15)'" onmouseout="this.style.background='rgba(248,113,113,.08)'">
                <svg style="width:17px;height:17px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar Akun
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════ MAIN CONTENT ═══════════════════ --}}
<div id="app-content">

    {{-- Page heading --}}
    <div class="page-heading">
        <h1 style="font-size:22px;font-weight:800;color:#0f0a1e;letter-spacing:-.02em;margin:0 0 2px">@yield('page_title', 'Dashboard')</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">@yield('page_subtitle', '')</p>
    </div>

    {{-- Flash messages --}}
    <div style="padding:12px 16px 0" class="sm:px-8">
        @if(session('success'))
            <div class="flash-alert" style="margin-bottom:12px;padding:12px 16px;border-radius:12px;background:#f0fdf4;border:1px solid #bbf7d0;display:flex;align-items:center;justify-content:space-between;gap:12px" role="alert">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:28px;height:28px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:14px;height:14px;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p style="font-size:13px;color:#15803d;margin:0;font-weight:500">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.closest('.flash-alert').remove()" style="background:none;border:none;cursor:pointer;color:#86efac;padding:0">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-alert" style="margin-bottom:12px;padding:12px 16px;border-radius:12px;background:#fdf4ff;border:1px solid #e9d5ff;display:flex;align-items:center;justify-content:space-between;gap:12px" role="alert">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:28px;height:28px;border-radius:8px;background:#f3e8ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:14px;height:14px;color:#9333ea" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p style="font-size:13px;color:#7c3aed;margin:0;font-weight:500">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="this.closest('.flash-alert').remove()" style="background:none;border:none;cursor:pointer;color:#c084fc;padding:0">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="flash-alert" style="margin-bottom:12px;padding:12px 16px;border-radius:12px;background:#eff6ff;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:space-between;gap:12px" role="alert">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:28px;height:28px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:14px;height:14px;color:#2563eb" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p style="font-size:13px;color:#1d4ed8;margin:0;font-weight:500">{{ session('info') }}</p>
                </div>
                <button type="button" onclick="this.closest('.flash-alert').remove()" style="background:none;border:none;cursor:pointer;color:#93c5fd;padding:0">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="flash-alert" style="margin-bottom:12px;padding:12px 16px;border-radius:12px;background:#fdf4ff;border:1px solid #e9d5ff" role="alert">
                <div style="display:flex;align-items:flex-start;gap:10px">
                    <div style="width:28px;height:28px;border-radius:8px;background:#f3e8ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                        <svg style="width:14px;height:14px;color:#9333ea" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#7c3aed;margin:0 0 4px">Ada kesalahan input:</p>
                        <ul style="margin:0;padding:0 0 0 14px">
                            @foreach($errors->all() as $err)
                                <li style="font-size:12px;color:#9333ea;line-height:1.6">{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Page content --}}
    <main class="content-pad" style="flex:1;max-width:1400px;width:100%;margin:0 auto">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer style="padding:20px 24px;border-top:1px solid #f3e8ff;text-align:center" class="hidden md:block">
        <p style="font-size:12px;color:#9ca3af;margin:0">&copy; {{ date('Y') }} <strong style="color:#7c3aed">DuitPelajar</strong> — Aplikasi Keuangan Harian untuk Pelajar &amp; Mahasiswa.</p>
    </footer>
</div>

{{-- ═══════════════════ MOBILE BOTTOM NAV ═══════════════════ --}}
<nav id="bottom-nav">
    <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        <span>Home</span>
    </a>
    <a href="{{ route('transactions.index') }}" class="bottom-nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <span>Transaksi</span>
    </a>
    <a href="{{ route('transactions.create') }}" class="bottom-nav-item" style="flex:none">
        <div class="fab">
            <svg style="width:22px;height:22px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        </div>
    </a>
    <a href="{{ route('budgets.index') }}" class="bottom-nav-item {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Anggaran</span>
    </a>
    <a href="{{ route('reports.index') }}" class="bottom-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Laporan</span>
    </a>
</nav>

{{-- ═══════════════════ GLOBAL SCRIPTS ═══════════════════ --}}
<script>
(function () {
    'use strict';

    /* ── User menu dropdown ── */
    var menuBtn  = document.getElementById('user-menu-btn');
    var menuDrop = document.getElementById('user-menu-dropdown');
    if (menuBtn && menuDrop) {
        menuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = menuDrop.style.display === 'block';
            menuDrop.style.display = open ? 'none' : 'block';
        });
        document.addEventListener('click', function () {
            if (menuDrop) menuDrop.style.display = 'none';
        });
    }

    /* ── Mobile drawer ── */
    var hamburger = document.getElementById('hamburger-btn');
    var overlay   = document.getElementById('mobile-drawer-overlay');
    var drawer    = document.getElementById('mobile-drawer');

    window.closeMobileDrawer = function () {
        if (overlay) overlay.classList.remove('open');
        if (drawer)  drawer.classList.remove('open');
    };
    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (overlay) overlay.classList.add('open');
            if (drawer)  drawer.classList.add('open');
        });
    }

    /* ── Global modal helpers (used by budgets, savings-goals) ── */
    window.openModal = function (id) {
        var m = document.getElementById(id);
        if (m) { m.classList.remove('hidden'); m.style.display = 'flex'; }
    };
    window.closeModal = function (id) {
        var m = document.getElementById(id);
        if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
    };

    /* ── Flash auto-dismiss (5s) ── */
    document.querySelectorAll('.flash-alert').forEach(function (el) {
        setTimeout(function () { if (el.parentNode) el.remove(); }, 5000);
    });
})();
</script>

{{-- ═══════════════════ SINGLE TAB ENFORCEMENT ═══════════════════ --}}
@auth
<script>
(function () {
    'use strict';
    var userId   = @json((string) Auth::id());
    var lockKey  = 'duitpelajar_active_tab_' + userId;
    var tabKey   = 'duitpelajar_tab_id_' + userId;
    var LOCK_TTL = 10000;
    var HEARTBEAT = 3000;
    var tabId  = sessionStorage.getItem(tabKey);
    if (!tabId) {
        tabId = (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(36).slice(2));
        sessionStorage.setItem(tabKey, tabId);
    }
    var blocked = false;
    var timer   = null;

    function getLock() { try { return JSON.parse(localStorage.getItem(lockKey)); } catch(e) { return null; } }
    function isFresh(l) { return l && l.tabId && (Date.now() - Number(l.heartbeat)) < LOCK_TTL; }
    function writeLock() { localStorage.setItem(lockKey, JSON.stringify({ tabId: tabId, heartbeat: Date.now() })); }

    function block() {
        if (blocked) return;
        blocked = true;
        if (timer) clearInterval(timer);
        document.documentElement.classList.add('single-tab-blocked');
        var el = document.createElement('div');
        el.id = 'single-tab-blocker';
        el.innerHTML = '<div style="max-width:420px;width:100%;padding:36px 28px;background:#1a0533;border:1px solid rgba(147,51,234,.3);border-radius:24px;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.5)">' +
            '<div style="width:56px;height:56px;border-radius:16px;background:rgba(147,51,234,.2);color:#c084fc;font-size:26px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px">⚠</div>' +
            '<h2 style="font-size:20px;font-weight:800;color:#fff;margin:0 0 10px">Tab Tidak Diizinkan</h2>' +
            '<p style="font-size:14px;color:#9ca3af;line-height:1.7;margin:0 0 20px">DuitPelajar hanya dapat digunakan dalam satu tab sekaligus.</p>' +
            '<button onclick="window.close();this.textContent=\'Tutup melalui browser\'" style="width:100%;padding:12px;border-radius:12px;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-weight:700;border:none;cursor:pointer;font-size:14px">Tutup Tab Ini</button></div>';
        document.body.appendChild(el);
    }

    var existing = getLock();
    if (isFresh(existing) && existing.tabId !== tabId) { block(); return; }
    writeLock();
    timer = setInterval(function () {
        if (blocked) return;
        var cur = getLock();
        if (isFresh(cur) && cur.tabId !== tabId) { block(); return; }
        writeLock();
    }, HEARTBEAT);
    window.addEventListener('storage', function (e) {
        if (e.key !== lockKey) return;
        var cur = getLock();
        if (isFresh(cur) && cur.tabId !== tabId) block();
    });
}());
</script>
@endauth

@stack('scripts')
</body>
</html>
