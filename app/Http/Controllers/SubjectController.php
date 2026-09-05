<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Department;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('departments')->withCount('scores')->latest()->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.subjects.create', compact('departments'));
    }

    public function store(StoreSubjectRequest $request)
    {
        $validated = $request->validated();
        $departmentIds = $validated['department_ids'];
        unset($validated['department_ids']);

        $subject = Subject::create($validated);
        $subject->departments()->sync($departmentIds);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        $departments = Department::all();
        $subject->load('departments');
        return view('admin.subjects.edit', compact('subject', 'departments'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $validated = $request->validated();
        $departmentIds = $validated['department_ids'];
        unset($validated['department_ids']);

        $subject->update($validated);
        $subject->departments()->sync($departmentIds);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata kuliah berhasil diupdate.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
