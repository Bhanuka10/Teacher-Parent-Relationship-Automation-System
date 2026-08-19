<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->query('class_id');
        $type = $request->query('type');
        $search = trim((string) $request->query('search', ''));

        $query = Homework::with(['schoolClass', 'teacher'])
            ->withCount([
                'submissions as total_count',
                'submissions as submitted_count' => fn ($q) => $q->whereNotNull('submitted_at'),
                'submissions as graded_count' => fn ($q) => $q->whereNotNull('graded_at'),
            ])
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search !== '', fn ($q) => $q->where('title', 'like', '%'.$search.'%'))
            ->latest();

        $homeworks = $query->paginate(12)->withQueryString();
        $classes = SchoolClass::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => Homework::count(),
            'quizzes' => Homework::where('type', 'quiz')->count(),
            'files' => Homework::where('type', 'file')->count(),
        ];

        $searchOptions = Homework::orderBy('title')->pluck('title')->unique()->values();

        return view('admin.homework.index', compact('homeworks', 'classes', 'classId', 'type', 'search', 'counts', 'searchOptions'));
    }

    public function show(Homework $homework)
    {
        $homework->load([
            'schoolClass',
            'teacher',
            'submissions' => fn ($query) => $query->with(['student.profile'])->orderBy('id'),
            'questions.options',
        ]);

        return view('admin.homework.show', compact('homework'));
    }
}
