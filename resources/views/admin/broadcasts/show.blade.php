@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.broadcasts') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900">Detail Broadcast</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $broadcast->title }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-5">
            <div class="card">
                <div class="flex items-center gap-3 mb-4">
                    @if($broadcast->urgency === 'important')
                        <span class="badge-danger">Important</span>
                    @elseif($broadcast->urgency === 'warning')
                        <span class="badge-warning">Warning</span>
                    @else
                        <span class="badge-secondary">Info</span>
                    @endif

                    @if($broadcast->target_type === 'all')
                        <span class="badge-secondary">Semua Mahasiswa</span>
                    @elseif($broadcast->target_type === 'department')
                        <span class="badge-info">Per Departemen</span>
                    @else
                        <span class="badge-info">Per Jadwal</span>
                    @endif
                </div>

                <h2 class="text-lg font-bold text-slate-900 mb-3">{{ $broadcast->title }}</h2>
                <div class="prose prose-sm max-w-none text-slate-600 whitespace-pre-wrap">{{ $broadcast->content }}</div>

                <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400 flex flex-wrap gap-4">
                    <span>Dikirim oleh: {{ $broadcast->admin->name ?? 'Admin' }}</span>
                    <span>{{ $broadcast->created_at->format('d/m/Y H:i') }}</span>
                    @if($broadcast->expires_at)
                        <span>Berlaku sampai: {{ $broadcast->expires_at->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
            </div>

            <!-- Delivery Stats -->
            <div class="card">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Statistik Pengiriman</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-2xl font-bold text-slate-900">{{ $total }}</div>
                        <div class="text-xs text-slate-500 mt-1">Total Dikirim</div>
                    </div>
                    <div class="text-center p-3 bg-emerald-50 rounded-lg">
                        <div class="text-2xl font-bold text-emerald-600">{{ $readCount }}</div>
                        <div class="text-xs text-slate-500 mt-1">Dibaca</div>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $broadcast->read_rate }}%</div>
                        <div class="text-xs text-slate-500 mt-1">Read Rate</div>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                        <span>Kemajuan baca</span>
                        <span>{{ $readCount }} / {{ $total }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $broadcast->read_rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-5">
            <!-- Meta -->
            <div class="card">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Informasi</h3>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Pengirim</dt>
                        <dd class="font-medium text-slate-700">{{ $broadcast->admin->name ?? 'Admin' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Target</dt>
                        <dd class="font-medium text-slate-700 capitalize">{{ str_replace('_', ' ', $broadcast->target_type) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Dikirim</dt>
                        <dd class="font-medium text-slate-700">{{ $broadcast->created_at->format('d/m/Y') }}</dd>
                    </div>
                    @if($broadcast->expires_at)
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Berakhir</dt>
                            <dd class="font-medium text-slate-700">{{ $broadcast->expires_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Delete -->
            <div class="card">
                <form method="POST" action="{{ route('admin.broadcasts.destroy', $broadcast) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn-danger text-sm" data-confirm="Hapus broadcast ini?" data-confirm-type="danger" data-confirm-ok="Hapus">
                        Hapus Broadcast
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
