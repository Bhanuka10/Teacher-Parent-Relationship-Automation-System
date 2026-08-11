<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Services\ExamResultService;

class ResultController extends Controller
{
    public function __construct(private ExamResultService $results) {}

    public function index()
    {
        $student = auth()->user()->students()->with('schoolClass')->first();

        if (!$student || !$student->schoolClass) {
            return view('parent.results.index', ['student' => $student, 'history' => collect()]);
        }

        $history = $this->results->termHistoryFor($student, $student->schoolClass);

        return view('parent.results.index', compact('student', 'history'));
    }

    public function show(Exam $exam)
    {
        $student = auth()->user()->students()->first();
        abort_unless($student, 404);
        abort_unless($exam->school_class_id === $student->school_class_id, 404);

        $exam->load('subjects');
        $grid = $this->results->gridFor($exam);
        $row = $grid->first(fn ($row) => $row['student']->id === $student->id);

        $summary = $this->results->summaryFor($exam, $student);
        $history = $this->results->termHistoryFor($student, $exam->schoolClass);
        $attendance = $this->results->attendanceCorrelationFor($exam, $student);

        $subjectAverages = $exam->subjects->mapWithKeys(function ($subject) use ($grid) {
            $marks = $grid->pluck("marks.{$subject->id}.mark")->filter(fn ($mark) => $mark !== null);

            return [$subject->id => $marks->isNotEmpty() ? round($marks->avg(), 1) : null];
        });

        return view('parent.results.show', compact('exam', 'row', 'summary', 'history', 'attendance', 'student', 'subjectAverages'));
    }
}
