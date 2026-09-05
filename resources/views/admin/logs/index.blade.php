@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Log Aktivitas" />

<!-- Tabs -->
<div class="flex gap-1 mb-5 bg-slate-100 p-1 rounded-lg w-fit">
    <a href="{{ route('admin.logs', ['tab' => 'activity']) }}"
       class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $logType === 'activity' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        Aktivitas
    </a>
    <a href="{{ route('admin.logs', ['tab' => 'failed']) }}"
       class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $logType === 'failed' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        Login Gagal
    </a>
</div>

<!-- Filter -->
<div class="card mb-5">
    <div class="card-body py-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <input type="hidden" name="tab" value="{{ $logType }}">
            <div class="flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ $logType === 'activity' ? 'Cari label atau IP...' : 'Cari email atau IP...' }}"
                       class="form-input">
            </div>

            @if($logType === 'activity')
                <div class="min-w-[150px]">
                    <select name="action" class="form-select">
                        <option value="">Semua Aksi</option>
                        <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Membuat</option>
                        <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Mengubah</option>
                        <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Menghapus</option>
                        <option value="published" {{ request('action') === 'published' ? 'selected' : '' }}>Publikasi</option>
                        <option value="unpublished" {{ request('action') === 'unpublished' ? 'selected' : '' }}>Batal Publikasi</option>
                        <option value="imported" {{ request('action') === 'imported' ? 'selected' : '' }}>Import</option>
                        <option value="exported" {{ request('action') === 'exported' ? 'selected' : '' }}>Export</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <select name="model_type" class="form-select">
                        <option value="">Semua Model</option>
                        <option value="App\Models\User" {{ request('model_type') === 'App\Models\User' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="App\Models\Subject" {{ request('model_type') === 'App\Models\Subject' ? 'selected' : '' }}>Mata Kuliah</option>
                        <option value="App\Models\ExamSchedule" {{ request('model_type') === 'App\Models\ExamSchedule' ? 'selected' : '' }}>Jadwal Ujian</option>
                        <option value="App\Models\Score" {{ request('model_type') === 'App\Models\Score' ? 'selected' : '' }}>Nilai</option>
                    </select>
                </div>
            @else
                <div class="min-w-[160px]">
                    <select name="guard_type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="admin_failed" {{ request('guard_type') == 'admin_failed' ? 'selected' : '' }}>Admin Gagal</option>
                        <option value="web_failed" {{ request('guard_type') == 'web_failed' ? 'selected' : '' }}>User Gagal</option>
                    </select>
                </div>
            @endif

            <div class="min-w-[140px]">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>
            <div class="min-w-[140px]">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('admin.logs', ['tab' => $logType]) }}" class="btn-secondary">Reset</a>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card overflow-hidden">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    @if($logType === 'activity')
                        <th>Waktu</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                        <th>Objek</th>
                        <th class="hidden lg:table-cell">IP</th>
                    @else
                        <th>Waktu</th>
                        <th>Email</th>
                        <th class="hidden sm:table-cell">IP</th>
                        <th>Tipe</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    @if($logType === 'activity')
                        <tr>
                            <td class="text-slate-500 text-sm whitespace-nowrap">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                                <div class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-slate-900 text-sm">{{ $log->admin?->name ?? '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $log->admin?->email }}</div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg" title="{{ $log->action }}">{{ $log->icon }}</span>
                                    <span class="text-sm font-medium text-slate-700">{{ $log->action_label }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-slate-700">
                                    @if($log->model_type)
                                        @php
                                            $shortModel = class_basename($log->model_type);
                                            $label = match($shortModel) {
                                                'User' => '👤',
                                                'Subject' => '📚',
                                                'ExamSchedule' => '📅',
                                                'Score' => '📝',
                                                default => '📄',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1">
                                            {{ $label }}
                                            {{ Str::afterLast($log->model_type, '\\') }}
                                            @if($log->model_label)
                                                <span class="text-slate-500">— {{ Str::limit($log->model_label, 40) }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                                @if($log->changes)
                                    <details class="mt-1">
                                        <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-700">Lihat perubahan</summary>
                                        <div class="mt-1 p-2 bg-slate-50 rounded text-xs font-mono space-y-1">
                                            @foreach($log->changes as $field => $change)
                                                <div>
                                                    <span class="text-slate-500">{{ $field }}:</span>
                                                    <span class="text-red-500 line-through">{{ $change['old'] ?? 'null' }}</span>
                                                    →
                                                    <span class="text-emerald-600">{{ $change['new'] ?? 'null' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </td>
                            <td class="hidden lg:table-cell font-mono text-xs text-slate-400">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-slate-500 text-sm whitespace-nowrap">
                                {{ $log->attempted_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="font-medium text-slate-900 text-sm">{{ $log->email }}</td>
                            <td class="hidden sm:table-cell font-mono text-xs text-slate-500">{{ $log->ip_address }}</td>
                            <td>
                                <x-ui.badge :type="$log->guard_type == 'admin_failed' || $log->guard_type == 'web_failed' ? 'danger' : 'info'" :label="$log->guard_type" />
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="text-center"><x-ui.empty-state message="Belum ada log" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
