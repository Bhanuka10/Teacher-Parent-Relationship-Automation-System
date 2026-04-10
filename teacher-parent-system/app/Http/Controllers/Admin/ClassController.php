<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassRequest;
use App\Http\Requests\Admin\UpdateClassRequest;
use App\Models\SchoolClass;
use App\Models\User;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with(['teacher', 'students'])->orderBy('name')->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(StoreClassRequest $request)
    {
        SchoolClass::create($request->validated());
        return redirect()->route('admin.classes.index')->with('success', 'Class created.');
    }

    public function edit(SchoolClass $school_class)
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('admin.classes.edit', compact('school_class', 'teachers'));
    }

    public function update(UpdateClassRequest $request, SchoolClass $school_class)
    {
        $school_class->update($request->validated());
        return redirect()->route('admin.classes.index')->with('success', 'Class updated.');
    }

    public function destroy(SchoolClass $school_class)
    {
        $school_class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted.');
    }
}