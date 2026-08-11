@extends('layouts.teacher')
@section('title', 'Homework')

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
    .kpi-icon.rose   { background: #ffe4e6; color: #be123c; }
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

    /* ── section card / homework rows (mirrors dashboard's Recent Homework) ── */
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
        display: flex; align-items: center; justify-content: center;
    }
    .hw-type-icon svg { width: 18px; height: 18px; }
    .hw-type-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .hw-type-icon.rose   { background: #ffe4e6; color: #be123c; }

    .hw-due-pill {
        display: inline-flex; align-items: center; border-radius: 999px; padding: 2px 9px;
        font-size: 10px; font-weight: 700; letter-spacing: .02em; white-space: nowrap; flex-shrink: 0;
    }
    .hw-due-overdue { background: #fee2e2; color: #b91c1c; }
    .hw-due-soon { background: #fef3c7; color: #92400e; }

    .hw-chevron { width: 16px; height: 16px; color: #d1d5db; flex-shrink: 0; margin-top: 10px; transition: transform .15s, color .15s; }
    .hw-row:hover .hw-chevron { color: #9ca3af; transform: translateX(2px); }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Homework</h1>
            <p class="db-sub">
                Assign file submissions or quizzes to
                @if($schoolClass)
                    class <span>{{ $schoolClass->name }}</span>
                @else
                    <span>your class</span>
                @endif
            </p>
        </div>
        @if($schoolClass)
            <div class="quick-actions">
                <a href="{{ route('teacher.homework.create') }}" class="qa-btn primary">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New
                </a>
            </div>
        @endif
    </div>

    @if(!$schoolClass)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before creating homework.</p>
            </div>
        </div>
    @else
        @php
            $totalAssigned = $homeworks->count();
            $quizCount = $homeworks->where('type', 'quiz')->count();
            $fileCount = $totalAssigned - $quizCount;
            $toGradeCount = $homeworks->sum(fn ($h) => max($h->submitted_count - $h->graded_count, 0));
        @endphp

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $totalAssigned }}</div>
                    <div class="kpi-label">Total Assigned</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M4 20h16"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $fileCount }}</div>
                    <div class="kpi-label">Files</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon rose">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $quizCount }}</div>
                    <div class="kpi-label">Quizzes</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L20 8l-4-4L4 16v4Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $toGradeCount }}</div>
                    <div class="kpi-label">To Grade</div>
                </div>
            </div>
        </div>

        @if($totalAssigned)
            <div class="toolbar-card">
                <div class="att-search-wrap">
                    <label for="hw-search" class="sr-only">Search homework</label>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="text" id="hw-search" class="att-input" placeholder="Search by title…" autocomplete="off">
                </div>
                <div class="ui-tab-bar" id="hw-type-filter">
                    <label class="ui-tab-btn"><input type="radio" name="hw-type" value="" class="sr-only" checked>All</label>
                    <label class="ui-tab-btn"><input type="radio" name="hw-type" value="file" class="sr-only">Files</label>
                    <label class="ui-tab-btn"><input type="radio" name="hw-type" value="quiz" class="sr-only">Quizzes</label>
                </div>
            </div>
        @endif

        <section class="section-card">
            @forelse($homeworks as $homework)
                @php
                    $pct = $homework->total_count > 0 ? round(($homework->submitted_count / $homework->total_count) * 100) : 0;
                    $toGrade = max($homework->submitted_count - $homework->graded_count, 0);
                    $dueStatus = null;
                    if ($homework->due_at && $pct < 100) {
                        if ($homework->due_at->isPast()) {
                            $dueStatus = 'overdue';
                        } elseif ($homework->due_at->diffInHours(now()) <= 24) {
                            $dueStatus = 'soon';
                        }
                    }
                @endphp
                <a href="{{ route('teacher.homework.show', $homework) }}" class="hw-row" data-search="{{ strtolower($homework->title) }}" data-type="{{ $homework->type }}">
                    <span class="hw-type-icon {{ $homework->isQuiz() ? 'rose' : 'indigo' }}">
                        @if($homework->isQuiz())
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        @else
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M4 20h16"/></svg>
                        @endif
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex flex-wrap items-center gap-2">
                            <span class="hw-title truncate">{{ $homework->title }}</span>
                            @if($dueStatus === 'overdue')
                                <span class="hw-due-pill hw-due-overdue">Overdue</span>
                            @elseif($dueStatus === 'soon')
                                <span class="hw-due-pill hw-due-soon">Due soon</span>
                            @endif
                        </span>
                        <span class="hw-meta block">
                            {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
                            @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min @endif
                        </span>
                        <span class="ui-progress mt-2" style="max-width:240px">
                            <span class="ui-progress-fill" style="width:{{ $pct }}%"></span>
                        </span>
                    </span>
                    <span class="flex shrink-0 flex-col items-end gap-1">
                        <span class="ui-tag ui-tag-gray">{{ $homework->submitted_count }}/{{ $homework->total_count }} submitted</span>
                        @if($toGrade > 0)
                            <span class="ui-tag" style="background:#fef3c7;color:#92400e">{{ $toGrade }} to grade</span>
                        @else
                            <span class="ui-tag ui-tag-gray">{{ $homework->graded_count }} graded</span>
                        @endif
                    </span>
                    <svg class="hw-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p>No homework assigned yet.</p>
                    <p class="ui-empty-sub">Create a file assignment or quiz for {{ $schoolClass->name }}.</p>
                </div>
            @endforelse
            <div class="ui-empty" id="hw-no-results" style="display:none">
                <p>No homework matches your filters.</p>
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const rows = document.querySelectorAll('.hw-row');
        const searchInput = document.getElementById('hw-search');
        const typeFilter = document.getElementById('hw-type-filter');
        const noResults = document.getElementById('hw-no-results');
        if (!rows.length) return;

        function applyFilters() {
            const term = (searchInput?.value || '').trim().toLowerCase();
            const type = typeFilter?.querySelector('input:checked')?.value || '';
            let visibleCount = 0;

            rows.forEach((row) => {
                const matchesSearch = !term || row.dataset.search.includes(term);
                const matchesType = !type || row.dataset.type === type;
                const visible = matchesSearch && matchesType;
                row.classList.toggle('is-hidden', !visible);
                if (visible) visibleCount++;
            });

            if (noResults) noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput?.addEventListener('input', applyFilters);
        typeFilter?.addEventListener('change', applyFilters);
    })();
</script>
@endpush
@endsection
