@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Mata Kuliah">
    <x-slot name="action">
        <a href="{{ route('admin.subjects.create') }}" class="btn-primary">
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
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>SKS</th>
                    <th>Nilai Min</th>
                    <th>Departemen</th>
                    <th class="hidden sm:table-cell">Jumlah Nilai</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr>
                    <td>
                        <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $subject->code }}</span>
                    </td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $subject->name }}</div>
                    </td>
                    <td>{{ $subject->credits }}</td>
                    <td>
                        <span class="font-semibold text-amber-600">{{ $subject->passing_grade }}</span>
                    </td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            @forelse($subject->departments as $dept)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">{{ $dept->name }}</span>
                            @empty
                                <span class="text-xs text-slate-400">-</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="hidden sm:table-cell">{{ $subject->scores_count }}</td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn-ghost text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}">
                                @csrf @method('DELETE')
                                <button type="button" data-confirm="Yakin hapus {{ $subject->name }}?" data-confirm-type="danger" class="btn-ghost text-xs text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Belum ada mata kuliah" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subjects->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $subjects->links() }}</div>
    @endif
</div>
@endsection
