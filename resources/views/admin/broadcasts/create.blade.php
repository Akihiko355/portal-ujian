@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.broadcasts') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buat Broadcast</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kirim pesan ke mahasiswa</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.broadcasts.store') }}" class="max-w-2xl">
        @csrf

        <div class="card space-y-5">
            <!-- Title -->
            <div>
                <label class="form-label">Judul Pesan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input" placeholder="Contoh: Jadwal Ujian Semester Ganjil" maxlength="150" required>
                @error('title')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label class="form-label">Isi Pesan <span class="text-red-500">*</span></label>
                <textarea name="content" rows="5" class="form-input resize-none" placeholder="Tulis pesan yang akan diterima mahasiswa..." maxlength="2000" required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Urgency -->
            <div>
                <label class="form-label">Tingkat Urgensi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                        <input type="radio" name="urgency" value="info" {{ old('urgency', 'info') === 'info' ? 'checked' : '' }} class="accent-blue-600">
                        <div>
                            <span class="text-sm font-medium text-slate-700">Info</span>
                            <p class="text-[11px] text-slate-400">Pesan biasa</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50 has-[:checked]:ring-1 has-[:checked]:ring-amber-500">
                        <input type="radio" name="urgency" value="warning" {{ old('urgency') === 'warning' ? 'checked' : '' }} class="accent-amber-500">
                        <div>
                            <span class="text-sm font-medium text-slate-700">Warning</span>
                            <p class="text-[11px] text-slate-400">Perlu perhatian</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50 has-[:checked]:ring-1 has-[:checked]:ring-red-500">
                        <input type="radio" name="urgency" value="important" {{ old('urgency') === 'important' ? 'checked' : '' }} class="accent-red-600">
                        <div>
                            <span class="text-sm font-medium text-slate-700">Important</span>
                            <p class="text-[11px] text-slate-400">Penting & urgent</p>
                        </div>
                    </label>
                </div>
                @error('urgency')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target Type -->
            <div>
                <label class="form-label">Target Penerima <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                        <input type="radio" name="target_type" value="all" id="target_all" {{ old('target_type', 'all') === 'all' ? 'checked' : '' }} class="accent-blue-600">
                        <span class="text-sm font-medium text-slate-700">Semua</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                        <input type="radio" name="target_type" value="department" id="target_department" {{ old('target_type') === 'department' ? 'checked' : '' }} class="accent-blue-600">
                        <span class="text-sm font-medium text-slate-700">Per Dept</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                        <input type="radio" name="target_type" value="exam_schedule" id="target_exam" {{ old('target_type') === 'exam_schedule' ? 'checked' : '' }} class="accent-blue-600">
                        <span class="text-sm font-medium text-slate-700">Per Jadwal</span>
                    </label>
                </div>
                @error('target_type')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department Selection -->
            <div id="department_section" class="{{ old('target_type') === 'department' ? '' : 'hidden' }}">
                <label class="form-label">Pilih Departemen</label>
                <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3 bg-slate-50">
                    @foreach($departments as $dept)
                        <label class="flex items-center gap-2 p-2 rounded hover:bg-white transition cursor-pointer">
                            <input type="checkbox" name="target_ids[]" value="{{ $dept->id }}" class="accent-blue-600 rounded" {{ is_array(old('target_ids')) && in_array($dept->id, old('target_ids')) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700">{{ $dept->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Exam Schedule Selection -->
            <div id="exam_schedule_section" class="{{ old('target_type') === 'exam_schedule' ? '' : 'hidden' }}">
                <label class="form-label">Pilih Jadwal Ujian</label>
                <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3 bg-slate-50">
                    @foreach($examSchedules as $schedule)
                        <label class="flex items-center gap-2 p-2 rounded hover:bg-white transition cursor-pointer">
                            <input type="checkbox" name="target_ids[]" value="{{ $schedule->id }}" class="accent-blue-600 rounded" {{ is_array(old('target_ids')) && in_array($schedule->id, old('target_ids')) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700">{{ $schedule->subject->name ?? 'N/A' }} — {{ $schedule->department->name ?? 'N/A' }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Recipient Counter -->
            <div id="recipient_counter" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-sm font-medium text-blue-700">Jumlah penerima: <span id="recipient_count">0</span> mahasiswa</span>
                </div>
            </div>

            <!-- Expires -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Berlaku Sampai</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="form-input">
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika tidak ada batas</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="btn-primary">
                    Kirim Broadcast
                </button>
                <a href="{{ route('admin.broadcasts') }}" class="btn-ghost">Batal</a>
            </div>
        </div>
    </form>

    <script>
        const targetAll = document.getElementById('target_all');
        const targetDept = document.getElementById('target_department');
        const targetExam = document.getElementById('target_exam');
        const deptSection = document.getElementById('department_section');
        const examSection = document.getElementById('exam_schedule_section');
        const counter = document.getElementById('recipient_counter');
        const countSpan = document.getElementById('recipient_count');

        function updateVisibility() {
            if (targetDept.checked) {
                deptSection.classList.remove('hidden');
                examSection.classList.add('hidden');
                counter.classList.remove('hidden');
            } else if (targetExam.checked) {
                deptSection.classList.add('hidden');
                examSection.classList.remove('hidden');
                counter.classList.remove('hidden');
            } else {
                deptSection.classList.add('hidden');
                examSection.classList.add('hidden');
                counter.classList.add('hidden');
                countSpan.textContent = '0';
            }
        }

        function updateRecipientCount() {
            const targetType = document.querySelector('input[name="target_type"]:checked')?.value;
            const selectedIds = Array.from(document.querySelectorAll('input[name="target_ids[]"]:checked')).map(i => i.value);

            if (targetType === 'all') {
                countSpan.textContent = '—';
                return;
            }

            if (selectedIds.length === 0) {
                countSpan.textContent = '0';
                return;
            }

            fetch(`/admin/broadcasts/recipient-count?target_type=${targetType}&target_ids=${selectedIds.join(',')}`)
                .then(r => r.json())
                .then(d => countSpan.textContent = d.count.toLocaleString())
                .catch(() => countSpan.textContent = '?');
        }

        [targetAll, targetDept, targetExam].forEach(el => {
            el.addEventListener('change', updateVisibility);
        });

        document.querySelectorAll('input[name="target_ids[]"]').forEach(el => {
            el.addEventListener('change', updateRecipientCount);
        });

        updateVisibility();
    </script>
@endsection
