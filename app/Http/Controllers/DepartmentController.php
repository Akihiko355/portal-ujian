<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users', 'subjects')->latest()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();
        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $validated = $request->validated();
        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil diupdate.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
