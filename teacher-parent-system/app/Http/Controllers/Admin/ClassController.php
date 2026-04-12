<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassRequest;
use App\Http\Requests\Admin\UpdateClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $classes = SchoolClass::with('teacher')
            ->withCount('students')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->get();

        return view('admin.classes.index', compact('classes', 'search'));
    }

    public function show(SchoolClass $school_class)
    {
        $school_class->load([
            'teacher',
            'students' => function ($query) {
                $query->orderBy('name');
            },
        ]);

        return view('admin.classes.show', compact('school_class'));
    }

    public function removeTeacher(SchoolClass $school_class)
    {
        $school_class->update(['teacher_id' => null]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Teacher removed from class.');
    }

    public function resetClass(SchoolClass $school_class)
    {
        $school_class->update(['teacher_id' => null]);
        $school_class->students()->update(['school_class_id' => null]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class reset completed. Teacher and students were unlinked.');
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