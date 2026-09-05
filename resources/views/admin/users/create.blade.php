@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Tambah User" backRoute="admin.users.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}" class="max-w-lg space-y-5">
            @csrf
            <x-ui.input label="Nama" name="name" value="{{ old('name') }}" :error="$errors->first('name')" required />
            <x-ui.input label="Email" name="email" type="email" value="{{ old('email') }}" :error="$errors->first('email')" required />
            <x-ui.input label="Nomor Ujian" name="nomor_ujian" value="{{ old('nomor_ujian') }}" />
            <x-ui.input label="Telepon" name="phone" value="{{ old('phone') }}" :error="$errors->first('phone')" required />
            <x-ui.input label="Password" name="password" type="password" :error="$errors->first('password')" required />
            <x-ui.input label="Konfirmasi Password" name="password_confirmation" type="password" required />
            <x-ui.select label="Departemen" name="department_id" :options="$departments->pluck('name', 'id')->prepend('Pilih Departemen', '')" :selected="old('department_id')" />
            <x-ui.input label="Alamat Institusi" name="institution_address" value="{{ old('institution_address') }}" />
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
