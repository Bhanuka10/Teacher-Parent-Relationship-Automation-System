{{--
    Shared submission row, used by both the teacher grading page and the
    admin read-only oversight page. Pass:
    - $homework  (Homework, with questions.options loaded if type=quiz)
    - $submission (HomeworkSubmission, with student.profile + answers loaded)
    - $canGrade  (bool) — teacher gets the inline grade form, admin doesn't
--}}
@php
    $status = $submission->status();
    $statusLabel = ['pending' => 'Not submitted', 'submitted' => 'Submitted', 'graded' => 'Graded', 'expired' => 'Expired'][$status];
    $initials = collect(explode(' ', $submission->student->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
@endphp
<div class="border-b border-gray-100 px-6 py-4 last:border-0" data-search="{{ mb_strtolower($submission->student->name) }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <div class="flex min-w-0 items-center gap-3">
            <span class="ui-avatar ui-avatar-indigo">{{ strtoupper($initials) }}</span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800">{{ $submission->student->name }}</p>
                <p class="mt-0.5 text-xs text-gray-500">
                    Admission {{ $submission->student->admission_number }}
                    @if($submission->submitted_at) · Submitted {{ $submission->submitted_at->format('d M, h:i A') }} @endif
                </p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @if($submission->isGraded())
                <span class="ui-tag ui-tag-indigo">{{ $submission->marks }}/{{ $homework->max_marks }}</span>
            @endif
            <span class="ui-status-pill ui-status-{{ $status }}">
                @if($status === 'graded')
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                @elseif($status === 'submitted')
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                @elseif($status === 'expired')
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                @else
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                @endif
                {{ $statusLabel }}
            </span>

            @if($canGrade && $submission->isSubmitted())
                <button type="button" class="ui-tab-btn" style="background:#eef2ff;color:#4338ca" data-grade-toggle="{{ $submission->id }}">
                    {{ $submission->isGraded() ? 'Edit grade' : 'Grade' }}
                </button>
            @endif
        </div>
    </div>

    @if($submission->isSubmitted())
        <div class="mt-3 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">
            @if($homework->type === 'file')
                @if($submission->file_path)
                    <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 font-semibold text-indigo-600 hover:text-indigo-800">
                        <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M4 20h16"/></svg>
                        {{ $submission->file_original_name ?? 'Download file' }}
                    </a>
                @endif
            @else
                <p class="font-semibold text-gray-700">
                    Auto-scored (MCQ): {{ $submission->auto_score }} mark{{ $submission->auto_score === 1 ? '' : 's' }}
                </p>
                <div class="mt-2 space-y-2">
                    @foreach($homework->questions as $question)
                        @php($answer = $submission->answers->firstWhere('homework_question_id', $question->id))
                        <div class="rounded border border-gray-200 bg-white p-2.5">
                            <p class="font-medium text-gray-700">{{ $loop->iteration }}. {{ $question->question }} <span class="text-gray-400">({{ $question->marks }} mk)</span></p>
                            @if($question->type === 'mcq')
                                <p class="mt-1 {{ $answer?->is_correct ? 'text-green-700' : 'text-red-600' }}">
                                    {{ $answer?->selectedOption?->option_text ?? 'No answer' }}
                                    {{ $answer?->is_correct ? '✓ correct' : '✗ incorrect' }}
                                </p>
                            @else
                                <p class="mt-1 whitespace-pre-line text-gray-600">{{ $answer?->answer_text ?: 'No answer' }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($submission->isGraded() && $submission->feedback)
                <p class="mt-2 border-t border-gray-200 pt-2"><span class="font-semibold text-gray-700">Feedback:</span> {{ $submission->feedback }}</p>
            @endif
        </div>
    @endif

    @if($canGrade && $submission->isSubmitted())
        <form method="POST" action="{{ route('teacher.homework.grade', [$homework, $submission]) }}"
              class="hidden mt-3 flex flex-wrap items-end gap-3 rounded-lg border border-indigo-100 bg-indigo-50/50 p-3"
              id="grade-panel-{{ $submission->id }}">
            @csrf
            @method('PUT')
            <div style="width:110px">
                <label class="ui-field-label" style="margin-bottom:4px">Marks (/{{ $homework->max_marks }})</label>
                <input type="number" name="marks" min="0" max="{{ $homework->max_marks }}" required
                       value="{{ old('marks', $submission->marks ?? ($homework->type === 'quiz' ? $submission->auto_score : '')) }}"
                       class="ui-input" style="padding:7px 10px">
            </div>
            <div style="flex:1;min-width:200px">
                <label class="ui-field-label" style="margin-bottom:4px">Feedback</label>
                <input type="text" name="feedback" value="{{ old('feedback', $submission->feedback) }}"
                       placeholder="Optional feedback for the student" class="ui-input" style="padding:7px 10px">
            </div>
            <button type="submit" class="ui-submit-btn" style="padding:8px 16px;margin-top:0">Save grade</button>
        </form>
    @endif
</div>
