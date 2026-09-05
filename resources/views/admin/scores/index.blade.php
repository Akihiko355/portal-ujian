@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Nilai">
    <x-slot name="action">
        <a href="{{ route('admin.scores.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </a>
    </x-slot>
</x-ui.page-header>

<!-- Filter -->
<div class="card mb-5">
    <div class="card-body py-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="form-input">
            </div>
            <div class="min-w-[160px]">
                <select name="subject_id" class="form-select">
                    <option value="">Semua MK</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <select name="is_published" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Published</option>
                    <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Cari</button>
            @if(request()->has('search') || request()->has('subject_id') || request()->has('is_published'))
                <a href="{{ route('admin.scores.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card overflow-hidden">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th class="hidden sm:table-cell">Mata Kuliah</th>
                    <th>Nilai</th>
                    <th>Status</th>
                    <th class="hidden md:table-cell">Input</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scores as $score)
                <tr>
                    <td>
                        <div class="font-medium text-slate-900">{{ $score->user->name }}</div>
                        <div class="text-xs text-slate-400">{{ $score->user->email }}</div>
                    </td>
                    <td class="hidden sm:table-cell">{{ $score->subject->name }}</td>
                    <td>
                        <span class="text-xl font-bold text-slate-900">{{ $score->score }}</span>
                    </td>
                    <td>
                        <x-ui.badge :type="$score->is_published ? 'success' : 'warning'" :label="$score->is_published ? 'Published' : 'Pending'" />
                    </td>
                    <td class="hidden md:table-cell text-slate-500 text-xs">{{ $score->inputByAdmin?->name ?? '-' }}</td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$score->is_published)
                                <form method="POST" action="{{ route('admin.scores.publish', $score) }}">
                                    @csrf
                                    <button type="submit" class="btn-ghost text-xs text-emerald-600 hover:bg-emerald-50">Publish</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.scores.unpublish', $score) }}">
                                    @csrf
                                    <button type="submit" class="btn-ghost text-xs text-amber-600 hover:bg-amber-50">Unpublish</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.scores.edit', $score) }}" class="btn-ghost text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.scores.destroy', $score) }}">
                                @csrf @method('DELETE')
                                <button type="button" data-confirm="Yakin hapus?" data-confirm-type="danger" class="btn-ghost text-xs text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Belum ada nilai" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($scores->hasPages())
    <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
        <p class="text-xs text-slate-500">Menampilkan {{ $scores->firstItem() }}-{{ $scores->lastItem() }} dari {{ $scores->total() }}</p>
        {{ $scores->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
