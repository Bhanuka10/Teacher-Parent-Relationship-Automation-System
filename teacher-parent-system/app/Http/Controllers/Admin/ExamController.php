<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Services\ExamResultService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(private ExamResultService $results) {}

    public function index(Request $request)
    {
        $classId = $request->query('class_id');
        $academicYear = $request->query('academic_year');
        $term = $request->query('term');
        $search = trim((string) $request->query('search', ''));

        $exams = Exam::with(['schoolClass', 'teacher'])
            ->withCount('subjects')
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->when($academicYear, fn ($q) => $q->where('academic_year', $academicYear))
            ->when($term, fn ($q) => $q->where('term', $term))
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderByDesc('academic_year')
            ->orderByDesc('term')
            ->paginate(12)
            ->withQueryString();

        $classes = SchoolClass::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => Exam::count(),
            'classes_covered' => Exam::distinct('school_class_id')->count('school_class_id'),
            'subjects_recorded' => ExamSubject::count(),
        ];

        $searchOptions = Exam::orderBy('name')->pluck('name')->unique()->values();

        return view('admin.exams.index', compact('exams', 'classes', 'classId', 'academicYear', 'term', 'search', 'counts', 'searchOptions'));
    }

    public function show(Exam $exam)
    {
        $exam->load(['schoolClass', 'teacher', 'subjects']);
        $grid = $this->results->gridFor($exam);

        return view('admin.exams.show', compact('exam', 'grid'));
    }
}
