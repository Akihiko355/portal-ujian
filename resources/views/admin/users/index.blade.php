@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Mahasiswa">
    <x-slot name="action">
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </a>
    </x-slot>
</x-ui.page-header>

<!-- Filter Bar -->
<div class="card mb-5">
    <div class="card-body py-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, email, telepon..."
                       class="form-input">
            </div>
            <div class="min-w-[160px]">
                <select name="department_id" class="form-select">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Cari</button>
            @if(request()->has('search') || request()->has('department_id'))
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
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
                    <th>Nama</th>
                    <th>Email</th>
                    <th class="hidden sm:table-cell">Telepon</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div class="font-medium text-slate-900">{{ $u->name }}</div>
                        @if($u->nomor_ujian)
                            <div class="text-xs text-slate-400 mt-0.5">No. Ujian: {{ $u->nomor_ujian }}</div>
                        @endif
                    </td>
                    <td>{{ $u->email }}</td>
                    <td class="hidden sm:table-cell">{{ $u->phone }}</td>
                    <td>{{ $u->department?->name ?? '-' }}</td>
                    <td>
                        <x-ui.badge :type="$u->is_active ? 'success' : 'danger'" :label="$u->is_active ? 'Aktif' : 'Nonaktif'" />
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn-ghost text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}">
                                @csrf @method('DELETE')
                                <button type="button" data-confirm="Yakin hapus {{ $u->name }}?" data-confirm-type="danger" class="btn-ghost text-xs text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-ui.empty-state message="Belum ada mahasiswa" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
        <p class="text-xs text-slate-500">Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }}</p>
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
