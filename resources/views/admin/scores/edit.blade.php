@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Edit Nilai" backRoute="admin.scores.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.scores.update', $score) }}" class="max-w-lg space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Mahasiswa</label>
                <input type="text" value="{{ $score->user->name }} ({{ $score->user->email }})" disabled class="form-input bg-slate-50 cursor-not-allowed">
            </div>
            <div>
                <label class="form-label">Mata Kuliah</label>
                <input type="text" value="{{ $score->subject->name }}" disabled class="form-input bg-slate-50 cursor-not-allowed">
            </div>
            <x-ui.input label="Nilai (0-100)" name="score" type="number" value="{{ old('score', $score->score) }}" :error="$errors->first('score')" required />
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update</button>
                <a href="{{ route('admin.scores.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
