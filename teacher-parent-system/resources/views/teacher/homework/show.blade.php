@extends('layouts.teacher')
@section('title', $homework->title)

@section('content')
@php
    $total = $homework->submissions->count();
    $submittedCount = $homework->submissions->filter->isSubmitted()->count();
    $gradedCount = $homework->submissions->filter->isGraded()->count();
    $pct = $total > 0 ? round(($submittedCount / $total) * 100) : 0;
@endphp
<div class="mx-auto max-w-4xl">
    <a href="{{ route('teacher.homework.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to homework
    </a>

    <section class="ui-card mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <span class="ui-tag {{ $homework->isQuiz() ? 'ui-tag-rose' : 'ui-tag-indigo' }}">{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span>
                <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $homework->title }}</h1>
                <p class="mt-1.5 text-sm text-gray-500">
                    {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
                    @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min once started @endif
                    · {{ $homework->max_marks }} marks
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('teacher.homework.edit', $homework) }}" class="ui-tab-btn" style="background:#fff;border:1px solid #e5e7eb;color:#374151">Edit timeline</a>
                <form method="POST" action="{{ route('teacher.homework.destroy', $homework) }}" onsubmit="return confirm('Delete this homework? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="ui-tab-btn" style="background:#fef2f2;color:#b91c1c">Delete</button>
                </form>
            </div>
        </div>

        @if($homework->instructions)
            <p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm leading-6 text-gray-700">{{ $homework->instructions }}</p>
        @endif

        <div class="mt-5 flex items-center gap-3">
            <span class="ui-progress flex-1"><span class="ui-progress-fill" style="width:{{ $pct }}%"></span></span>
            <span class="whitespace-nowrap text-xs font-semibold text-gray-600">{{ $submittedCount }}/{{ $total }} submitted · {{ $gradedCount }} graded</span>
        </div>

        @if($homework->isQuiz() && $homework->questions->isNotEmpty())
            <details class="mt-4 border-t border-gray-100 pt-4">
                <summary class="cursor-pointer text-xs font-semibold text-indigo-600">View {{ $homework->questions->count() }} question(s)</summary>
                <div class="mt-3 space-y-2">
                    @foreach($homework->questions as $question)
                        <div class="rounded border border-gray-200 bg-gray-50 p-2.5 text-xs">
                            <p class="font-medium text-gray-700">{{ $loop->iteration }}. {{ $question->question }} <span class="text-gray-400">({{ $question->marks }} mk, {{ $question->type === 'mcq' ? 'MCQ' : 'writing' }})</span></p>
                            @if($question->type === 'mcq')
                                <ul class="mt-1 list-inside list-disc text-gray-500">
                                    @foreach($question->options as $option)
                                        <li class="{{ $option->is_correct ? 'font-semibold text-green-700' : '' }}">{{ $option->option_text }}{{ $option->is_correct ? ' ✓' : '' }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        @endif
    </section>

    <section class="ui-card mt-6 overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800">Submissions</h2>
            <div class="ui-search-wrap" style="position:relative;width:200px;max-width:100%">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="submission-search" placeholder="Filter by name…" class="ui-input" style="padding:7px 10px 7px 30px;font-size:12.5px">
            </div>
        </div>
        <div id="submission-list">
            @forelse($homework->submissions as $submission)
                @include('homework._submission-row', ['homework' => $homework, 'submission' => $submission, 'canGrade' => true])
            @empty
                <div class="ui-empty"><p>No students in this class yet.</p></div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const search = document.getElementById('submission-search');
        document.getElementById('submission-list').addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-grade-toggle]');
            if (toggle) {
                document.getElementById('grade-panel-' + toggle.dataset.gradeToggle)?.classList.toggle('hidden');
            }
        });
        search?.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            document.querySelectorAll('#submission-list [data-search]').forEach((row) => {
                row.classList.toggle('hidden', !row.dataset.search.includes(q));
            });
        });
    })();
</script>
@endpush
