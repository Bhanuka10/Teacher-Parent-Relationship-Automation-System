@extends('layouts.parent')
@section('title', $homework->title)

@push('styles')
<style>
    :root {
        --p-accent:       #c2410c;
        --p-accent-light: #ffedd5;
        --p-accent-mid:   #f97316;
    }

    /* ── KPI stat cards (identical to the dashboard's kit) ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 16px;
    }
    .kpi-icon {
        width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .kpi-icon.orange { background: var(--p-accent-light); color: var(--p-accent); }
    .kpi-icon.green  { background: #d1fae5; color: #065f46; }
    .kpi-icon.red    { background: #fee2e2; color: #991b1b; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.rose   { background: #ffe4e6; color: #be123c; }
    .kpi-val   { font-size: 18px; font-weight: 800; color: #111827; line-height: 1.2; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── section card (same radius/shadow as the dashboard's cards) ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    /* ── two-column content layout (same ratio as the dashboard) ── */
    .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .content-grid { grid-template-columns: 1fr; } }

    /* ── sidebar timeline (mirrors the profile page's Account Overview card) ── */
    .pf-card-header {
        display: flex; align-items: center; gap: 10px; padding: 16px 22px;
        border-bottom: 1px solid #f3f4f6;
    }
    .pf-card-header svg { width: 16px; height: 16px; color: var(--p-accent); }
    .pf-card-title { font-size: 13.5px; font-weight: 800; color: #111827; }
    .pf-stat-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 12.5px;
    }
    .pf-stat-row:last-child { border-bottom: none; }
    .pf-stat-label { color: #6b7280; display: flex; align-items: center; gap: 7px; }
    .pf-stat-label svg { width: 14px; height: 14px; color: #9ca3af; flex-shrink: 0; }
    .pf-stat-value { font-weight: 700; color: #111827; text-align: right; }
</style>
@endpush

@section('content')
@php
    $statusMeta = [
        'pending'   => ['label' => 'Not submitted', 'icon' => 'amber'],
        'submitted' => ['label' => 'Awaiting grading', 'icon' => 'indigo'],
        'graded'    => ['label' => 'Graded', 'icon' => 'green'],
        'expired'   => ['label' => 'Expired', 'icon' => 'red'],
    ][$submission->status()];
@endphp
<div class="max-w-7xl">
    <a href="{{ route('parent.homework.index') }}" class="ui-back" style="color:#c2410c">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to homework
    </a>

    <section class="section-card ui-animate-in mt-4 p-6">
        <span class="ui-tag {{ $homework->isQuiz() ? 'ui-tag-rose' : 'ui-tag-orange' }}">{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $homework->title }}</h1>
        @if($homework->instructions)
            <p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm leading-6 text-gray-700">{{ $homework->instructions }}</p>
        @endif
    </section>

    {{-- ═══════ QUICK FACTS ═══════ --}}
    <div class="kpi-grid mt-4 ui-animate-in ui-animate-in-1">
        <div class="kpi-card">
            <div class="kpi-icon {{ $statusMeta['icon'] }}">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $statusMeta['label'] }}</div>
                <div class="kpi-label">Status</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon orange">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $homework->due_at?->format('d M, h:i A') ?? 'No due date' }}</div>
                <div class="kpi-label">Due</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $homework->max_marks }}</div>
                <div class="kpi-label">Max Marks</div>
            </div>
        </div>
        @if($homework->isQuiz())
            <div class="kpi-card">
                <div class="kpi-icon rose">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $homework->duration_minutes }} min</div>
                    <div class="kpi-label">Time Limit</div>
                </div>
            </div>
        @endif
    </div>

    <div class="content-grid mt-6">
    <div>
    @if($submission->isGraded())
        {{-- ═══════ GRADED RESULT (both types) ═══════ --}}
        @php $pct = $homework->max_marks > 0 ? round(($submission->marks / $homework->max_marks) * 100) : 0; @endphp
        <section class="section-card ui-animate-in ui-animate-in-2 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h2 class="font-bold text-gray-800">Your Result</h2>
                <span class="ui-tag ui-tag-orange" style="font-size:13px;padding:5px 12px">{{ $submission->marks }}/{{ $homework->max_marks }}</span>
            </div>
            <div class="p-6">
                <span class="ui-progress"><span class="ui-progress-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,#fb923c,#c2410c)"></span></span>

                @if($submission->feedback)
                    <p class="mt-4 rounded-lg bg-orange-50 p-3 text-sm text-gray-700"><span class="font-semibold">Feedback:</span> {{ $submission->feedback }}</p>
                @endif

                @if($homework->isQuiz())
                    <div class="mt-4 space-y-2">
                        @foreach($homework->questions as $question)
                            @php($answer = $submission->answers->firstWhere('homework_question_id', $question->id))
                            <div class="rounded-lg border border-gray-200 p-3 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="font-medium text-gray-700">{{ $loop->iteration }}. {{ $question->question }}</p>
                                    @if($question->type === 'mcq')
                                        <span class="ui-status-pill {{ $answer?->is_correct ? 'ui-status-graded' : 'ui-status-expired' }}" style="flex-shrink:0">{{ $answer?->is_correct ? 'Correct' : 'Incorrect' }}</span>
                                    @endif
                                </div>
                                @if($question->type === 'mcq')
                                    <p class="mt-1.5 text-gray-600">Your answer: {{ $answer?->selectedOption?->option_text ?? 'No answer' }}</p>
                                @else
                                    <p class="mt-1.5 whitespace-pre-line text-gray-600">{{ $answer?->answer_text ?: 'No answer' }}</p>
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
            </div>
        </section>

    @elseif($homework->type === 'file')
        {{-- ═══════ FILE SUBMISSION ═══════ --}}
        <section class="section-card ui-animate-in ui-animate-in-2 overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-bold text-gray-800">{{ $submission->isSubmitted() ? 'Your Submission' : 'Submit Your Work' }}</h2>
            </div>
            <div class="p-6">
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
            </div>
        </section>

    @else
        {{-- ═══════ QUIZ ═══════ --}}
        @if($submission->isSubmitted())
            <section class="section-card ui-animate-in ui-animate-in-2 p-6">
                <div class="ui-status-pill ui-status-submitted" style="display:inline-flex">Submitted — awaiting grading</div>
                <p class="mt-3 text-sm text-gray-500">Your teacher will review the written questions and release your final mark and feedback here.</p>
            </section>
        @elseif($submission->started_at && $submission->isExpired())
            <section class="section-card ui-animate-in ui-animate-in-2 p-6">
                <div class="ui-status-pill ui-status-expired" style="display:inline-flex">Time expired</div>
                <p class="mt-3 text-sm text-gray-500">Your attempt ran out of time before it was submitted.</p>
            </section>
        @elseif($submission->started_at)
            {{-- IN PROGRESS --}}
            <section class="section-card ui-animate-in ui-animate-in-2 p-6">
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
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 p-2.5 text-sm hover:bg-orange-50/50 has-checked:border-orange-400 has-checked:bg-orange-50">
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
            <section class="section-card ui-animate-in ui-animate-in-2 p-6">
                <div class="ui-status-pill ui-status-expired" style="display:inline-flex">This quiz is closed</div>
            </section>
        @else
            <section class="section-card ui-animate-in ui-animate-in-2 p-6 text-center">
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

    {{-- ── Right: timeline sidebar ── --}}
    <div class="flex flex-col gap-6">
        <section class="section-card ui-animate-in ui-animate-in-2">
            <div class="pf-card-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
                <span class="pf-card-title">Timeline</span>
            </div>
            <div class="p-6" style="padding-top:6px;padding-bottom:6px">
                <div class="pf-stat-row">
                    <span class="pf-stat-label">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25"/></svg>
                        Assigned by
                    </span>
                    <span class="pf-stat-value">{{ $homework->teacher?->name ?? '—' }}</span>
                </div>
                <div class="pf-stat-row">
                    <span class="pf-stat-label">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4.75" y="5.75" width="14.5" height="13.5" rx="1.75"/><path d="M8 3.5v4.5M16 3.5v4.5M4.75 10h14.5"/></svg>
                        Assigned
                    </span>
                    <span class="pf-stat-value">{{ $homework->created_at->format('d M Y') }}</span>
                </div>
                @if($submission->started_at)
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 5v14l11-7L8 5Z"/></svg>
                            Started
                        </span>
                        <span class="pf-stat-value">{{ $submission->started_at->format('d M, h:i A') }}</span>
                    </div>
                @endif
                @if($submission->submitted_at)
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21V8m0 0 4 4m-4-4-4 4M4 4h16"/></svg>
                            Submitted
                        </span>
                        <span class="pf-stat-value">{{ $submission->submitted_at->format('d M, h:i A') }}</span>
                    </div>
                @endif
                @if($submission->graded_at)
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                            Graded
                        </span>
                        <span class="pf-stat-value">{{ $submission->graded_at->format('d M, h:i A') }}</span>
                    </div>
                @endif
            </div>
        </section>
    </div>
    </div>
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
