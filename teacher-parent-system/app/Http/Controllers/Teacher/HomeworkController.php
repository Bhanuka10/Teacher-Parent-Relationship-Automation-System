<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\GradeSubmissionRequest;
use App\Http\Requests\Teacher\StoreHomeworkRequest;
use App\Http\Requests\Teacher\UpdateHomeworkRequest;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Support\Facades\DB;

class HomeworkController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass()->with('students')->first();

        if (!$schoolClass) {
            return view('teacher.homework.index', [
                'schoolClass' => null,
                'homeworks' => collect(),
            ]);
        }

        $homeworks = Homework::where('school_class_id', $schoolClass->id)
            ->withCount([
                'submissions as total_count',
                'submissions as submitted_count' => fn ($query) => $query->whereNotNull('submitted_at'),
                'submissions as graded_count' => fn ($query) => $query->whereNotNull('graded_at'),
            ])
            ->latest()
            ->get();

        return view('teacher.homework.index', compact('schoolClass', 'homeworks'));
    }

    public function create()
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass;

        return view('teacher.homework.create', compact('schoolClass'));
    }

    public function store(StoreHomeworkRequest $request)
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass()->with('students')->first();

        if (!$schoolClass) {
            return redirect()->route('teacher.homework.index')
                ->withErrors(['homework' => 'No class is assigned to this teacher.']);
        }

        $validated = $request->validated();

        $homework = DB::transaction(function () use ($validated, $schoolClass, $teacher) {
            $isQuiz = $validated['type'] === 'quiz';

            $homework = Homework::create([
                'school_class_id' => $schoolClass->id,
                'teacher_id' => $teacher->id,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'instructions' => $validated['instructions'] ?? null,
                'allowed_file_types' => $isQuiz ? null : $validated['allowed_file_types'],
                'max_marks' => $isQuiz
                    ? collect($validated['questions'])->sum('marks')
                    : $validated['max_marks'],
                'due_at' => $validated['due_at'] ?? null,
                'duration_minutes' => $isQuiz ? $validated['duration_minutes'] : null,
            ]);

            if ($isQuiz) {
                foreach ($validated['questions'] as $qIndex => $q) {
                    $question = $homework->questions()->create([
                        'type' => $q['type'],
                        'question' => $q['question'],
                        'marks' => $q['marks'],
                        'position' => $qIndex,
                    ]);

                    if ($q['type'] === 'mcq') {
                        foreach (($q['options'] ?? []) as $oIndex => $option) {
                            $question->options()->create([
                                'option_text' => $option['text'],
                                'is_correct' => !empty($option['is_correct']),
                                'position' => $oIndex,
                            ]);
                        }
                    }
                }
            }

            if ($schoolClass->students->isNotEmpty()) {
                $homework->submissions()->createMany(
                    $schoolClass->students->map(fn ($student) => ['student_id' => $student->id])->all()
                );
            }

            return $homework;
        });

        $label = $homework->isQuiz() ? 'Quiz' : 'Homework';

        return redirect()->route('teacher.homework.show', $homework)
            ->with('success', "{$label} created and assigned to {$schoolClass->students->count()} student(s).");
    }

    public function show(Homework $homework)
    {
        $this->authorizeOwnership($homework);

        $homework->load([
            'submissions' => fn ($query) => $query->with(['student.profile'])->orderBy('id'),
            'submissions.answers.question',
            'submissions.answers.selectedOption',
            'questions.options',
        ]);

        return view('teacher.homework.show', compact('homework'));
    }

    public function edit(Homework $homework)
    {
        $this->authorizeOwnership($homework);

        return view('teacher.homework.edit', compact('homework'));
    }

    public function update(UpdateHomeworkRequest $request, Homework $homework)
    {
        $this->authorizeOwnership($homework);

        $validated = $request->validated();
        $oldDueAt = $homework->due_at;
        $oldDuration = $homework->duration_minutes;

        $homework->update([
            'title' => $validated['title'],
            'instructions' => $validated['instructions'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
            'duration_minutes' => $homework->isQuiz() ? ($validated['duration_minutes'] ?? $oldDuration) : null,
        ]);

        // Extending the timeline only ever lengthens an in-progress attempt —
        // never shrinks it — and never touches already-submitted rows.
        if ($homework->isQuiz() && ($homework->due_at != $oldDueAt || $homework->duration_minutes != $oldDuration)) {
            $inProgress = $homework->submissions()
                ->whereNotNull('started_at')
                ->whereNull('submitted_at')
                ->get();

            foreach ($inProgress as $submission) {
                $newExpiry = $homework->duration_minutes
                    ? $submission->started_at->copy()->addMinutes($homework->duration_minutes)
                    : $homework->due_at;

                if ($homework->due_at && (!$newExpiry || $newExpiry->gt($homework->due_at))) {
                    $newExpiry = $homework->due_at;
                }

                if ($newExpiry && (!$submission->expires_at || $newExpiry->gt($submission->expires_at))) {
                    $submission->update(['expires_at' => $newExpiry]);
                }
            }
        }

        return redirect()->route('teacher.homework.show', $homework)
            ->with('success', 'Timeline updated.');
    }

    public function grade(GradeSubmissionRequest $request, Homework $homework, HomeworkSubmission $submission)
    {
        $this->authorizeOwnership($homework);
        abort_unless($submission->homework_id === $homework->id, 404);

        $validated = $request->validated();

        $submission->update([
            'marks' => $validated['marks'],
            'feedback' => $validated['feedback'] ?? null,
            'graded_at' => now(),
        ]);

        return redirect()->route('teacher.homework.show', $homework)
            ->with('success', 'Grade saved.');
    }

    public function destroy(Homework $homework)
    {
        $this->authorizeOwnership($homework);
        $homework->delete();

        return redirect()->route('teacher.homework.index')->with('success', 'Homework deleted.');
    }

    private function authorizeOwnership(Homework $homework): void
    {
        abort_unless($homework->teacher_id === auth()->id(), 403, 'Access denied.');
    }
}
