@extends('layouts.teacher')
@section('title', $homework->title)

@push('styles')
<style>
    :root {
        --t-accent:       #0f766e;
        --t-accent-light: #ccfbf1;
        --t-accent-mid:   #14b8a6;
    }

    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0; }
    .db-sub span { color: var(--t-accent); font-weight: 600; }

    .db-head {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 14px; margin-bottom: 26px;
    }

    .quick-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .qa-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 10px;
        font-size: 12.5px; font-weight: 700;
        text-decoration: none; transition: all .15s;
        border: 1.5px solid transparent; cursor: pointer;
    }
    .qa-btn svg { width: 15px; height: 15px; }
    .qa-btn.primary { background: var(--t-accent); color: #fff; box-shadow: 0 4px 14px rgba(15,118,110,.28); }
    .qa-btn.primary:hover { background: #0d5f58; transform: translateY(-1px); }
    .qa-btn.ghost { background: #fff; color: var(--t-accent); border-color: #d1fae5; }
    .qa-btn.ghost:hover { background: var(--t-accent-light); border-color: var(--t-accent-mid); }
    .qa-btn.ghost-rose { background: #fff; color: #be123c; border-color: #fecdd3; }
    .qa-btn.ghost-rose:hover { background: #ffe4e6; border-color: #fda4af; }

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
    .kpi-icon.teal   { background: var(--t-accent-light); color: var(--t-accent); }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── section card ── */
    .exam-form-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,.04); margin-bottom: 20px;
    }
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }

    .att-input {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 7px 10px 7px 30px;
        font-size: 12.5px; color: #111827; background: #fafafa; outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .att-input:focus { border-color: var(--t-accent-mid); box-shadow: 0 0 0 3px rgba(20,184,166,.15); background: #fff; }
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
    <div class="db-head">
        <div>
            <h1 class="db-title">{{ $homework->title }}</h1>
            <p class="db-sub">
                <span>{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span> ·
                {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
                @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min once started @endif
                · {{ $homework->max_marks }} marks
            </p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.homework.edit', $homework) }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L20 8l-4-4L4 16v4Z"/></svg>
                Edit timeline
            </a>
            <form method="POST" action="{{ route('teacher.homework.destroy', $homework) }}" onsubmit="return confirm('Delete this homework? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="qa-btn ghost-rose">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6.75h12M9.75 6.75V5a1.25 1.25 0 0 1 1.25-1.25h2A1.25 1.25 0 0 1 14.25 5v1.75m-7 0 .58 11.6a2 2 0 0 0 2 1.9h3.34a2 2 0 0 0 2-1.9l.58-11.6"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon teal">
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
            <div class="kpi-icon teal">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $pct }}%</div>
                <div class="kpi-label">Completion</div>
            </div>
        </div>
    </div>

    <section class="exam-form-card">
        <div class="flex items-center gap-3">
            <span class="ui-progress flex-1"><span class="ui-progress-fill" style="width:{{ $pct }}%"></span></span>
            <span class="whitespace-nowrap text-xs font-semibold text-gray-600">{{ $submittedCount }}/{{ $total }} submitted · {{ $gradedCount }} graded</span>
        </div>

        @if($homework->instructions)
            <p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm leading-6 text-gray-700">{{ $homework->instructions }}</p>
        @endif

        @if($homework->isQuiz() && $homework->questions->isNotEmpty())
            <details class="mt-4 border-t border-gray-100 pt-4">
                <summary class="cursor-pointer text-xs font-semibold" style="color:#0f766e">View {{ $homework->questions->count() }} question(s)</summary>
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

    <section class="section-card">
        <div class="section-header">
            <div class="section-title">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Submissions
            </div>
            <div style="position:relative;width:200px;max-width:100%">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="submission-search" placeholder="Filter by name…" class="att-input" style="width:100%">
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
