<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — RVP Ticket Ball Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #94a3b8;
            transition: all 0.18s ease;
            text-decoration: none;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
        }
        .sidebar-link:hover { background: rgba(148,163,184,0.1); color: #e2e8f0; }
        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
        }
        .sidebar-link.logout-btn { color: #f87171; }
        .sidebar-link.logout-btn:hover { background: rgba(239,68,68,0.12); color: #fca5a5; }
        .sidebar-divider { border: none; border-top: 1px solid rgba(148,163,184,0.1); margin: 10px 0; }
        .section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
            padding: 0 14px;
            margin-bottom: 6px;
            margin-top: 8px;
            display: block;
        }
    </style>
</head>
<body class="bg-slate-100">

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 flex flex-col" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); min-height:100vh;">
        <!-- Brand -->
        <div class="px-5 py-6" style="border-bottom:1px solid rgba(148,163,184,0.1);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                     style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 14px rgba(37,99,235,0.4);">
                    ⚽
                </div>
                <div>
                    <h1 class="text-white font-bold text-sm leading-tight">RVP Ticket Ball</h1>
                    <p class="text-xs mt-0.5" style="color:#64748b;">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto">
            <span class="section-label">Menu Utama</span>
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span>📊</span> Dashboard
            </a>
            <a href="{{ route('admin.matches.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.matches*') ? 'active' : '' }}">
                <span>⚽</span> Pertandingan
            </a>
            <a href="{{ route('admin.stadiums.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.stadiums*') ? 'active' : '' }}">
                <span>🏟️</span> Stadion
            </a>
            <a href="{{ route('admin.ticket-categories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.ticket-categories*') ? 'active' : '' }}">
                <span>🎫</span> Kategori Tiket
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <span>📋</span> Pesanan
            </a>
            <a href="{{ route('admin.sanctions.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.sanctions*') ? 'active' : '' }}">
                <span>🚫</span> Sanksi & Blacklist
            </a>

            <hr class="sidebar-divider">
            <span class="section-label">Lainnya</span>

            <a href="{{ route('home') }}" class="sidebar-link">
                <span>🌐</span> Lihat Website
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link logout-btn">
                    <span>🚪</span> Logout
                </button>
            </form>
        </nav>

        <!-- User Info -->
        <div class="px-4 py-4" style="border-top:1px solid rgba(148,163,184,0.1);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#10b981,#059669);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs truncate" style="color:#64748b;">Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Header -->
        <header class="bg-white px-6 py-4 flex justify-between items-center sticky top-0 z-10"
                style="border-bottom:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div>
                <h2 class="text-base font-bold text-gray-900">@yield('title', 'Dashboard')</h2>
                <p class="text-xs text-gray-400 mt-0.5">RVP Ticket Ball — Panel Admin</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full"
                      style="background:#eff6ff;color:#2563eb;">⚽ RVP Ticket Ball</span>
                <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <main class="flex-1 p-6">
            @if (session('success'))
                <div class="mb-5 p-4 rounded-xl font-medium text-sm flex items-center gap-3"
                     style="background:#f0fdf4;border:1px solid #86efac;color:#166534;">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 p-4 rounded-xl font-medium text-sm flex items-center gap-3"
                     style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl font-medium text-sm"
                     style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
