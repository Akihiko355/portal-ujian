@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Edit Mata Kuliah" backRoute="admin.subjects.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="max-w-lg space-y-5">
            @csrf @method('PUT')
            <x-ui.input label="Nama" name="name" value="{{ old('name', $subject->name) }}" :error="$errors->first('name')" required />
            <x-ui.input label="Kode" name="code" value="{{ old('code', $subject->code) }}" :error="$errors->first('code')" required />
            <x-ui.input label="SKS" name="credits" type="number" value="{{ old('credits', $subject->credits) }}" :error="$errors->first('credits')" required />
            <x-ui.input label="Passing Grade" name="passing_grade" type="number" value="{{ old('passing_grade', $subject->passing_grade) }}" :error="$errors->first('passing_grade')" required />
            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="3" class="form-input resize-none">{{ old('description', $subject->description) }}</textarea>
            </div>
            <div>
                <label class="form-label">Departemen <span class="text-red-500">*</span></label>
                @php $selectedDepts = old('department_ids', $subject->departments->pluck('id')->toArray()); @endphp
                <div class="space-y-2">
                    @foreach($departments as $dept)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}"
                                {{ in_array($dept->id, $selectedDepts) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                            <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ $dept->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('department_ids')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
