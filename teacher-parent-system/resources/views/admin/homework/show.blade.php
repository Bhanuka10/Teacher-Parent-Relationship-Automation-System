@extends('layouts.admin')
@section('title', $homework->title)

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .hw-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--accent); text-decoration: none; transition: color .15s;
        margin-bottom: 14px;
    }
    .hw-back:hover { color: var(--accent-mid); }
    .hw-back svg { width: 14px; height: 14px; }

    .hw-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .hw-sub   { font-size: 13px; color: #6b7280; margin: 0 0 26px; }
    .hw-sub span { color: var(--accent); font-weight: 600; }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
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
    .kpi-icon.purple { background: var(--accent-light); color: var(--accent); }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    .hw-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .hw-form-card { padding: 22px 24px; margin-bottom: 20px; }
    .hw-section-card { overflow: hidden; }
    .hw-section-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .hw-section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }

    .hw-progress { display: block; height: 6px; border-radius: 999px; background: #f3f4f6; overflow: hidden; }
    .hw-progress-fill { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--accent-mid), var(--accent)); transition: width .4s ease; }

    .hw-search-input {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 7px 10px 7px 30px;
        font-size: 12.5px; color: #111827; background: #fafafa; outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .hw-search-input:focus { border-color: var(--accent-mid); box-shadow: 0 0 0 3px rgba(79,70,229,.15); background: #fff; }

    .hw-empty { text-align: center; padding: 40px; color: #9ca3af; font-size: 13px; }

    /* Submission rows come from a partial shared with the teacher grading page —
       recolour them here, scoped to this page, instead of touching the shared file. */
    .hw-section-card .ui-avatar-indigo { background: var(--accent-light); color: var(--accent); }
    .hw-section-card .ui-tag-indigo    { background: var(--accent-light); color: var(--accent); }

    .hw-section-card .ui-status-pill { gap: 5px; }
    .hw-section-card .ui-status-pill svg { display: none; }
    .hw-section-card .ui-status-pill::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .hw-section-card .ui-status-graded    { background: #d1fae5; color: #065f46; }
    .hw-section-card .ui-status-submitted { background: var(--accent-light); color: var(--accent); }
    .hw-section-card .ui-status-pending   { background: #fef3c7; color: #92400e; }
    .hw-section-card .ui-status-expired   { background: #fee2e2; color: #991b1b; }

    .hw-section-card .text-indigo-600 { color: var(--accent); }
    .hw-section-card .text-indigo-600:hover,
    .hw-section-card .hover\:text-indigo-800:hover { color: var(--accent-mid); }
</style>
@endpush

@section('content')
@php
    $total = $homework->submissions->count();
    $submittedCount = $homework->submissions->filter->isSubmitted()->count();
    $gradedCount = $homework->submissions->filter->isGraded()->count();
    $pct = $total > 0 ? round(($submittedCount / $total) * 100) : 0;
@endphp
<div class="max-w-7xl">
    <a href="{{ route('admin.homework.index') }}" class="hw-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to homework
    </a>

    <h1 class="hw-title">{{ $homework->title }}</h1>
    <p class="hw-sub">
        <span>{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span> ·
        {{ $homework->schoolClass?->name ?? '—' }} · Assigned by {{ $homework->teacher?->name ?? '—' }}
        · {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
        @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min once started @endif
        · {{ $homework->max_marks }} marks
    </p>

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon purple">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $total }}</div>
                <div class="kpi-label">Students</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $submittedCount }}</div>
                <div class="kpi-label">Submitted</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $gradedCount }}</div>
                <div class="kpi-label">Graded</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon purple">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $pct }}%</div>
                <div class="kpi-label">Completion</div>
            </div>
        </div>
    </div>

    <section class="hw-card hw-form-card">
        <div class="flex items-center gap-3">
            <span class="hw-progress flex-1"><span class="hw-progress-fill" style="width:{{ $pct }}%"></span></span>
            <span class="whitespace-nowrap text-xs font-semibold text-gray-600">{{ $submittedCount }}/{{ $total }} submitted · {{ $gradedCount }} graded</span>
        </div>

        @if($homework->instructions)
            <p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm leading-6 text-gray-700">{{ $homework->instructions }}</p>
        @endif

        @if($homework->isQuiz() && $homework->questions->isNotEmpty())
            <details class="mt-4 border-t border-gray-100 pt-4">
                <summary class="cursor-pointer text-xs font-semibold" style="color:var(--accent)">View {{ $homework->questions->count() }} question(s)</summary>
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

    <section class="hw-card hw-section-card">
        <div class="hw-section-header">
            <div class="hw-section-title">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Submissions
            </div>
            <div style="position:relative;width:200px;max-width:100%">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="submission-search" placeholder="Filter by name…" class="hw-search-input" style="width:100%">
            </div>
        </div>
        <div id="submission-list">
            @forelse($homework->submissions as $submission)
                @include('homework._submission-row', ['homework' => $homework, 'submission' => $submission, 'canGrade' => false])
            @empty
                <div class="hw-empty"><p>No students in this class.</p></div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const search = document.getElementById('submission-search');
        search?.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            document.querySelectorAll('#submission-list [data-search]').forEach((row) => {
                row.classList.toggle('hidden', !row.dataset.search.includes(q));
            });
        });
    })();
</script>
@endpush
