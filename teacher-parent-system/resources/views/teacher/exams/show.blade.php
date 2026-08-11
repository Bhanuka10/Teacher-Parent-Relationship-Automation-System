@extends('layouts.teacher')
@section('title', $exam->name)

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
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }
    .section-sub { font-size: 11.5px; color: #9ca3af; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">{{ $exam->name }}</h1>
            <p class="db-sub">
                Term {{ $exam->term }} · <span>{{ $exam->academic_year }}</span> ·
                exam date {{ $exam->exam_date->format('d M Y') }} ·
                term window {{ $exam->term_start_date->format('d M') }}–{{ $exam->term_end_date->format('d M Y') }}
            </p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.exams.edit', $exam) }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L20 8l-4-4L4 16v4Z"/></svg>
                Edit exam
            </a>
            <form method="POST" action="{{ route('teacher.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this exam and all its marks? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="qa-btn ghost-rose">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6.75h12M9.75 6.75V5a1.25 1.25 0 0 1 1.25-1.25h2A1.25 1.25 0 0 1 14.25 5v1.75m-7 0 .58 11.6a2 2 0 0 0 2 1.9h3.34a2 2 0 0 0 2-1.9l.58-11.6"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @php
        $totalStudents = $grid->count();
        $totalSubjects = $exam->subjects->count();
        $completeCount = $grid->where('is_complete', true)->count();
        $averages = $grid->pluck('average')->filter(fn ($a) => $a !== null);
        $classAverage = $averages->isNotEmpty() ? round($averages->avg(), 1) : null;
    @endphp

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon teal">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $totalStudents }}</div>
                <div class="kpi-label">Students</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $totalSubjects }}</div>
                <div class="kpi-label">Subjects</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $completeCount }}/{{ $totalStudents }}</div>
                <div class="kpi-label">Fully Graded</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon teal">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $classAverage ?? '—' }}</div>
                <div class="kpi-label">Class Average</div>
            </div>
        </div>
    </div>

    <section class="section-card">
        <div class="section-header">
            <div class="section-title">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                Marks entry
            </div>
            <p class="section-sub">Tab/Enter/arrow keys move between cells · only complete rows are ranked</p>
        </div>
        <form method="POST" action="{{ route('teacher.exams.marks.save', $exam) }}" id="marks-form">
            @csrf
            @method('PUT')
            <div class="p-4">
                @include('exams._marks-grid', ['exam' => $exam, 'grid' => $grid, 'canEdit' => true])
            </div>
            <div class="border-t border-gray-100 px-6 py-4">
                <button type="submit" class="qa-btn primary w-full justify-center" style="width:100%;padding:12px;font-size:13.5px">Save all marks</button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const inputs = Array.from(document.querySelectorAll('.exam-mark-input'));

    function moveFocus(row, col) {
        const el = inputs.find((i) => +i.dataset.row === row && +i.dataset.col === col);
        if (el) el.focus();
    }

    inputs.forEach((input) => {
        input.addEventListener('keydown', (event) => {
            const row = +input.dataset.row;
            const col = +input.dataset.col;

            if (event.key === 'Enter' || event.key === 'ArrowDown') {
                event.preventDefault();
                moveFocus(row + 1, col);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveFocus(row - 1, col);
            } else if (event.key === 'ArrowRight' && input.selectionStart === input.value.length) {
                moveFocus(row, col + 1);
            } else if (event.key === 'ArrowLeft' && input.selectionStart === 0) {
                moveFocus(row, col - 1);
            }
        });
        input.addEventListener('focus', () => input.select());
    });
})();
</script>
@endpush
