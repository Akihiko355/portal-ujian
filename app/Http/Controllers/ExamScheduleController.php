<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamScheduleRequest;
use App\Http\Requests\UpdateExamScheduleRequest;
use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\Subject;

class ExamScheduleController extends Controller
{
    public function index()
    {
        $schedules = ExamSchedule::with(['subject', 'department'])->latest()->paginate(10);
        return view('admin.exam-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $departments = Department::all();
        return view('admin.exam-schedules.create', compact('subjects', 'departments'));
    }

    public function store(StoreExamScheduleRequest $request)
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        ExamSchedule::create($validated);

        return redirect()->route('admin.exam-schedules.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function edit(ExamSchedule $examSchedule)
    {
        $subjects = Subject::all();
        $departments = Department::all();
        return view('admin.exam-schedules.edit', compact('examSchedule', 'subjects', 'departments'));
    }

    public function update(UpdateExamScheduleRequest $request, ExamSchedule $examSchedule)
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $examSchedule->update($validated);

        return redirect()->route('admin.exam-schedules.index')->with('success', 'Jadwal ujian berhasil diupdate.');
    }

    public function destroy(ExamSchedule $examSchedule)
    {
        $examSchedule->delete();
        return redirect()->route('admin.exam-schedules.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}
