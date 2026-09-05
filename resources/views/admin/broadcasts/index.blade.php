@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Broadcast</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kirim pesan ke mahasiswa</p>
        </div>
        <a href="{{ route('admin.broadcasts.create') }}" class="btn-primary text-sm inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Broadcast
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label-sm">Urgency</label>
                <select name="urgency" class="form-input py-1.5 text-sm">
                    <option value="">Semua</option>
                    <option value="info" {{ request('urgency') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ request('urgency') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="important" {{ request('urgency') === 'important' ? 'selected' : '' }}>Important</option>
                </select>
            </div>
            <div>
                <label class="form-label-sm">Target</label>
                <select name="target_type" class="form-input py-1.5 text-sm">
                    <option value="">Semua</option>
                    <option value="all" {{ request('target_type') === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="department" {{ request('target_type') === 'department' ? 'selected' : '' }}>Per Departemen</option>
                    <option value="exam_schedule" {{ request('target_type') === 'exam_schedule' ? 'selected' : '' }}>Per Jadwal</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary text-sm">Filter</button>
            <a href="{{ route('admin.broadcasts') }}" class="btn-ghost text-sm">Reset</a>
        </form>
    </div>

    <!-- Broadcast List -->
    <div class="card">
        @if($broadcasts->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="text-sm font-medium">Belum ada broadcast</p>
                <p class="text-xs mt-1">Buat broadcast pertama untuk mengirim pesan ke mahasiswa</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Judul</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Target</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Urgency</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Stats</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Tanggal</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($broadcasts as $broadcast)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.broadcasts.show', $broadcast) }}" class="font-medium text-slate-900 hover:text-blue-600 transition">
                                        {{ $broadcast->title }}
                                    </a>
                                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ Str::limit($broadcast->content, 60) }}</p>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    @if($broadcast->target_type === 'all')
                                        <span class="badge-secondary text-[10px]">Semua</span>
                                    @elseif($broadcast->target_type === 'department')
                                        <span class="badge-info text-[10px]">Dept</span>
                                    @else
                                        <span class="badge-info text-[10px]">Jadwal</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    @if($broadcast->urgency === 'important')
                                        <span class="badge-danger text-[10px]">Important</span>
                                    @elseif($broadcast->urgency === 'warning')
                                        <span class="badge-warning text-[10px]">Warning</span>
                                    @else
                                        <span class="badge-secondary text-[10px]">Info</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center hidden lg:table-cell">
                                    <span class="text-xs text-slate-500">{{ $broadcast->delivery_count }} terkirim</span>
                                    <span class="text-xs font-medium text-emerald-600 ml-1">{{ $broadcast->read_rate }}%</span>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <span class="text-xs text-slate-400">{{ $broadcast->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.broadcasts.show', $broadcast) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.broadcasts.destroy', $broadcast) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus" data-confirm="Hapus broadcast ini?" data-confirm-type="danger" data-confirm-ok="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($broadcasts->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $broadcasts->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
