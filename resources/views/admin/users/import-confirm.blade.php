@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Konfirmasi Import" backRoute="admin.users.import" />

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

@if(!empty($importErrors))
<div class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-sm">
    <p class="font-semibold mb-2">Peringatan:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($importErrors as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-700">Data Mahasiswa ({{ count($rows) }} orang)</h2>
        @if(!empty($rows))
        <form method="POST" action="{{ route('admin.users.import.finalize') }}">
            @csrf
            <button type="button" data-confirm="Konfirmasi import {{ count($rows) }} mahasiswa?" data-confirm-type="success" class="btn-primary bg-emerald-600 hover:bg-emerald-700">Konfirmasi &amp; Sinkronkan</button>
        </form>
        @endif
    </div>

    @if(empty($rows))
        <div class="p-8 text-center"><x-ui.empty-state message="Tidak ada data mahasiswa yang valid untuk diimport." /></div>
    @else
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">#</th>
                    <th>Nama</th>
                    <th class="hidden lg:table-cell">Email</th>
                    <th class="hidden md:table-cell">Telepon</th>
                    <th class="hidden sm:table-cell">Dept</th>
                    <th>Password</th>
                    <th class="hidden lg:table-cell">No. Ujian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                    @php $existing = \App\Models\User::where('email', $row['email'])->first(); @endphp
                    <tr>
                        <td class="text-slate-400 text-sm">{{ $i + 1 }}</td>
                        <td class="font-medium text-slate-900 text-sm">{{ $row['nama'] }}</td>
                        <td class="hidden lg:table-cell text-sm text-slate-600">{{ $row['email'] }}</td>
                        <td class="hidden md:table-cell text-sm text-slate-600">{{ $row['telepon'] }}</td>
                        <td class="hidden sm:table-cell text-sm text-slate-600">{{ $row['departemen'] ?: '-' }}</td>
                        <td class="font-mono text-xs text-slate-500">{{ $row['password'] ? str_repeat('*', min(strlen($row['password']), 8)) : '-' }}</td>
                        <td class="hidden lg:table-cell font-mono text-sm text-slate-700">{{ $row['nomor_ujian'] ?: '-' }}</td>
                        <td>
                            @if($existing)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">UPDATE</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">BARU</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
