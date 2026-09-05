@extends('layouts.admin')

@section('content')
<x-ui.page-header title="Tambah Nilai" backRoute="admin.scores.index" />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.scores.store') }}" class="max-w-2xl">
            @csrf

            <div class="mb-6">
                <label class="form-label">Pilih Mahasiswa</label>
                <select name="user_id" id="userSelect" required class="form-select">
                    <option value="">Pilih Mahasiswa</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" data-dept="{{ $u->department_id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <p id="deptHint" class="text-xs text-slate-400 mt-1.5 hidden"></p>
            </div>

            <div class="border-t border-slate-100 pt-5 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-700">Mata Kuliah &amp; Nilai</h3>
                    <button type="button" onclick="addRow()" id="addRowBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-xs font-semibold hover:bg-emerald-600 active:scale-[0.98] transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah MK
                    </button>
                </div>

                <div id="score-rows">
                    <div class="score-row mb-3 p-3 bg-slate-50 rounded-xl flex items-center gap-3">
                        <div class="flex-1">
                            <select name="scores[0][subject_id]" required class="form-select subject-select">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="w-24">
                            <input type="number" name="scores[0][score]" placeholder="Nilai" min="0" max="100" required class="form-input text-center">
                        </div>
                        <button type="button" onclick="removeRow(this)" class="remove-btn text-slate-300 hover:text-red-500 p-1 transition hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('admin.scores.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    let rowIndex = 1;
    const allSubjects = {!! $subjectsJson !!};
    const deptMap = {!! $deptMapJson !!};
    let currentDeptId = null;

    function getSubjectsForDept(deptId) {
        return allSubjects.filter(s => s.departments.includes(deptId));
    }

    function populateSubjectSelects(deptId) {
        const subjects = getSubjectsForDept(deptId);
        document.querySelectorAll('.subject-select').forEach(select => {
            const currentVal = select.value;
            select.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
            subjects.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name + ' (PG: ' + s.passing_grade + ')';
                select.appendChild(opt);
            });
            if (currentVal && subjects.some(s => s.id == currentVal)) {
                select.value = currentVal;
            }
        });
    }

    document.getElementById('userSelect').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const deptId = selected?.getAttribute('data-dept');
        if (deptId && deptMap[deptId]) {
            currentDeptId = parseInt(deptId);
            document.getElementById('deptHint').textContent = 'Departemen: ' + deptMap[deptId];
            document.getElementById('deptHint').classList.remove('hidden');
            populateSubjectSelects(currentDeptId);
        } else {
            currentDeptId = null;
            document.getElementById('deptHint').classList.add('hidden');
            document.querySelectorAll('.subject-select').forEach(select => {
                select.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
            });
        }
    });

    function addRow() {
        if (!currentDeptId) return;
        const container = document.getElementById('score-rows');
        const subjects = getSubjectsForDept(currentDeptId);
        let opts = '<option value="">Pilih Mata Kuliah</option>';
        subjects.forEach(s => {
            opts += `<option value="${s.id}">${s.name} (PG: ${s.passing_grade})</option>`;
        });
        const row = document.createElement('div');
        row.className = 'score-row mb-3 p-3 bg-slate-50 rounded-xl flex items-center gap-3';
        row.innerHTML = `
            <div class="flex-1">
                <select name="scores[${rowIndex}][subject_id]" required class="form-select subject-select">${opts}</select>
            </div>
            <div class="w-24">
                <input type="number" name="scores[${rowIndex}][score]" placeholder="Nilai" min="0" max="100" required class="form-input text-center">
            </div>
            <button type="button" onclick="removeRow(this)" class="remove-btn text-slate-300 hover:text-red-500 p-1 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(row);
        rowIndex++;
        updateRemoveButtons();
    }

    function removeRow(btn) { btn.closest('.score-row').remove(); updateRemoveButtons(); }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.score-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-btn');
            btn.classList.toggle('hidden', rows.length <= 1);
        });
    }

    if (document.getElementById('userSelect').value) {
        document.getElementById('userSelect').dispatchEvent(new Event('change'));
    }
</script>
@endsection
