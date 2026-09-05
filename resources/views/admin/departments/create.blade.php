@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Tambah Departemen" backRoute="admin.departments.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.departments.store') }}" class="max-w-lg space-y-5">
            @csrf
            <x-ui.input label="Nama" name="name" value="{{ old('name') }}" :error="$errors->first('name')" required />
            <x-ui.input label="Kode" name="code" value="{{ old('code') }}" :error="$errors->first('code')" required />
            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="3" class="form-input resize-none">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('admin.departments.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
