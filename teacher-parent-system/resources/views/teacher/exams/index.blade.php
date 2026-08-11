@extends('layouts.teacher')
@section('title', 'Exams')

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
    .kpi-icon.gray   { background: #f3f4f6; color: #6b7280; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── toolbar ── */
    .toolbar-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 14px 18px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;
        margin-bottom: 20px;
    }
    .att-input {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 9px 13px;
        font-size: 13px; color: #111827; background: #fafafa; outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .att-input:focus { border-color: var(--t-accent-mid); box-shadow: 0 0 0 3px rgba(20,184,166,.15); background: #fff; }
    .att-search-wrap { position: relative; flex: 1; min-width: 200px; }
    .att-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #9ca3af; }
    .att-search-wrap input { width: 100%; padding-left: 32px; box-sizing: border-box; }

    /* ── section card / exam rows ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .hw-row {
        display: flex; align-items: flex-start; gap: 12px; padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background .12s;
    }
    .hw-row:last-child { border-bottom: none; }
    .hw-row:hover { background: #fafafa; }
    .hw-row.is-hidden { display: none; }
    .hw-title { font-size: 13px; font-weight: 700; color: #111827; }
    .hw-meta  { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }

    .hw-type-icon {
        width: 38px; height: 38px; flex-shrink: 0; border-radius: 11px; margin-top: 1px;
        background: #e0e7ff; color: #4338ca;
        display: flex; align-items: center; justify-content: center;
    }
    .hw-type-icon svg { width: 18px; height: 18px; }

    .hw-due-pill {
        display: inline-flex; align-items: center; border-radius: 999px; padding: 2px 9px;
        font-size: 10px; font-weight: 700; letter-spacing: .02em; white-space: nowrap; flex-shrink: 0;
        background: #fef3c7; color: #92400e;
    }

    .hw-chevron { width: 16px; height: 16px; color: #d1d5db; flex-shrink: 0; margin-top: 10px; transition: transform .15s, color .15s; }
    .hw-row:hover .hw-chevron { color: #9ca3af; transform: translateX(2px); }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Exams</h1>
            <p class="db-sub">
                Create term exams and enter marks for
                @if($schoolClass)
                    class <span>{{ $schoolClass->name }}</span>
                @else
                    <span>your class</span>
                @endif
            </p>
        </div>
        @if($schoolClass)
            <div class="quick-actions">
                <a href="{{ route('teacher.exams.create') }}" class="qa-btn primary">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New
                </a>
            </div>
        @endif
    </div>

    @if(!$schoolClass)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before creating an exam.</p>
            </div>
        </div>
    @else
        @php
            $totalExams = $exams->count();
            $totalSubjects = $exams->sum('subjects_count');
            $upcomingCount = $exams->filter(fn ($e) => $e->exam_date->isFuture())->count();
            $completedCount = $totalExams - $upcomingCount;
        @endphp

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $totalExams }}</div>
                    <div class="kpi-label">Total Exams</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $totalSubjects }}</div>
                    <div class="kpi-label">Subjects Covered</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $upcomingCount }}</div>
                    <div class="kpi-label">Upcoming</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon gray">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $completedCount }}</div>
                    <div class="kpi-label">Completed</div>
                </div>
            </div>
        </div>

        @if($totalExams)
            <div class="toolbar-card">
                <div class="att-search-wrap">
                    <label for="exam-search" class="sr-only">Search exams</label>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="text" id="exam-search" class="att-input" placeholder="Search by exam name…" autocomplete="off">
                </div>
                <div class="ui-tab-bar" id="exam-term-filter">
                    <label class="ui-tab-btn"><input type="radio" name="exam-term" value="" class="sr-only" checked>All</label>
                    <label class="ui-tab-btn"><input type="radio" name="exam-term" value="1" class="sr-only">Term 1</label>
                    <label class="ui-tab-btn"><input type="radio" name="exam-term" value="2" class="sr-only">Term 2</label>
                    <label class="ui-tab-btn"><input type="radio" name="exam-term" value="3" class="sr-only">Term 3</label>
                </div>
            </div>
        @endif

        <section class="section-card">
            @forelse($exams as $exam)
                <a href="{{ route('teacher.exams.show', $exam) }}" class="hw-row" data-search="{{ strtolower($exam->name) }}" data-term="{{ $exam->term }}">
                    <span class="hw-type-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex flex-wrap items-center gap-2">
                            <span class="hw-title truncate">{{ $exam->name }}</span>
                            @if($exam->exam_date->isFuture())
                                <span class="hw-due-pill">Upcoming</span>
                            @endif
                        </span>
                        <span class="hw-meta block">
                            {{ $exam->academic_year }} · {{ $exam->exam_date->format('d M Y') }} · {{ $exam->subjects_count }} subject{{ $exam->subjects_count === 1 ? '' : 's' }}
                        </span>
                    </span>
                    <span class="flex shrink-0 items-center gap-2">
                        <span class="ui-tag ui-tag-indigo">Term {{ $exam->term }}</span>
                    </span>
                    <svg class="hw-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                    <p>No exams created yet.</p>
                    <p class="ui-empty-sub">Create a term exam for {{ $schoolClass->name }}.</p>
                </div>
            @endforelse
            <div class="ui-empty" id="exam-no-results" style="display:none">
                <p>No exams match your filters.</p>
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const rows = document.querySelectorAll('.hw-row');
        const searchInput = document.getElementById('exam-search');
        const termFilter = document.getElementById('exam-term-filter');
        const noResults = document.getElementById('exam-no-results');
        if (!rows.length) return;

        function applyFilters() {
            const term = (searchInput?.value || '').trim().toLowerCase();
            const termValue = termFilter?.querySelector('input:checked')?.value || '';
            let visibleCount = 0;

            rows.forEach((row) => {
                const matchesSearch = !term || row.dataset.search.includes(term);
                const matchesTerm = !termValue || row.dataset.term === termValue;
                const visible = matchesSearch && matchesTerm;
                row.classList.toggle('is-hidden', !visible);
                if (visible) visibleCount++;
            });

            if (noResults) noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput?.addEventListener('input', applyFilters);
        termFilter?.addEventListener('change', applyFilters);
    })();
</script>
@endpush
@endsection
