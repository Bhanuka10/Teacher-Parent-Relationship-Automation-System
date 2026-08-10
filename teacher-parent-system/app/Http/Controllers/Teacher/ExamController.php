<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\SaveExamMarksRequest;
use App\Http\Requests\Teacher\StoreExamRequest;
use App\Http\Requests\Teacher\UpdateExamRequest;
use App\Models\Exam;
use App\Services\ExamResultService;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function __construct(private ExamResultService $results) {}

    public function index()
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass;

        if (!$schoolClass) {
            return view('teacher.exams.index', ['schoolClass' => null, 'exams' => collect()]);
        }

        $exams = Exam::where('school_class_id', $schoolClass->id)
            ->withCount('subjects')
            ->orderByDesc('academic_year')
            ->orderByDesc('term')
            ->get();

        return view('teacher.exams.index', compact('schoolClass', 'exams'));
    }

    public function create()
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass;

        return view('teacher.exams.create', compact('schoolClass'));
    }

    public function store(StoreExamRequest $request)
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass;

        if (!$schoolClass) {
            return redirect()->route('teacher.exams.index')
                ->withErrors(['exam' => 'No class is assigned to this teacher.']);
        }

        $validated = $request->validated();

        $exam = DB::transaction(function () use ($validated, $schoolClass, $teacher) {
            $exam = Exam::create([
                'school_class_id' => $schoolClass->id,
                'teacher_id' => $teacher->id,
                'academic_year' => $validated['academic_year'],
                'term' => $validated['term'],
                'name' => $validated['name'],
                'exam_date' => $validated['exam_date'],
                'term_start_date' => $validated['term_start_date'],
                'term_end_date' => $validated['term_end_date'],
            ]);

            $exam->subjects()->createMany(
                collect($validated['subjects'])->values()->map(fn ($subject, $index) => [
                    'name' => $subject['name'],
                    'max_mark' => $subject['max_mark'],
                    'position' => $index,
                ])->all()
            );

            return $exam;
        });

        return redirect()->route('teacher.exams.show', $exam)
            ->with('success', 'Exam created. Enter student marks below.');
    }

    public function show(Exam $exam)
    {
        $this->authorizeOwnership($exam);

        $exam->load('subjects');
        $grid = $this->results->gridFor($exam);

        return view('teacher.exams.show', compact('exam', 'grid'));
    }

    public function edit(Exam $exam)
    {
        $this->authorizeOwnership($exam);

        $exam->load('subjects.marks');

        return view('teacher.exams.edit', compact('exam'));
    }

    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $this->authorizeOwnership($exam);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $exam) {
            $exam->update([
                'academic_year' => $validated['academic_year'],
                'term' => $validated['term'],
                'name' => $validated['name'],
                'exam_date' => $validated['exam_date'],
                'term_start_date' => $validated['term_start_date'],
                'term_end_date' => $validated['term_end_date'],
            ]);

            if (!empty($validated['new_subjects'])) {
                $nextPosition = (int) $exam->subjects()->max('position') + 1;

                $exam->subjects()->createMany(
                    collect($validated['new_subjects'])->values()->map(fn ($subject, $index) => [
                        'name' => $subject['name'],
                        'max_mark' => $subject['max_mark'],
                        'position' => $nextPosition + $index,
                    ])->all()
                );
            }
        });

        return redirect()->route('teacher.exams.show', $exam)->with('success', 'Exam updated.');
    }

    public function saveMarks(SaveExamMarksRequest $request, Exam $exam)
    {
        $this->authorizeOwnership($exam);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $exam) {
            foreach ($validated['marks'] as $studentId => $subjectMarks) {
                foreach ($subjectMarks as $subjectId => $mark) {
                    $exam->subjects()
                        ->whereKey($subjectId)
                        ->firstOrFail()
                        ->marks()
                        ->updateOrCreate(
                            ['student_id' => $studentId],
                            ['mark' => $mark === '' ? null : $mark, 'entered_by' => auth()->id()]
                        );
                }
            }
        });

        return redirect()->route('teacher.exams.show', $exam)->with('success', 'Marks saved.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorizeOwnership($exam);
        $exam->delete();

        return redirect()->route('teacher.exams.index')->with('success', 'Exam deleted.');
    }

    private function authorizeOwnership(Exam $exam): void
    {
        abort_unless($exam->teacher_id === auth()->id(), 403, 'Access denied.');
    }
}
