<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>
</head>
<body class="bg-slate-50 min-h-screen font-sans">
    <!-- Top Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('user.dashboard') }}" class="text-lg font-bold text-slate-900 tracking-tight">
                    Portal <span class="text-emerald-600">Ujian</span>
                </a>
                <div class="flex items-center gap-1">
                    <!-- Notification Bell -->
                    <div class="relative mr-1">
                        <button id="notifBell" class="relative p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full px-1">0</span>
                        </button>
                        <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-900">Notifikasi</span>
                                <a href="{{ route('user.notifications') }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
                            </div>
                            <div id="notifList" class="max-h-72 overflow-y-auto">
                                <div class="p-4 text-center text-xs text-slate-400">Memuat...</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('user.profile.edit') }}" class="btn-ghost text-sm hidden sm:inline-flex">Profil</a>
                    <form method="POST" action="{{ route('user.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-ghost text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col gap-3 pointer-events-none max-w-sm"></div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-2" id="confirm-title">Konfirmasi</h3>
                <p class="text-sm text-slate-600 mb-5" id="confirm-message">Apakah Anda yakin?</p>
                <div class="flex gap-3 justify-end">
                    <button onclick="closeConfirmModal()" class="btn-secondary">Batal</button>
                    <button id="confirm-yes" class="btn-primary bg-red-600 hover:bg-red-700">Ya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toast
        const toastContainer = document.getElementById('toast-container');
        const toastIcons = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        const toastStyles = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-700',
            error: 'bg-red-50 border-red-200 text-red-700',
            warning: 'bg-amber-50 border-amber-200 text-amber-700',
            info: 'bg-blue-50 border-blue-200 text-blue-700'
        };

        function showToast(message, type = 'success', duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium shadow-lg ${toastStyles[type]} transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `<span class="flex-shrink-0">${toastIcons[type]}</span><span>${message}</span>`;
            toastContainer.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            });
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        // Confirm Modal
        function openConfirmModal(message, type, callback) {
            const modal = document.getElementById('confirm-modal');
            document.getElementById('confirm-message').textContent = message;
            const yesBtn = document.getElementById('confirm-yes');
            yesBtn.className = type === 'danger'
                ? 'btn-primary bg-red-600 hover:bg-red-700'
                : 'btn-primary';
            yesBtn.onclick = () => { callback(); closeConfirmModal(); };
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirm-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('[data-confirm]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const form = btn.closest('form');
                openConfirmModal(
                    btn.dataset.confirm,
                    btn.dataset.confirmType || 'default',
                    () => form.submit()
                );
            });
        });

        @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if(session('error')) showToast('{{ session('error') }}', 'error'); @endif
        @if(session('warning')) showToast('{{ session('warning') }}', 'warning'); @endif
        @if(session('info')) showToast('{{ session('info') }}', 'info'); @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('{{ addslashes($error) }}', 'error', 6000);
            @endforeach
        @endif

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

        function loadStudentNotifications() {
            fetch('/notifications/recent')
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

                    if (data.broadcasts.length === 0) {
                        notifList.innerHTML = '<div class="p-4 text-center text-xs text-slate-400">Tidak ada notifikasi</div>';
                        return;
                    }

                    notifList.innerHTML = data.broadcasts.map(n => `
                        <div class="px-4 py-3 hover:bg-slate-50 transition border-b border-slate-50 ${n.is_read || n.is_dismissed ? 'opacity-50' : ''}">
                            <div class="flex items-start gap-2">
                                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 ${n.urgency === 'important' ? 'bg-red-500' : n.urgency === 'warning' ? 'bg-amber-500' : 'bg-blue-500'} ${n.is_read || n.is_dismissed ? 'opacity-30' : ''}"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">${n.title}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">${new Date(n.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'})}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <form method="POST" action="/notifications/${n.id}/read">
                                            @csrf
                                            <button type="submit" class="text-[11px] text-blue-600 hover:underline">Tandai baca</button>
                                        </form>
                                        ${!n.is_dismissed ? `<button onclick="dismissNotif(${n.id})" class="text-[11px] text-slate-400 hover:text-slate-600">Sembunyikan</button>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(() => {});
        }

        function dismissNotif(id) {
            fetch('/notifications/dismiss', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ broadcast_id: id })
            }).then(() => loadStudentNotifications());
        }

        loadStudentNotifications();
        setInterval(loadStudentNotifications, 60000);
    </script>
</body>
</html>
