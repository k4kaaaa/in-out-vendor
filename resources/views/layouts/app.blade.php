<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In-Out Vendor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sidebar: '#0b3b2e',
                        sidebarhover: '#11503f',
                        accent: '#16a34a',
                        primary: '#16a34a',
                        secondary: '#0b3b2e',
                        surface: '#f6f8f7',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-surface font-sans text-gray-800 antialiased">
<div class="min-h-screen flex">

    <!-- Sidebar Dynamic -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-200 flex flex-col shadow-xl">
        <div class="px-6 py-5 flex items-center gap-3 border-b border-white/10 bg-sidebar">
            <div class="w-10 h-10 flex items-center justify-center shrink-0 text-emerald-400">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
            <span id="brand-text" class="text-base font-black tracking-tighter transition-all duration-200 whitespace-nowrap uppercase">In-Out Vendor</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <p id="menu-label" class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-white/40 transition-all duration-200">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-accent text-white shadow-lg shadow-accent/30 font-semibold' : 'text-white/70 hover:bg-sidebarhover hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="transition-all duration-200 whitespace-nowrap menu-text">Dashboard</span>
            </a>

            <a href="{{ route('transactions.index') }}"
               class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('transactions.*') ? 'bg-accent text-white shadow-lg shadow-accent/30 font-semibold' : 'text-white/70 hover:bg-sidebarhover hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
                <span class="transition-all duration-200 whitespace-nowrap menu-text">Barang Masuk</span>
            </a>

            <a href="{{ route('products.index') }}"
               class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-accent text-white shadow-lg shadow-accent/30 font-semibold' : 'text-white/70 hover:bg-sidebarhover hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                <span class="transition-all duration-200 whitespace-nowrap menu-text">Data Barang</span>
            </a>

            @if(auth()->check() && auth()->user()->role !== 'staff')
            <a href="{{ route('vendors.index') }}"
               class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('vendors.*') ? 'bg-accent text-white shadow-lg shadow-accent/30 font-semibold' : 'text-white/70 hover:bg-sidebarhover hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17h8M5 12h14M3 7h18v14H3V7zm4-4h10l2 4H5l2-4z"></path>
                </svg>
                <span class="transition-all duration-200 whitespace-nowrap menu-text">Vendor</span>
            </a>

            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-accent text-white shadow-lg shadow-accent/30 font-semibold' : 'text-white/70 hover:bg-sidebarhover hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="transition-all duration-200 whitespace-nowrap menu-text">Laporan</span>
            </a>
            @endif
        </nav>

        <div class="px-6 py-4 border-t border-white/10 flex items-center justify-between">
            <span id="footer-text" class="text-xs text-white/40 transition-all duration-200 whitespace-nowrap">SISBAR-IN © 2026</span>
            <button id="collapse-btn" class="hidden lg:flex p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition" title="Collapse/Expand Sidebar">
                <svg id="collapse-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    <!-- Main Wrapper -->
    <div id="main-wrapper" class="flex-1 flex flex-col min-w-0 lg:ml-64 transition-[margin] duration-200">

        <!-- Topbar -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-100 shadow-sm">
            <div class="flex items-center justify-between gap-4 px-4 sm:px-8 py-3">
                <!-- Hamburger toggle -->
                <button id="menu-toggle" class="p-2 rounded-xl text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none transition shrink-0" title="Buka/Menutup Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex items-center gap-2 min-w-0">
                    <span class="font-semibold text-gray-800">Warehouse Management</span>
                    <span class="text-accent">/ Inbound operations</span>
                </div>

                @auth
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-accent text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-right leading-tight">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->role === 'staff' ? 'Staff Gudang' : 'Admin/Manager' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 p-4 sm:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium shadow-sm">
                    {{ session('success' )}}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-sm font-medium shadow-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggle = document.getElementById('menu-toggle');
    const mainWrapper = document.getElementById('main-wrapper');
    const collapseBtn = document.getElementById('collapse-btn');
    const collapseIcon = document.getElementById('collapse-icon');
    const menuTexts = document.querySelectorAll('.menu-text');
    const brandText = document.getElementById('brand-text');
    const menuLabel = document.getElementById('menu-label');
    const footerText = document.getElementById('footer-text');

    const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    function setCollapsed(collapsed) {
        if (collapsed) {
            sidebar.classList.add('lg:w-20');
            sidebar.classList.remove('lg:w-64');
            mainWrapper.classList.add('lg:ml-20');
            mainWrapper.classList.remove('lg:ml-64');
            menuTexts.forEach(el => el.classList.add('lg:hidden'));
            brandText?.classList.add('lg:hidden');
            menuLabel?.classList.add('lg:hidden');
            footerText?.classList.add('lg:hidden');
            collapseIcon?.classList.add('rotate-180');
        } else {
            sidebar.classList.add('lg:w-64');
            sidebar.classList.remove('lg:w-20');
            mainWrapper.classList.add('lg:ml-64');
            mainWrapper.classList.remove('lg:ml-20');
            menuTexts.forEach(el => el.classList.remove('lg:hidden'));
            brandText?.classList.remove('lg:hidden');
            menuLabel?.classList.remove('lg:hidden');
            footerText?.classList.remove('lg:hidden');
            collapseIcon?.classList.remove('rotate-180');
        }
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }

    function toggleSidebar() {
        if (isDesktop()) {
            setCollapsed(!sidebar.classList.contains('lg:w-20'));
        } else {
            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        }
    }

    // restore desktop state
    if (localStorage.getItem('sidebarCollapsed') === '1' && isDesktop()) {
        setCollapsed(true);
    }

    toggle?.addEventListener('click', toggleSidebar);
    collapseBtn?.addEventListener('click', () => setCollapsed(!sidebar.classList.contains('lg:w-20')));
    overlay?.addEventListener('click', closeSidebar);

    // reset mobile translate when resizing to desktop
    window.addEventListener('resize', () => {
        if (isDesktop()) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    });
</script>
</body>
</html>
