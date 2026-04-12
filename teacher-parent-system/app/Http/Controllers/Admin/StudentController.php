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
        $parents = User::where('role', 'parent')->orderBy('name')->get();
        return view('admin.students.create', compact('parents'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        $className = $data['grade'].'-'.$data['section'];
        $schoolClass = SchoolClass::firstOrCreate(['name' => $className]);

        $data['school_class_id'] = $schoolClass->id;
        unset($data['grade'], $data['section']);

        Student::create($data);

        return redirect()->route('admin.students.index')->with('success', 'Student added.');
    }

    public function edit(Student $student)
    {
        $parents = User::where('role', 'parent')->orderBy('name')->get();

        $selectedGrade = null;
        $selectedSection = null;

        if ($student->schoolClass && str_contains($student->schoolClass->name, '-')) {
            [$selectedGrade, $selectedSection] = explode('-', $student->schoolClass->name);
        }

        return view('admin.students.edit', compact('student', 'parents', 'selectedGrade', 'selectedSection'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        $className = $data['grade'].'-'.$data['section'];
        $schoolClass = SchoolClass::firstOrCreate(['name' => $className]);

        $data['school_class_id'] = $schoolClass->id;
        unset($data['grade'], $data['section']);

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted.');
    }
}