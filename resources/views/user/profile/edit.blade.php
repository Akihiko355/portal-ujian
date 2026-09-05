@extends('layouts.user')

@section('content')
<x-ui.page-header title="Edit Profil" />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-body">
            <h2 class="text-base font-semibold text-slate-800 mb-5">Informasi Profil</h2>
            <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-5">
                @csrf @method('PUT')
                <x-ui.input label="Nama" name="name" value="{{ old('name', $user->name) }}" required />
                <x-ui.input label="Telepon" name="phone" value="{{ old('phone', $user->phone) }}" required />
                <x-ui.select label="Departemen" name="department_id" :options="$departments->pluck('name', 'id')->prepend('Pilih Departemen', '')" :selected="old('department_id', $user->department_id)" />
                <x-ui.input label="Alamat Institusi" name="institution_address" value="{{ old('institution_address', $user->institution_address) }}" />
                <button type="submit" class="btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="text-base font-semibold text-slate-800 mb-5">Ubah Password</h2>
            <form method="POST" action="{{ route('user.profile.password') }}" class="space-y-5">
                @csrf @method('PUT')
                <x-ui.input label="Password Saat Ini" name="current_password" type="password" required />
                <x-ui.input label="Password Baru" name="password" type="password" required />
                <x-ui.input label="Konfirmasi Password Baru" name="password_confirmation" type="password" required />
                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
