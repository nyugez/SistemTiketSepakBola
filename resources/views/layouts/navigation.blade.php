<nav x-data="{ open: false }" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);border-bottom:1px solid rgba(255,255,255,0.08);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">

                <!-- Logo / Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('matches.index') }}"
                       class="flex items-center gap-2.5 group" style="text-decoration:none;">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-base transition-transform group-hover:scale-105"
                             style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.4);">
                            ⚽
                        </div>
                        <span class="text-white font-bold text-base tracking-tight hidden sm:block">RVP Ticket Ball</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-1 sm:ms-8 sm:flex sm:items-center">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('admin.dashboard') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('admin.dashboard') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                Dashboard Admin
                            </a>
                            <a href="{{ route('admin.stadiums.index') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('admin.stadiums.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('admin.stadiums.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                Kelola Stadion
                            </a>
                            <a href="{{ route('admin.matches.index') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('admin.matches.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('admin.matches.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                Pertandingan
                            </a>
                            <a href="{{ route('admin.ticket-categories.index') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('admin.ticket-categories.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('admin.ticket-categories.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                Kategori Tiket
                            </a>
                            <a href="{{ route('admin.orders.index') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('admin.orders.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('admin.orders.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                Pesanan
                            </a>
                        @else
                            <a href="{{ route('matches.index') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('matches.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('matches.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                ⚽ Pertandingan
                            </a>
                            <a href="{{ route('orders.index') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('orders.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('orders.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                📋 Pesanan Saya
                            </a>
                            <a href="{{ route('tickets.my') }}"
                               style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('tickets.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('tickets.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;transition:all 0.15s;">
                                🎫 Tiket Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('matches.index') }}"
                           style="padding:6px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;color:{{ request()->routeIs('matches.*') ? '#ffffff' : 'rgba(255,255,255,0.65)' }};background:{{ request()->routeIs('matches.*') ? 'rgba(37,99,235,0.7)' : 'none' }};text-decoration:none;">
                            ⚽ Pertandingan
                        </a>
                    @endauth
                </div>
            </div>

            <!-- User Dropdown / Auth Actions -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all"
                                    style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);color:white;font-size:0.85rem;font-weight:500;cursor:pointer;">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                     style="background:linear-gradient(135deg,#10b981,#059669);">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    🚪 Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                       style="padding:7px 18px;border-radius:8px;font-size:0.85rem;font-weight:600;color:white;background:linear-gradient(135deg,#2563eb,#1d4ed8);text-decoration:none;box-shadow:0 3px 10px rgba(37,99,235,0.4);">
                        Login
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       style="padding:7px 18px;border-radius:8px;font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);text-decoration:none;">
                        Daftar
                    </a>
                    @endif
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-lg transition-colors"
                        style="color:rgba(255,255,255,0.6);background:rgba(255,255,255,0.06);">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden"
         style="border-top:1px solid rgba(255,255,255,0.08);">
        <div class="pt-2 pb-3 px-3 space-y-1">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">📊 Dashboard Admin</a>
                    <a href="{{ route('admin.stadiums.index') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">🏟️ Kelola Stadion</a>
                    <a href="{{ route('admin.matches.index') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">⚽ Pertandingan</a>
                    <a href="{{ route('admin.ticket-categories.index') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">🎫 Kategori Tiket</a>
                    <a href="{{ route('admin.orders.index') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">📋 Pesanan</a>
                @else
                    <a href="{{ route('matches.index') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">⚽ Pertandingan</a>
                    <a href="{{ route('orders.index') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">📋 Pesanan Saya</a>
                    <a href="{{ route('tickets.my') }}"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                       style="color:rgba(255,255,255,0.75);text-decoration:none;">🎫 Tiket Saya</a>
                @endif
            @else
                <a href="{{ route('matches.index') }}"
                   class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                   style="color:rgba(255,255,255,0.75);text-decoration:none;">⚽ Pertandingan</a>
            @endauth
        </div>

        <div class="pt-3 pb-4 px-3" style="border-top:1px solid rgba(255,255,255,0.08);">
            @auth
                <div class="flex items-center gap-3 px-4 mb-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#10b981,#059669);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.45);">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left block px-4 py-2.5 rounded-lg text-sm font-medium"
                            style="color:#f87171;background:rgba(239,68,68,0.08);border:none;cursor:pointer;">
                        🚪 Logout
                    </button>
                </form>
            @else
                <div class="flex gap-2 px-4">
                    <a href="{{ route('login') }}"
                       class="flex-1 text-center py-2.5 rounded-lg text-sm font-semibold"
                       style="background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;text-decoration:none;">Login</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="flex-1 text-center py-2.5 rounded-lg text-sm font-semibold"
                       style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);border:1px solid rgba(255,255,255,0.16);text-decoration:none;">Daftar</a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
