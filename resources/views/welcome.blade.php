<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }
        .gradient-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(16,185,129,0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .dot-pattern {
            background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="gradient-hero min-h-screen flex items-center justify-center px-4 relative overflow-hidden dot-pattern">
    <div class="dot-pattern absolute inset-0"></div>

    <div class="relative text-center max-w-lg w-full">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-4xl sm:text-5xl font-bold text-white tracking-tight mb-3">Portal Ujian</h1>
        <p class="text-slate-400 text-base sm:text-lg mb-10">Sistem Pengelolaan Nilai dan Jadwal Ujian</p>

        <!-- Buttons -->
        <div class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:justify-center">
            <a href="{{ route('user.login') }}"
               class="card-hover flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-white text-slate-900 rounded-xl font-semibold text-sm shadow-xl shadow-black/10 hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <div class="text-left">
                    <div class="text-slate-500 text-xs">Login sebagai</div>
                    <div class="font-bold">Mahasiswa</div>
                </div>
            </a>
            <a href="{{ route('admin.login') }}"
               class="card-hover flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-white text-slate-900 rounded-xl font-semibold text-sm shadow-xl shadow-black/10 hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <div class="text-left">
                    <div class="text-slate-500 text-xs">Login sebagai</div>
                    <div class="font-bold">Admin</div>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-12 text-slate-500 text-xs">Portal Ujian &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
