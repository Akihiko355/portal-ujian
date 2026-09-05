@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Departemen">
    <x-slot name="action">
        <a href="{{ route('admin.departments.create') }}" class="btn-primary">
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
                    <th>Mahasiswa</th>
                    <th>Mata Kuliah</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td>
                        <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $dept->code }}</span>
                    </td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $dept->name }}</div>
                        @if($dept->description)
                            <div class="text-xs text-slate-400 mt-0.5">{{ $dept->description }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ $dept->users_count }}</span>
                    </td>
                    <td>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ $dept->subjects_count }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.departments.edit', $dept) }}" class="btn-ghost text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}">
                                @csrf @method('DELETE')
                                <button type="button" data-confirm="Yakin hapus {{ $dept->name }}?" data-confirm-type="danger" class="btn-ghost text-xs text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-ui.empty-state message="Belum ada departemen" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($departments->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $departments->links() }}
    </div>
    @endif
</div>
@endsection
