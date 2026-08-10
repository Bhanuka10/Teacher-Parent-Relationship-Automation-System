@extends('layouts.parent')
@section('title', $homework->title)

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('parent.homework.index') }}" class="ui-back" style="color:#c2410c">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to homework
    </a>

    <section class="ui-card mt-4 p-6">
        <span class="ui-tag {{ $homework->isQuiz() ? 'ui-tag-rose' : 'ui-tag-orange' }}">{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $homework->title }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">
            {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
            @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min once started @endif
            · {{ $homework->max_marks }} marks
        </p>
        @if($homework->instructions)
            <p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm leading-6 text-gray-700">{{ $homework->instructions }}</p>
        @endif
    </section>

    @if($submission->isGraded())
        {{-- ═══════ GRADED RESULT (both types) ═══════ --}}
        <section class="ui-card mt-6 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Your result</h2>
                <span class="ui-tag ui-tag-orange" style="font-size:13px;padding:5px 12px">{{ $submission->marks }}/{{ $homework->max_marks }}</span>
            </div>
            @if($submission->feedback)
                <p class="mt-3 rounded-lg bg-orange-50 p-3 text-sm text-gray-700"><span class="font-semibold">Feedback:</span> {{ $submission->feedback }}</p>
            @endif

            @if($homework->isQuiz())
                <div class="mt-4 space-y-2">
                    @foreach($homework->questions as $question)
                        @php($answer = $submission->answers->firstWhere('homework_question_id', $question->id))
                        <div class="rounded border border-gray-200 p-3 text-sm">
                            <p class="font-medium text-gray-700">{{ $loop->iteration }}. {{ $question->question }}</p>
                            @if($question->type === 'mcq')
                                <p class="mt-1 {{ $answer?->is_correct ? 'text-green-700' : 'text-red-600' }}">
                                    Your answer: {{ $answer?->selectedOption?->option_text ?? 'No answer' }}
                                    {{ $answer?->is_correct ? '✓' : '✗' }}
                                </p>
                            @else
                                <p class="mt-1 whitespace-pre-line text-gray-600">{{ $answer?->answer_text ?: 'No answer' }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif($submission->file_path)
                <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-orange-600 hover:text-orange-800">
                    <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M4 20h16"/></svg>
                    {{ $submission->file_original_name }}
                </a>
            @endif
        </section>

    @elseif($homework->type === 'file')
        {{-- ═══════ FILE SUBMISSION ═══════ --}}
        <section class="ui-card mt-6 p-6">
            @if($submission->isSubmitted())
                <div class="ui-status-pill ui-status-submitted" style="display:inline-flex">Submitted — awaiting grading</div>
                @if($submission->file_path)
                    <p class="mt-3 text-sm text-gray-600">
                        Current file:
                        <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="font-semibold text-orange-600 hover:text-orange-800">{{ $submission->file_original_name }}</a>
                    </p>
                @endif
                @if(!$homework->isPastDue())
                    <p class="mt-3 text-xs text-gray-400">You can replace your file until the due date or until it's graded.</p>
                @endif
            @endif

            @if(!$homework->isPastDue())
                <form method="POST" action="{{ route('parent.homework.submit', $homework) }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-3">
                    @csrf
                    <div style="flex:1;min-width:220px">
                        <label class="ui-field-label">{{ $submission->isSubmitted() ? 'Replace file' : 'Upload file' }}</label>
                        <input type="file" name="file" required class="ui-input" style="padding:8px 10px">
                        <p class="mt-1.5 text-xs text-gray-400">
                            Accepted: {{ collect($homework->allowed_file_types ?: ['pdf','pptx','video','png'])->map(fn($t) => ucfirst($t))->implode(', ') }}
                        </p>
                        @error('file')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="ui-submit-btn" style="background:linear-gradient(135deg,#c2410c,#f97316)">Submit</button>
                </form>
            @else
                <p class="{{ $submission->isSubmitted() ? 'mt-3' : '' }} ui-status-pill ui-status-expired" style="display:inline-flex">Due date has passed</p>
            @endif
        </section>

    @else
        {{-- ═══════ QUIZ ═══════ --}}
        @if($submission->isSubmitted())
            <section class="ui-card mt-6 p-6">
                <div class="ui-status-pill ui-status-submitted" style="display:inline-flex">Submitted — awaiting grading</div>
                <p class="mt-3 text-sm text-gray-500">Your teacher will review the written questions and release your final mark and feedback here.</p>
            </section>
        @elseif($submission->started_at && $submission->isExpired())
            <section class="ui-card mt-6 p-6">
                <div class="ui-status-pill ui-status-expired" style="display:inline-flex">Time expired</div>
                <p class="mt-3 text-sm text-gray-500">Your attempt ran out of time before it was submitted.</p>
            </section>
        @elseif($submission->started_at)
            {{-- IN PROGRESS --}}
            <section class="ui-card mt-6 p-6">
                <div class="flex items-center justify-between rounded-lg bg-rose-50 px-4 py-2.5">
                    <span class="text-sm font-semibold text-rose-700">Time remaining</span>
                    <span id="quiz-timer" class="font-mono text-lg font-bold text-rose-700">--:--</span>
                </div>

                <form method="POST" action="{{ route('parent.homework.answer', $homework) }}" id="quiz-form"
                      data-expires="{{ $submission->expires_at?->valueOf() }}" class="mt-5 space-y-5">
                    @csrf
                    @foreach($homework->questions as $question)
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-sm font-semibold text-gray-800">{{ $loop->iteration }}. {{ $question->question }} <span class="font-normal text-gray-400">({{ $question->marks }} mk)</span></p>
                            @if($question->type === 'mcq')
                                <div class="mt-3 space-y-2">
                                    @foreach($question->options as $option)
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 p-2.5 text-sm hover:bg-orange-50/50 has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="text-orange-600 focus:ring-orange-500">
                                            {{ $option->option_text }}
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <textarea name="answers[{{ $question->id }}]" rows="3" class="ui-input mt-3 resize-none" placeholder="Write your answer…"></textarea>
                            @endif
                        </div>
                    @endforeach

                    <button type="submit" class="ui-submit-btn w-full justify-center" style="width:100%;background:linear-gradient(135deg,#c2410c,#f97316)">Submit quiz</button>
                </form>
            </section>
        @elseif($homework->isPastDue())
            <section class="ui-card mt-6 p-6">
                <div class="ui-status-pill ui-status-expired" style="display:inline-flex">This quiz is closed</div>
            </section>
        @else
            <section class="ui-card mt-6 p-6 text-center">
                <p class="text-sm text-gray-600">{{ $homework->questions->count() }} question(s) · {{ $homework->duration_minutes }} minutes once you start · {{ $homework->max_marks }} marks total</p>
                <p class="mt-1 text-xs text-gray-400">The timer starts the moment you click Start — make sure you're ready.</p>
                <form method="POST" action="{{ route('parent.homework.start', $homework) }}" class="mt-4">
                    @csrf
                    <button type="submit" class="ui-submit-btn" style="background:linear-gradient(135deg,#c2410c,#f97316)">Start quiz</button>
                </form>
            </section>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('quiz-form');
        if (!form) return;

        const expiresAt = Number(form.dataset.expires);
        const timerEl = document.getElementById('quiz-timer');
        if (!expiresAt || !timerEl) return;

        function tick() {
            const remainingMs = expiresAt - Date.now();
            if (remainingMs <= 0) {
                timerEl.textContent = '00:00';
                clearInterval(interval);
                form.requestSubmit();
                return;
            }
            const totalSeconds = Math.floor(remainingMs / 1000);
            const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');
            timerEl.textContent = `${minutes}:${seconds}`;
        }

        tick();
        const interval = setInterval(tick, 1000);
    })();
</script>
@endpush
