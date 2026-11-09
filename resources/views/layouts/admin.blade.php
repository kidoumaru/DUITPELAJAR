<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') — DuitPelajar Admin</title>

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

        /* ── Top Navbar ── */
        #admin-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 9000;
            background: rgba(15,10,30,.97);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(147,51,234,.25);
            height: 64px;
            display: flex;
            align-items: center;
        }

        /* ── Admin badge pill ── */
        .admin-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 8px; border-radius: 999px;
            font-size: 10px; font-weight: 700; letter-spacing: .04em;
            background: linear-gradient(135deg,rgba(147,51,234,.4),rgba(124,58,237,.3));
            color: #c084fc;
            border: 1px solid rgba(147,51,234,.3);
            text-transform: uppercase;
        }

        /* ── Mobile drawer ── */
        #admin-drawer-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 9500;
            background: rgba(15,10,30,.65);
            backdrop-filter: blur(4px);
        }
        #admin-drawer-overlay.open { display: block; }
        #admin-drawer {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 280px; z-index: 9600;
            background: #0f0a1e;
            border-right: 1px solid rgba(147,51,234,.2);
            transform: translateX(-100%);
            transition: transform .3s ease;
            display: flex; flex-direction: column;
            overflow-y: auto;
        }
        #admin-drawer.open { transform: translateX(0); }

        /* ── Nav link ── */
        .nav-link {
            display: flex; align-items: center; gap: 7px;
            padding: 7px 12px; border-radius: 9px;
            font-size: 12.5px; font-weight: 500;
            color: rgba(255,255,255,.6);
            text-decoration: none; white-space: nowrap;
            transition: color .15s, background .15s;
        }
        .nav-link:hover { color: #fff; background: rgba(147,51,234,.2); }
        .nav-link.active { color: #fff; background: linear-gradient(135deg,rgba(147,51,234,.5),rgba(124,58,237,.4)); }
        .nav-link svg { flex-shrink: 0; opacity: .7; }
        .nav-link.active svg { opacity: 1; }

        /* ── Drawer link ── */
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

        /* ── Content area ── */
        #admin-content {
            flex: 1;
            margin-top: 64px;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 64px);
        }

        /* ── Flash ── */
        .flash-alert { animation: slideDown .3s ease; }
        @keyframes slideDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:none} }

        /* ── Page heading ── */
        .page-heading { padding: 24px 16px 0; }
        @media (min-width:640px) { .page-heading { padding: 28px 32px 0; } }

        /* ── Content padding ── */
        .content-pad { padding: 20px 16px 40px; }
        @media (min-width:640px) { .content-pad { padding: 24px 32px 48px; } }

        /* ── Desktop nav scroll ── */
        #admin-desktop-nav {
            display: none;
            overflow-x: auto;
            scrollbar-width: none;
            flex: 1;
        }
        #admin-desktop-nav::-webkit-scrollbar { display: none; }
        @media (min-width: 1024px) { #admin-desktop-nav { display: flex; align-items: center; gap: 2px; } }

        /* ── Single tab blocker ── */
        html.admin-tab-blocked body > *:not(#admin-tab-blocker) { visibility: hidden !important; }
        #admin-tab-blocker {
            position: fixed; inset: 0; z-index: 999999;
            display: flex; align-items: center; justify-content: center;
            padding: 24px; background: #0f0a1e; visibility: visible !important;
        }

        /* ── Shared card/btn ── */
        .app-card {
            background: #fff; border-radius: 16px;
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
    </style>
</head>
<body>

{{-- ═══ TOP NAVBAR ═══ --}}
<nav id="admin-navbar">
    <div style="max-width:1400px;margin:0 auto;padding:0 16px;width:100%;display:flex;align-items:center;gap:12px">

        {{-- Logo + Admin badge --}}
        <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:9px;text-decoration:none;flex-shrink:0">
            <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(147,51,234,.4)">
                <svg style="width:18px;height:18px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div style="flex-shrink:0">
                <span style="font-size:15px;font-weight:800;background:linear-gradient(135deg,#c084fc,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:block;line-height:1.2">DuitPelajar</span>
                <span class="admin-badge">Panel Admin</span>
            </div>
        </a>

        {{-- Desktop nav links --}}
        <div id="admin-desktop-nav">
            @php
            $adminLinks = [
                ['admin.dashboard',       route('admin.dashboard'),         'Dashboard',        'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                ['admin.users.*',         route('admin.users.index'),        'Pengguna',         'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z'],
                ['admin.categories.*',    route('admin.categories.index'),   'Kategori',         'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                ['admin.announcements.*', route('admin.announcements.index'),'Pengumuman',       'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                ['admin.messages.*',      route('admin.messages.index'),     'Pesan',            'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ['admin.articles.*',      route('admin.articles.index'),     'Artikel',          'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253'],
                ['admin.statistics.*',    route('admin.statistics.index'),   'Statistik',        'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['admin.activity-logs.*', route('admin.activity-logs.index'),'Aktivitas',        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ];
            @endphp
            @foreach($adminLinks as [$pattern,$href,$label,$path])
                <a href="{{ $href }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Right: Profile + hamburger --}}
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:auto">

            {{-- Profile dropdown --}}
            <div style="position:relative">
                <button id="admin-user-btn" type="button" style="display:flex;align-items:center;gap:8px;padding:4px 12px 4px 4px;border-radius:999px;background:rgba(147,51,234,.15);border:none;cursor:pointer;transition:background .15s" onmouseover="this.style.background='rgba(147,51,234,.25)'" onmouseout="this.style.background='rgba(147,51,234,.15)'">
                    <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                    <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,.8);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="hidden lg:block">{{ Auth::user()->name }}</span>
                    <svg style="width:14px;height:14px;color:rgba(255,255,255,.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="hidden lg:block"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div id="admin-user-drop" style="display:none;position:absolute;top:calc(100% + 8px);right:0;min-width:200px;background:#1a0533;border:1px solid rgba(147,51,234,.25);border-radius:14px;box-shadow:0 16px 48px rgba(0,0,0,.4);overflow:hidden;z-index:1000">
                    <div style="padding:12px 16px;border-bottom:1px solid rgba(147,51,234,.15)">
                        <p style="font-size:13px;font-weight:600;color:#fff;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->name }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0">Administrator</p>
                    </div>
                    <div style="padding:6px">
                        <a href="{{ route('admin.profile.show') }}" style="display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:10px;font-size:13px;color:rgba(255,255,255,.75);text-decoration:none;transition:background .15s" onmouseover="this.style.background='rgba(147,51,234,.2)'" onmouseout="this.style.background='transparent'">
                            <svg style="width:14px;height:14px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Admin
                        </a>
                        <div style="height:1px;background:rgba(147,51,234,.1);margin:4px 0"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="width:100%;display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:10px;font-size:13px;color:#f87171;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s" onmouseover="this.style.background='rgba(248,113,113,.1)'" onmouseout="this.style.background='transparent'">
                                <svg style="width:14px;height:14px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Hamburger (mobile + tablet) --}}
            <button id="admin-hamburger" type="button" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:rgba(147,51,234,.15);border:none;cursor:pointer;color:rgba(255,255,255,.7)" class="lg:hidden">
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</nav>

{{-- ═══ MOBILE DRAWER ═══ --}}
<div id="admin-drawer-overlay" onclick="closeAdminDrawer()"></div>
<div id="admin-drawer">
    <div style="padding:20px 20px 16px;border-bottom:1px solid rgba(147,51,234,.15);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:16px;height:16px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
                <span style="font-size:15px;font-weight:800;background:linear-gradient(135deg,#c084fc,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:block;line-height:1.2">DuitPelajar</span>
                <span style="font-size:10px;font-weight:700;color:#9333ea;text-transform:uppercase;letter-spacing:.04em">Panel Admin</span>
            </div>
        </div>
        <button onclick="closeAdminDrawer()" style="width:28px;height:28px;border-radius:8px;background:rgba(147,51,234,.15);border:none;cursor:pointer;color:rgba(255,255,255,.6);display:flex;align-items:center;justify-content:center">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div style="padding:12px 16px;border-bottom:1px solid rgba(147,51,234,.1);flex-shrink:0">
        <div style="display:flex;align-items:center;gap:10px;background:rgba(147,51,234,.12);border-radius:12px;padding:10px 12px">
            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div style="min-width:0">
                <p style="font-size:13px;font-weight:600;color:#fff;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->name }}</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">Administrator</p>
            </div>
        </div>
    </div>

    <nav style="padding:12px 14px;flex:1;overflow-y:auto">
        <div style="display:flex;flex-direction:column;gap:2px">
            @foreach($adminLinks as [$pattern,$href,$label,$path])
                <a href="{{ $href }}" class="drawer-link {{ request()->routeIs($pattern) ? 'active' : '' }}" onclick="closeAdminDrawer()">
                    <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    <div style="padding:12px 14px 20px;border-top:1px solid rgba(147,51,234,.1);flex-shrink:0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:12px;font-size:14px;font-weight:500;color:#f87171;background:rgba(248,113,113,.08);border:none;cursor:pointer;transition:background .15s" onmouseover="this.style.background='rgba(248,113,113,.15)'" onmouseout="this.style.background='rgba(248,113,113,.08)'">
                <svg style="width:17px;height:17px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</div>

{{-- ═══ MAIN CONTENT ═══ --}}
<div id="admin-content">

    {{-- Page heading --}}
    <div class="page-heading">
        <h1 style="font-size:22px;font-weight:800;color:#0f0a1e;letter-spacing:-.02em;margin:0 0 2px">@yield('page_title', 'Dashboard Admin')</h1>
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
                        <p style="font-size:13px;font-weight:600;color:#7c3aed;margin:0 0 4px">Ada kesalahan:</p>
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
    <footer style="padding:20px 24px;border-top:1px solid #f3e8ff;text-align:center">
        <p style="font-size:12px;color:#9ca3af;margin:0">&copy; {{ date('Y') }} <strong style="color:#7c3aed">DuitPelajar</strong> — Panel Administrator.</p>
    </footer>
</div>

{{-- ═══ SCRIPTS ═══ --}}
<script>
(function () {
    'use strict';

    /* ── User dropdown ── */
    var btn  = document.getElementById('admin-user-btn');
    var drop = document.getElementById('admin-user-drop');
    if (btn && drop) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            drop.style.display = drop.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function () { if (drop) drop.style.display = 'none'; });
    }

    /* ── Mobile drawer ── */
    var hamburger = document.getElementById('admin-hamburger');
    var overlay   = document.getElementById('admin-drawer-overlay');
    var drawer    = document.getElementById('admin-drawer');

    window.closeAdminDrawer = function () {
        if (overlay) overlay.classList.remove('open');
        if (drawer)  drawer.classList.remove('open');
    };
    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (overlay) overlay.classList.add('open');
            if (drawer)  drawer.classList.add('open');
        });
    }

    /* ── Global modal helpers ── */
    window.openModal = function (id) {
        var m = document.getElementById(id);
        if (m) { m.classList.remove('hidden'); m.style.display = 'flex'; }
    };
    window.closeModal = function (id) {
        var m = document.getElementById(id);
        if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
    };

    /* ── Flash auto-dismiss ── */
    document.querySelectorAll('.flash-alert').forEach(function (el) {
        setTimeout(function () { if (el.parentNode) el.remove(); }, 5000);
    });
})();
</script>

{{-- ═══ ADMIN SINGLE TAB ENFORCEMENT ═══ --}}
@auth
@if(Auth::user()->isAdmin())
<script>
(function () {
    'use strict';
    var adminId  = @json((string) Auth::id());
    var lockKey  = 'duitpelajar_admin_active_tab_' + adminId;
    var tabKey   = 'duitpelajar_admin_tab_id_' + adminId;
    var LOCK_TTL = 15000;
    var HEARTBEAT = 3000;
    var tabId = sessionStorage.getItem(tabKey);
    if (!tabId) {
        tabId = (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(36).slice(2));
        sessionStorage.setItem(tabKey, tabId);
    }
    var blocked = false;
    var timer = null;
    function readLock() { try { return JSON.parse(localStorage.getItem(lockKey)); } catch(e) { return null; } }
    function isFresh(l) { return Boolean(l && l.tabId && Number.isFinite(Number(l.heartbeat)) && Date.now() - Number(l.heartbeat) < LOCK_TTL); }
    function writeLock() { localStorage.setItem(lockKey, JSON.stringify({ tabId: tabId, heartbeat: Date.now() })); }
    function block() {
        if (blocked) return;
        blocked = true;
        if (timer) clearInterval(timer);
        document.documentElement.classList.add('admin-tab-blocked');
        var el = document.createElement('div');
        el.id = 'admin-tab-blocker';
        el.innerHTML = '<div style="max-width:420px;width:100%;padding:36px 28px;background:#1a0533;border:1px solid rgba(147,51,234,.3);border-radius:24px;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.5)">' +
            '<div style="width:56px;height:56px;border-radius:16px;background:rgba(147,51,234,.2);color:#c084fc;font-size:26px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px">⚠</div>' +
            '<h2 style="font-size:20px;font-weight:800;color:#fff;margin:0 0 10px">Tab Tidak Diizinkan</h2>' +
            '<p style="font-size:14px;color:#9ca3af;line-height:1.7;margin:0 0 20px">Panel Admin DuitPelajar hanya dapat digunakan dalam satu tab sekaligus.</p>' +
            '<button onclick="window.close();this.textContent=\'Tutup melalui browser\'" style="width:100%;padding:12px;border-radius:12px;background:linear-gradient(135deg,#9333ea,#7c3aed);color:#fff;font-weight:700;border:none;cursor:pointer;font-size:14px">Tutup Tab Ini</button></div>';
        document.body.appendChild(el);
    }
    var existing = readLock();
    if (isFresh(existing) && existing.tabId !== tabId) { block(); return; }
    writeLock();
    timer = setInterval(function () {
        if (blocked) return;
        var cur = readLock();
        if (isFresh(cur) && cur.tabId !== tabId) { block(); return; }
        writeLock();
    }, HEARTBEAT);
    window.addEventListener('storage', function (e) {
        if (e.key !== lockKey) return;
        var cur = readLock();
        if (isFresh(cur) && cur.tabId !== tabId) block();
    });
}());
</script>
@endif
@endauth

@stack('scripts')
</body>
</html>
