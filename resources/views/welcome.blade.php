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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);">
    <!-- Green gradient background -->

    <div class="relative text-center max-w-lg w-full">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <img src="/images/logo.png" alt="Logo" class="w-20 h-20 object-contain rounded-2xl shadow-lg">
        </div>

        <!-- Title -->
        <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 tracking-tight mb-3">Portal Ujian</h1>
        <p class="text-slate-600 text-base sm:text-lg mb-10">Sistem Pengelolaan Nilai dan Jadwal Ujian</p>

        <!-- Buttons -->
        <div class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:justify-center">
            <a href="{{ route('user.login') }}"
               class="card-hover flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-white text-slate-900 rounded-xl font-semibold text-sm shadow-xl shadow-black/10 hover:bg-slate-50 transition-all border border-slate-200">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <div class="text-left">
                    <div class="text-slate-500 text-xs font-medium">Login sebagai</div>
                    <div class="font-bold">Mahasiswa</div>
                </div>
            </a>
            <a href="{{ route('admin.login') }}"
               class="card-hover flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-emerald-600 text-white rounded-xl font-semibold text-sm shadow-xl shadow-black/10 hover:bg-emerald-700 transition-all">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <div class="text-left">
                    <div class="text-emerald-200 text-xs font-medium">Login sebagai</div>
                    <div class="font-bold">Admin</div>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-12 text-slate-400 text-sm">Portal Ujian &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
