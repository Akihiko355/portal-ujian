@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Jadwal Ujian">
    <x-slot name="action">
        <a href="{{ route('admin.exam-schedules.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </a>
    </x-slot>
</x-ui.page-header>

<div class="card overflow-hidden">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th class="hidden sm:table-cell">Dept</th>
                    <th>Mulai</th>
                    <th class="hidden md:table-cell">Selesai</th>
                    <th>No. Ujian</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>
                        <div class="font-medium text-slate-900">{{ $schedule->subject->name }}</div>
                        @if($schedule->exam_link)
                            <a href="{{ $schedule->exam_link }}" target="_blank" class="text-xs text-blue-500 hover:underline">Buka Link</a>
                        @endif
                    </td>
                    <td class="hidden sm:table-cell">
                        <span class="text-sm text-slate-600">{{ $schedule->department->name }}</span>
                    </td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $schedule->exam_start_datetime->format('d M Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $schedule->exam_start_datetime->format('H:i') }}</div>
                    </td>
                    <td class="hidden md:table-cell">
                        <div class="text-sm text-slate-600">{{ $schedule->exam_end_datetime->format('d M Y H:i') }}</div>
                    </td>
                    <td>
                        <span class="font-mono text-sm text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $schedule->exam_number ?: '-' }}</span>
                    </td>
                    <td>
                        <x-ui.badge :type="$schedule->is_published ? 'success' : 'neutral'" :label="$schedule->is_published ? 'Published' : 'Draft'" />
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.exam-schedules.edit', $schedule) }}" class="btn-ghost text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.exam-schedules.destroy', $schedule) }}">
                                @csrf @method('DELETE')
                                <button type="button" data-confirm="Yakin hapus jadwal ini?" data-confirm-type="danger" class="btn-ghost text-xs text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Belum ada jadwal ujian" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($schedules->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $schedules->links() }}</div>
    @endif
</div>
@endsection
