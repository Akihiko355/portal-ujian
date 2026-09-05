@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Import / Export" backRoute="admin.dashboard" />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Export -->
    <div class="card">
        <div class="card-body">
            <h2 class="text-base font-semibold text-slate-800 mb-1">Download Excel</h2>
            <p class="text-sm text-slate-500 mb-5">Pilih jadwal ujian dan departemen untuk mengunduh data mahasiswa.</p>
            <form method="POST" action="{{ route('admin.users.export') }}" class="space-y-4">
                @csrf
                <x-ui.select label="Jadwal Ujian" name="exam_schedule_id" :options="$examSchedules->pluck('name', 'id')->prepend('Pilih Jadwal Ujian', '')" :selected="old('exam_schedule_id')" required />
                <x-ui.select label="Departemen" name="department_id" :options="$departments->pluck('name', 'id')->prepend('Pilih Departemen', '')" :selected="old('department_id')" required />
                <button type="submit" class="btn-primary bg-emerald-600 hover:bg-emerald-700">Download Excel</button>
            </form>
        </div>
    </div>

    <!-- Import -->
    <div class="card">
        <div class="card-body">
            <h2 class="text-base font-semibold text-slate-800 mb-1">Upload Excel</h2>
            <p class="text-sm text-slate-500 mb-5">Upload file Excel untuk preview sebelum sinkronisasi.</p>
            <form method="POST" action="{{ route('admin.users.import.preview') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <x-ui.select label="Jadwal Ujian" name="exam_schedule_id" :options="$examSchedules->pluck('name', 'id')->prepend('Pilih Jadwal Ujian', '')" :selected="old('exam_schedule_id')" required />
                <div>
                    <label class="form-label">File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        class="form-input text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>
                <button type="submit" class="btn-primary">Preview Data</button>
            </form>
            <div class="mt-5 p-4 bg-slate-50 rounded-xl">
                <p class="text-xs font-semibold text-slate-600 mb-2">Format kolom Excel:</p>
                <p class="text-xs text-slate-500 leading-relaxed">Nama | Email | Telepon | Departemen | Password | Nomor Ujian</p>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">- Email adalah kunci sinkronisasi</p>
                <p class="text-xs text-slate-500 leading-relaxed">- Password &amp; Nomor Ujian: kosongkan jika tidak diubah</p>
                <p class="text-xs text-slate-500 leading-relaxed">- User baru wajib isi Password</p>
            </div>
        </div>
    </div>
</div>
@endsection
