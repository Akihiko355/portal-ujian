@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Edit User" backRoute="admin.users.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-lg space-y-5">
            @csrf @method('PUT')
            <x-ui.input label="Nama" name="name" value="{{ old('name', $user->name) }}" :error="$errors->first('name')" required />
            <x-ui.input label="Email" name="email" type="email" value="{{ old('email', $user->email) }}" :error="$errors->first('email')" required />
            <x-ui.input label="Nomor Ujian" name="nomor_ujian" value="{{ old('nomor_ujian', $user->nomor_ujian) }}" />
            <x-ui.input label="Telepon" name="phone" value="{{ old('phone', $user->phone) }}" :error="$errors->first('phone')" required />
            <x-ui.input label="Password (kosongkan jika tidak diubah)" name="password" type="password" :error="$errors->first('password')" />
            <x-ui.input label="Konfirmasi Password" name="password_confirmation" type="password" :error="$errors->first('password_confirmation')" />
            <x-ui.select label="Departemen" name="department_id" :options="$departments->pluck('name', 'id')->prepend('Pilih Departemen', '')" :selected="old('department_id', $user->department_id)" />
            <x-ui.input label="Alamat Institusi" name="institution_address" value="{{ old('institution_address', $user->institution_address) }}" />
            <div class="flex items-center gap-2.5">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ $user->is_active ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                <label for="is_active" class="text-sm text-slate-700">User Aktif</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
