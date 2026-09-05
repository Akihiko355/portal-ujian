@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Log sistem dan aktivitas penting</p>
        </div>
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn-secondary text-xs">Tandai Semua Dibaca</button>
        </form>
    </div>

    <!-- Filters -->
    <div class="card mb-5 p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label-sm">Status</label>
                <select name="read" class="form-input py-1.5 text-sm">
                    <option value="">Semua</option>
                    <option value="0" {{ request('read') === '0' ? 'selected' : '' }}>Belum Dibaca</option>
                    <option value="1" {{ request('read') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
                </select>
            </div>
            <div>
                <label class="form-label-sm">Priority</label>
                <select name="priority" class="form-input py-1.5 text-sm">
                    <option value="">Semua</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div>
                <label class="form-label-sm">Tipe</label>
                <select name="type" class="form-input py-1.5 text-sm">
                    <option value="">Semua</option>
                    <option value="score_added" {{ request('type') === 'score_added' ? 'selected' : '' }}>Nilai Ditambahkan</option>
                    <option value="score_published" {{ request('type') === 'score_published' ? 'selected' : '' }}>Nilai Dipublikasi</option>
                    <option value="registration" {{ request('type') === 'registration' ? 'selected' : '' }}>Pendaftaran</option>
                    <option value="import" {{ request('type') === 'import' ? 'selected' : '' }}>Import</option>
                    <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Sistem</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary text-sm">Filter</button>
            <a href="{{ route('admin.notifications') }}" class="btn-ghost text-sm">Reset</a>
        </form>
    </div>

    <!-- Notification List -->
    <div class="card">
        @if($notifications->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <p class="text-sm font-medium">Belum ada notifikasi</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($notifications as $notification)
                    <div class="p-4 flex items-start gap-3 hover:bg-slate-50 transition {{ !$notification->read_at ? 'bg-blue-50/40' : '' }}">
                        <!-- Icon -->
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                            @if($notification->priority === 'urgent' || $notification->priority === 'high') bg-red-100 text-red-600
                            @elseif($notification->priority === 'medium') bg-amber-100 text-amber-600
                            @else bg-slate-100 text-slate-500
                            @endif">
                            @if($notification->type === 'score_added' || $notification->type === 'score_published')
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            @elseif($notification->type === 'registration')
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            @elseif($notification->type === 'import')
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            @else
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 {{ !$notification->read_at ? 'font-bold' : '' }}">{{ $notification->title }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $notification->message }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    @if($notification->priority === 'urgent')
                                        <span class="badge-danger ">Urgent</span>
                                    @elseif($notification->priority === 'high')
                                        <span class="badge-danger ">High</span>
                                    @elseif($notification->priority === 'medium')
                                        <span class="badge-warning ">Medium</span>
                                    @else
                                        <span class="badge-secondary ">Low</span>
                                    @endif
                                    @if(!$notification->read_at)
                                        <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                                @if($notification->link)
                                    <a href="{{ $notification->link }}" class="text-[11px] text-blue-600 hover:underline">Lihat</a>
                                @endif
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[11px] text-slate-400 hover:text-red-500 transition" onclick="return confirm('Hapus notifikasi ini?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $notifications->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
