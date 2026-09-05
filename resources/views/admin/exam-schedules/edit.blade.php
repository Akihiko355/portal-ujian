@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Edit Jadwal Ujian" backRoute="admin.exam-schedules.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.exam-schedules.update', $examSchedule) }}" class="max-w-lg space-y-5">
            @csrf @method('PUT')
            <x-ui.select label="Mata Kuliah" name="subject_id" :options="$subjects->pluck('name', 'id')->prepend('Pilih Mata Kuliah', '')" :selected="old('subject_id', $examSchedule->subject_id)" :error="$errors->first('subject_id')" required />
            <x-ui.select label="Departemen" name="department_id" :options="$departments->pluck('name', 'id')->prepend('Pilih Departemen', '')" :selected="old('department_id', $examSchedule->department_id)" :error="$errors->first('department_id')" required />
            <x-ui.input label="Tanggal Briefing" name="briefing_datetime" type="datetime-local" value="{{ old('briefing_datetime', $examSchedule->briefing_datetime->format('Y-m-d\TH:i')) }}" :error="$errors->first('briefing_datetime')" required />
            <x-ui.input label="Mulai Ujian" name="exam_start_datetime" type="datetime-local" value="{{ old('exam_start_datetime', $examSchedule->exam_start_datetime->format('Y-m-d\TH:i')) }}" :error="$errors->first('exam_start_datetime')" required />
            <x-ui.input label="Selesai Ujian" name="exam_end_datetime" type="datetime-local" value="{{ old('exam_end_datetime', $examSchedule->exam_end_datetime->format('Y-m-d\TH:i')) }}" :error="$errors->first('exam_end_datetime')" required />
            <x-ui.input label="Link Ujian" name="exam_link" type="url" value="{{ old('exam_link', $examSchedule->exam_link) }}" :error="$errors->first('exam_link')" />
            <x-ui.input label="Password Ujian" name="exam_password" value="{{ old('exam_password', $examSchedule->exam_password) }}" :error="$errors->first('exam_password')" required />
            <x-ui.input label="Nomor Ujian" name="exam_number" value="{{ old('exam_number', $examSchedule->exam_number) }}" :error="$errors->first('exam_number')" required />

            <div class="border-t border-slate-100 pt-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Pengaturan Visibilitas</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.select label="Tampilkan Link" name="link_reveal" :options="['' => 'Pilih...', 'on_briefing' => 'Saat Briefing', 'on_start' => 'Saat Ujian', 'always' => 'Selalu']" :selected="old('link_reveal', $examSchedule->link_reveal)" :error="$errors->first('link_reveal')" required />
                    <x-ui.select label="Tampilkan Password" name="password_reveal" :options="['' => 'Pilih...', 'on_briefing' => 'Saat Briefing', '5_min_before' => '5 Menit Sebelum', 'on_start' => 'Saat Ujian', 'always' => 'Selalu']" :selected="old('password_reveal', $examSchedule->password_reveal)" :error="$errors->first('password_reveal')" required />
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $examSchedule->is_published) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                <label for="is_published" class="text-sm text-slate-700">Publish</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update</button>
                <a href="{{ route('admin.exam-schedules.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
