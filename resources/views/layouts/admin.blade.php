<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Portal Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>
</head>
<body class="bg-slate-100 m-0 p-0 antialiased">

    <!-- Mobile overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed top-0 left-0 w-60 h-screen bg-slate-900 flex flex-col z-50">
        <!-- Logo -->
        <div class="sidebar-logo px-5 py-4 border-b border-white/[0.06]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img src="/images/logo.png" alt="Logo" class="w-full h-full object-cover rounded-lg">
                </div>
                <div class="sidebar-logo-text">
                    <h1 class="text-sm font-bold text-white leading-tight">Portal Ujian</h1>
                    <p class="text-[10px] text-slate-400 font-medium">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <div class="sidebar-divider-label px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.departments.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="sidebar-text">Departemen</span>
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="sidebar-text">Mata Kuliah</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="sidebar-text">Mahasiswa</span>
            </a>
            <a href="{{ route('admin.users.import') }}" class="sidebar-nav-item {{ request()->routeIs('admin.users.import') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span class="sidebar-text">Import / Export</span>
            </a>
            <a href="{{ route('admin.exam-schedules.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.exam-schedules.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="sidebar-text">Jadwal Ujian</span>
            </a>
            <a href="{{ route('admin.scores.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.scores.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="sidebar-text">Nilai</span>
            </a>
            <a href="{{ route('admin.logs') }}" class="sidebar-nav-item {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="sidebar-text">Log Aktivitas</span>
            </a>
            <div class="border-t border-white/[0.06] my-2"></div>
            <a href="{{ route('admin.notifications') }}" class="sidebar-nav-item {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="sidebar-text">Notifikasi</span>
            </a>
            <a href="{{ route('admin.broadcasts') }}" class="sidebar-nav-item {{ request()->routeIs('admin.broadcasts*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="sidebar-text">Broadcast</span>
            </a>
        </nav>

        <!-- User / Logout -->
        <div class="px-3 py-3 border-t border-white/[0.06]">
            <div class="user-info px-3 py-2 mb-1">
                <div class="text-sm font-semibold text-white/80 truncate sidebar-text">{{ Auth::guard('admin')->user()->name }}</div>
                <div class="text-xs text-slate-500 truncate sidebar-text">{{ Auth::guard('admin')->user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all duration-150">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="sidebar-text font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div id="mainContent" class="main-content ml-60 min-h-screen">
        <!-- Mobile Topbar -->
        <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center gap-3 sticky top-0 z-30">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="text-sm font-bold text-slate-900">Portal Ujian</span>
        </div>

        <!-- Desktop Topbar -->
        <div class="hidden lg:flex items-center justify-between px-7 py-3 bg-white border-b border-slate-200 sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Toggle Sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <div id="topbar-greeting" class="text-sm font-semibold text-slate-700">Hai, Super Admin</div>
            </div>
            <div class="flex items-center gap-5">
                <div id="topbar-clock" class="text-xs text-slate-400 font-medium tabular-nums"></div>
                <div class="relative">
                <button id="notifBell" class="relative p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full px-1">0</span>
                </button>
                <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-900">Notifikasi</span>
                        <a href="{{ route('admin.notifications') }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
                    </div>
                    <div id="notifList" class="max-h-80 overflow-y-auto">
                        <div class="p-4 text-center text-xs text-slate-400">Memuat...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-5 lg:p-7">
            @yield('content')
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none"></div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
            <div class="p-6 text-center">
                <div id="confirm-icon" class="w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center"></div>
                <h3 id="confirm-title" class="text-base font-semibold text-slate-900 mb-1"></h3>
                <p id="confirm-message" class="text-sm text-slate-500"></p>
            </div>
            <div class="flex border-t border-slate-100">
                <button id="confirm-cancel" class="flex-1 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 transition">Batal</button>
                <button id="confirm-ok" class="flex-1 py-3 text-sm font-medium transition border-l border-slate-100"></button>
            </div>
        </div>
    </div>

    <script>
        // Greeting based on time of day
        (function() {
            const greeting = document.getElementById('topbar-greeting');
            const hour = new Date().getHours();
            let greetingText = 'Hai';
            if (hour >= 5 && hour < 12) greetingText = 'Selamat Pagi';
            else if (hour >= 12 && hour < 15) greetingText = 'Selamat Siang';
            else if (hour >= 15 && hour < 18) greetingText = 'Selamat Sore';
            else if (hour >= 18 && hour < 22) greetingText = 'Selamat Malam';
            else greetingText = 'Halo, Night Owl';
            if (greeting) greeting.textContent = greetingText + ', Super Admin';
        })();

        // Clock & date display
        (function() {
            const clock = document.getElementById('topbar-clock');
            if (!clock) return;
            function updateClock() {
                const now = new Date();
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const jam = String(now.getHours()).padStart(2, '0');
                const menit = String(now.getMinutes()).padStart(2, '0');
                const detik = String(now.getSeconds()).padStart(2, '0');
                const hari = days[now.getDay()];
                const tanggal = now.getDate();
                const bulan = months[now.getMonth()];
                const tahun = now.getFullYear();
                clock.textContent = jam + ':' + menit + ':' + detik + '  •  ' + hari + ', ' + tanggal + ' ' + bulan + ' ' + tahun;
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();

        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');
        let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

        function applySidebarState() {
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            if (window.innerWidth < 1024) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('hidden');
            } else {
                isCollapsed = !isCollapsed;
                applySidebarState();
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
        }

        applySidebarState();
        window.addEventListener('resize', applySidebarState);

        // Session timeout - auto logout after 15 minutes of inactivity
        (function() {
            const TIMEOUT_MS = 15 * 60 * 1000; // 15 minutes
            let lastActivity = Date.now();
            let timeoutTimer;

            function resetTimer() {
                lastActivity = Date.now();
                clearTimeout(timeoutTimer);
                timeoutTimer = setTimeout(() => {
                    // Check if user is still logged in
                    @auth('admin')
                    window.location.href = '{{ route('admin.logout') }}?timeout=1';
                    @endauth
                }, TIMEOUT_MS);
            }

            ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'click'].forEach(evt => {
                document.addEventListener(evt, resetTimer, { passive: true });
            });

            resetTimer();
        })();

        // Toast
        const toastContainer = document.getElementById('toast-container');
        const toastIcons = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        const toastStyles = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };

        function showToast(message, type = 'success', duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg border text-sm font-medium shadow-lg ${toastStyles[type]} translate-x-4 opacity-0 transition-all duration-200`;
            toast.innerHTML = `<span class="flex-shrink-0">${toastIcons[type]}</span><span>${message}</span>`;
            toastContainer.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-4', 'opacity-0');
            });
            setTimeout(() => {
                toast.classList.add('translate-x-4', 'opacity-0');
                setTimeout(() => toast.remove(), 200);
            }, duration);
        }

        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('{{ addslashes($error) }}', 'error', 6000);
            @endforeach
        @endif

        // Confirm Modal
        let confirmResolve = null;
        const confirmModal = document.getElementById('confirm-modal');
        const confirmIcon = document.getElementById('confirm-icon');
        const confirmTitle = document.getElementById('confirm-title');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmOk = document.getElementById('confirm-ok');
        const confirmCancel = document.getElementById('confirm-cancel');

        const okStyles = {
            danger: 'bg-red-600 hover:bg-red-700 text-white',
            success: 'bg-emerald-600 hover:bg-emerald-700 text-white',
            primary: 'bg-slate-900 hover:bg-slate-800 text-white',
            warning: 'bg-amber-500 hover:bg-amber-600 text-white'
        };
        const iconBg = {
            danger: 'bg-red-100 text-red-600',
            success: 'bg-emerald-100 text-emerald-600',
            primary: 'bg-slate-100 text-slate-600',
            warning: 'bg-amber-100 text-amber-600'
        };
        const iconSvgs = {
            danger: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
            success: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            primary: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
        };

        document.getElementById('confirm-cancel').addEventListener('click', () => {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
            if (confirmResolve) confirmResolve(false);
        });
        document.getElementById('confirm-ok').addEventListener('click', () => {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
            if (confirmResolve) confirmResolve(true);
        });

        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const msg = this.dataset.confirm || 'Yakin ingin melanjutkan?';
                const type = this.dataset.confirmType || 'danger';

                confirmIcon.className = `w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center ${iconBg[type]}`;
                confirmIcon.innerHTML = iconSvgs[type];
                confirmTitle.textContent = 'Konfirmasi';
                confirmMessage.textContent = msg;
                confirmOk.textContent = this.dataset.confirmOk || 'Ya, Lanjutkan';
                confirmOk.className = `flex-1 py-3 text-sm font-semibold transition border-l border-slate-100 ${okStyles[type]}`;

                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');

                confirmResolve = (ok) => {
                    if (ok && form) form.submit();
                };
            });
        });

        // Notification bell dropdown
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        const badge = document.getElementById('notifBadge');
        const notifList = document.getElementById('notifList');

        if (bell) {
            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', () => dropdown.classList.add('hidden'));
        }

        function loadNotifications() {
            fetch('/admin/notifications/recent')
                .then(r => r.json())
                .then(data => {
                    const count = data.unread_count;
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                    } else {
                        badge.classList.add('hidden');
                    }

                    if (data.notifications.length === 0) {
                        notifList.innerHTML = '<div class="p-4 text-center text-xs text-slate-400">Belum ada notifikasi</div>';
                        return;
                    }

                    notifList.innerHTML = data.notifications.map(n => `
                        <div class="px-4 py-3 hover:bg-slate-50 transition ${!n.read_at ? 'bg-blue-50/30' : ''}">
                            <div class="flex items-start gap-2">
                                ${!n.read_at ? '<span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>' : '<span class="w-1.5 h-1.5 flex-shrink-0"></span>'}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">${n.title}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">${n.created_at}</p>
                                    ${n.link ? `<a href="${n.link}" class="text-[11px] text-blue-600 hover:underline mt-1 inline-block">Lihat</a>` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(() => {});
        }

        // Session timeout — logout after 15 min idle
        let sessionTimeout;
        const SESSION_TIMEOUT = 15 * 60 * 1000;
        function resetSessionTimer() {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(() => {
                if (confirm('Sesi kamu telah habis karena tidak aktif. Kamu akan dialihkan ke halaman login.')) {
                    window.location.href = '/admin/logout';
                } else {
                    resetSessionTimer();
                }
            }, SESSION_TIMEOUT);
        }
        ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, resetSessionTimer, { passive: true });
        });
        resetSessionTimer();

        loadNotifications();
        setInterval(loadNotifications, 30000);
    </script>
</body>
</html>
