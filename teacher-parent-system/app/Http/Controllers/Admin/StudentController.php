<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['schoolClass', 'parent'])->orderBy('name')->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $parents = User::where('role', 'parent')->orderBy('name')->get();
        return view('admin.students.create', compact('classes', 'parents'));
    }

    public function store(StoreStudentRequest $request)
    {
        Student::create($request->validated());
        return redirect()->route('admin.students.index')->with('success', 'Student added.');
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $parents = User::where('role', 'parent')->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes', 'parents'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return redirect()->route('admin.students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted.');
    }
}