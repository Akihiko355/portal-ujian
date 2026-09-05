@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Hasil Import" backRoute="admin.users.import" />

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="text-3xl font-bold text-emerald-600">{{ $created }}</p>
            <p class="text-sm text-slate-500 mt-1">User Baru</p>
        </div>
    </div>
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="text-3xl font-bold text-blue-600">{{ $updated }}</p>
            <p class="text-sm text-slate-500 mt-1">Diperbarui</p>
        </div>
    </div>
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="text-3xl font-bold text-amber-600">{{ $skipped }}</p>
            <p class="text-sm text-slate-500 mt-1">Dilewati</p>
        </div>
    </div>
</div>

@if($examSchedule)
<div class="card mb-5">
    <div class="card-body">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Jadwal Ujian</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
            <div><span class="text-xs text-slate-400">Mata Kuliah</span><p class="font-medium text-slate-800">{{ $examSchedule->subject->name }}</p></div>
            <div><span class="text-xs text-slate-400">Departemen</span><p class="font-medium text-slate-800">{{ $examSchedule->department->name }}</p></div>
            <div><span class="text-xs text-slate-400">Nomor Ujian</span><p class="font-medium text-slate-800">{{ $examSchedule->exam_number ?: '-' }}</p></div>
            <div><span class="text-xs text-slate-400">Briefing</span><p class="font-medium text-slate-800">{{ $examSchedule->briefing_datetime->format('d M Y H:i') }}</p></div>
            <div><span class="text-xs text-slate-400">Waktu Ujian</span><p class="font-medium text-slate-800">{{ $examSchedule->exam_start_datetime->format('d M H:i') }}-{{ $examSchedule->exam_end_datetime->format('H:i') }}</p></div>
            <div><span class="text-xs text-slate-400">File</span><p class="font-medium text-slate-800 truncate">{{ $fileName }}</p></div>
        </div>
    </div>
</div>
@endif

@if(!empty($finalizeErrors))
<div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
    <p class="font-semibold mb-2">Error:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($finalizeErrors as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(!empty($results))
<div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-700">Detail Hasil Import ({{ count($results) }} data)</h2>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">#</th>
                    <th>Nama</th>
                    <th class="hidden lg:table-cell">Email</th>
                    <th class="hidden md:table-cell">No. Ujian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $i => $row)
                    <tr>
                        <td class="text-slate-400 text-sm">{{ $i + 1 }}</td>
                        <td class="font-medium text-slate-900 text-sm">{{ $row['nama'] }}</td>
                        <td class="hidden lg:table-cell text-sm text-slate-600">{{ $row['email'] }}</td>
                        <td class="hidden md:table-cell font-mono text-sm text-slate-700">{{ $row['nomor_ujian'] ?: '-' }}</td>
                        <td>
                            @if($row['status'] === 'created')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">BARU</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">UPDATE</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card p-8 text-center">
    <x-ui.empty-state message="Tidak ada data yang berhasil diproses." />
</div>
@endif
@endsection
