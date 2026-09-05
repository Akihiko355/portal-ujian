<?php

namespace App\Http\Controllers;

use App\Exports\MahasiswaExport;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\MahasiswaImport;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "{$search}%")
                  ->orWhere('email', $search)
                  ->orWhere('phone', 'like', "{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->latest()->paginate(10);
        $departments = Department::all();

        return view('admin.users.index', compact('users', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.users.create', compact('departments'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function importForm()
    {
        if (session('import_rows') || session('import_errors')) {
            return redirect()->route('admin.users.import.confirm');
        }

        $departments = Department::withCount('users')->get();
        $subjects = Subject::all();
        $examSchedules = ExamSchedule::with(['subject', 'department'])->latest()->get();
        return view('admin.users.import', compact('departments', 'subjects', 'examSchedules'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
        ]);

        $dept = Department::findOrFail($request->department_id);

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => 'exported',
            'model_label' => "Exported students for {$dept->name}",
            'metadata' => ['department_id' => $dept->id, 'exam_schedule_id' => $request->exam_schedule_id],
            'created_at' => now(),
        ]);

        return Excel::download(
            new MahasiswaExport($dept->id, $request->exam_schedule_id),
            'mahasiswa-' . $dept->code . '.xlsx'
        );
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
        ]);

        $import = new MahasiswaImport();
        Excel::import($import, $request->file('file'));

        $rows = $import->getRows();
        $importErrors = $import->errors;

        session([
            'import_rows' => $rows,
            'import_errors' => $importErrors,
            'import_exam_schedule_id' => $request->exam_schedule_id,
            'import_file_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return redirect()->route('admin.users.import.confirm');
    }

    public function importConfirm()
    {
        $rows = session('import_rows', []);
        $importErrors = session('import_errors', []);
        $examScheduleId = session('import_exam_schedule_id');
        $fileName = session('import_file_name', '');

        if (empty($rows) && empty($importErrors)) {
            return redirect()->route('admin.users.import')->with('error', 'Tidak ada data untuk dikonfirmasi.');
        }

        $examSchedule = ExamSchedule::with(['subject', 'department'])->find($examScheduleId);

        return view('admin.users.import-confirm', compact('rows', 'importErrors', 'examSchedule', 'fileName'));
    }

    public function importFinalize()
    {
        $rows = session('import_rows', []);
        $examScheduleId = session('import_exam_schedule_id');
        $fileName = session('import_file_name', '');

        if (empty($rows)) {
            return redirect()->route('admin.users.import')->with('error', 'Tidak ada data untuk diproses.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $finalizeErrors = [];
        $results = [];

        foreach ($rows as $row) {
            $email = strtolower(trim($row['email'] ?? ''));
            if (empty($email)) {
                $skipped++;
                continue;
            }

            $departmentId = $this->resolveDept($row['departemen'] ?? null);
            $plainPassword = trim($row['password'] ?? '');
            $nomorUjian = trim($row['nomor_ujian'] ?? '');

            $existing = User::where('email', $email)->first();

            if ($existing) {
                $update = [
                    'name' => $row['nama'] ?? $existing->name,
                    'phone' => $row['telepon'] ?? $existing->phone,
                    'nomor_ujian' => $nomorUjian !== '' ? $nomorUjian : $existing->nomor_ujian,
                ];
                if ($departmentId) {
                    $update['department_id'] = $departmentId;
                }
                if ($plainPassword !== '') {
                    $update['password'] = Hash::make($plainPassword);
                }
                $existing->update($update);
                $updated++;

                $results[] = [
                    'nama' => $existing->name,
                    'email' => $existing->email,
                    'nomor_ujian' => $nomorUjian ?: $existing->nomor_ujian,
                    'status' => 'updated',
                ];
            } else {
                if ($plainPassword === '') {
                    $finalizeErrors[] = "{$email}: password wajib diisi untuk user baru";
                    $skipped++;
                    continue;
                }

                $user = User::create([
                    'name' => $row['nama'] ?? '',
                    'email' => $email,
                    'phone' => $row['telepon'] ?? '',
                    'password' => Hash::make($plainPassword),
                    'department_id' => $departmentId,
                    'nomor_ujian' => $nomorUjian ?: null,
                    'is_active' => true,
                ]);
                $created++;

                $results[] = [
                    'nama' => $user->name,
                    'email' => $user->email,
                    'nomor_ujian' => $nomorUjian,
                    'status' => 'created',
                ];
            }
        }

        $examSchedule = ExamSchedule::with(['subject', 'department'])->find($examScheduleId);

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => 'imported',
            'model_label' => "Imported {$created} students, {$updated} updated",
            'metadata' => [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'file' => $fileName,
                'exam_schedule_id' => $examScheduleId,
            ],
            'created_at' => now(),
        ]);

        session()->forget(['import_rows', 'import_errors', 'import_exam_schedule_id', 'import_file_name']);

        return view('admin.users.import-result', compact(
            'results', 'created', 'updated', 'skipped', 'finalizeErrors', 'examSchedule', 'fileName'
        ));
    }

    protected function resolveDept(?string $name): ?int
    {
        if (empty($name)) return null;
        $dept = Department::where('name', 'like', $name)->first();
        return $dept?->id;
    }
}
