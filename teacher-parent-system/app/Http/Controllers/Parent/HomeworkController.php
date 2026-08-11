<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkAnswer;
use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function index()
    {
        $parent = auth()->user();
        $student = $parent->students()->with('schoolClass')->first();

        if (!$student) {
            return view('parent.homework.index', ['student' => null, 'submissions' => collect()]);
        }

        $submissions = HomeworkSubmission::where('student_id', $student->id)
            ->with('homework')
            ->get()
            ->sortByDesc(fn ($submission) => $submission->homework->created_at)
            ->values();

        return view('parent.homework.index', compact('student', 'submissions'));
    }

    public function show(Homework $homework)
    {
        [$student, $submission] = $this->resolve($homework);

        $homework->load('questions.options', 'teacher');
        $submission->load('answers');

        return view('parent.homework.show', compact('homework', 'student', 'submission'));
    }

    public function submitFile(Request $request, Homework $homework)
    {
        [, $submission] = $this->resolve($homework);
        abort_unless($homework->type === 'file', 404);

        if ($submission->isGraded()) {
            return back()->withErrors(['file' => 'This has already been graded and can no longer be changed.']);
        }

        if ($homework->isPastDue()) {
            return back()->withErrors(['file' => 'The due date for this homework has passed.']);
        }

        $categories = $homework->allowed_file_types ?: ['pdf', 'pptx', 'video', 'png'];
        $extensionMap = ['pdf' => ['pdf'], 'pptx' => ['pptx'], 'video' => ['mp4', 'mov', 'webm'], 'png' => ['png']];
        $allowedExtensions = collect($categories)->flatMap(fn ($category) => $extensionMap[$category] ?? [])->implode(',');

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200', "mimes:{$allowedExtensions}"],
        ]);

        // Replace any previous attempt's file rather than leaving it orphaned on disk.
        Storage::disk('public')->deleteDirectory("homework-submissions/{$submission->id}");

        $file = $validated['file'];
        $path = $file->store("homework-submissions/{$submission->id}", 'public');

        $submission->update([
            'file_path' => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
        ]);

        return redirect()->route('parent.homework.show', $homework)->with('success', 'Homework submitted.');
    }

    public function startQuiz(Homework $homework)
    {
        [, $submission] = $this->resolve($homework);
        abort_unless($homework->type === 'quiz', 404);

        if ($submission->started_at) {
            return redirect()->route('parent.homework.show', $homework);
        }

        if ($homework->isPastDue()) {
            return back()->withErrors(['quiz' => 'This quiz is closed.']);
        }

        $startedAt = now();
        $expiresAt = $homework->duration_minutes
            ? $startedAt->copy()->addMinutes($homework->duration_minutes)
            : null;

        if ($homework->due_at && (!$expiresAt || $expiresAt->gt($homework->due_at))) {
            $expiresAt = $homework->due_at;
        }

        $submission->update(['started_at' => $startedAt, 'expires_at' => $expiresAt]);

        return redirect()->route('parent.homework.show', $homework);
    }

    public function submitQuiz(Request $request, Homework $homework)
    {
        [, $submission] = $this->resolve($homework);
        abort_unless($homework->type === 'quiz', 404);

        if (!$submission->started_at || $submission->submitted_at) {
            return redirect()->route('parent.homework.show', $homework);
        }

        // Server-side is the source of truth on timing — the client countdown is UX only.
        if ($submission->expires_at && now()->gt($submission->expires_at)) {
            return back()->withErrors(['quiz' => 'Time is up — this attempt has expired and can no longer be submitted.']);
        }

        $answers = $request->input('answers', []);
        $questions = $homework->questions()->with('options')->get();
        $autoScore = 0;
        $hasWriting = $questions->contains(fn ($question) => $question->type === 'writing');

        foreach ($questions as $question) {
            $raw = $answers[$question->id] ?? null;

            if ($question->type === 'mcq') {
                $selectedOption = $question->options->firstWhere('id', (int) $raw);
                $isCorrect = (bool) $selectedOption?->is_correct;
                $marksAwarded = $isCorrect ? $question->marks : 0;
                $autoScore += $marksAwarded;

                HomeworkAnswer::updateOrCreate(
                    ['homework_submission_id' => $submission->id, 'homework_question_id' => $question->id],
                    ['selected_option_id' => $selectedOption?->id, 'is_correct' => $isCorrect, 'marks_awarded' => $marksAwarded]
                );
            } else {
                HomeworkAnswer::updateOrCreate(
                    ['homework_submission_id' => $submission->id, 'homework_question_id' => $question->id],
                    ['answer_text' => is_string($raw) ? $raw : null]
                );
            }
        }

        $submission->update([
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
            'auto_score' => $autoScore,
            // A quiz with no writing questions grades itself instantly.
            'marks' => $hasWriting ? null : $autoScore,
            'graded_at' => $hasWriting ? null : now(),
        ]);

        return redirect()->route('parent.homework.show', $homework)->with('success', 'Quiz submitted.');
    }

    private function resolve(Homework $homework): array
    {
        $student = auth()->user()->students()->first();
        abort_unless($student, 404);

        $submission = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        return [$student, $submission];
    }
}
