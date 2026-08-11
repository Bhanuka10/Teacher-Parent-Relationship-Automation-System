@extends('layouts.parent')
@section('title', 'Homework')

@push('styles')
<style>
    :root {
        --p-accent:       #c2410c;
        --p-accent-light: #ffedd5;
        --p-accent-mid:   #f97316;
    }

    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0; }
    .db-sub span { color: var(--p-accent); font-weight: 600; }

    .db-head {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 14px; margin-bottom: 26px;
    }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
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
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.green  { background: #d1fae5; color: #065f46; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .kpi-note  { font-size: 10.5px; color: #9ca3af; margin-top: 3px; }

    /* ── toolbar ── */
    .toolbar-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 14px 18px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;
        margin-bottom: 20px;
    }
    .hw-search-input {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 9px 13px;
        font-size: 13px; color: #111827; background: #fafafa; outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .hw-search-input:focus { border-color: var(--p-accent-mid); box-shadow: 0 0 0 3px rgba(249,115,22,.15); background: #fff; }
    .hw-search-wrap { position: relative; flex: 1; min-width: 200px; }
    .hw-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #9ca3af; }
    .hw-search-wrap input { width: 100%; padding-left: 32px; box-sizing: border-box; }

    /* ── section card / homework rows ── */
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
            <h1 class="db-title">Homework</h1>
            <p class="db-sub">
                @if($student)
                    File assignments and quizzes for <span>{{ $student->name }}</span>{{ $student->schoolClass ? ' · '.$student->schoolClass->name : '' }}
                @else
                    <span>No student linked yet</span>
                @endif
            </p>
        </div>
    </div>

    @if(!$student)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p>No student record is linked to this account yet.</p>
                <p class="ui-empty-sub">Contact an admin to have your child linked — homework will appear here automatically.</p>
            </div>
        </div>
    @else
        @php
            $total = $submissions->count();
            $pendingCount = $submissions->filter(fn ($s) => $s->status() === 'pending')->count();
            $submittedCount = $submissions->filter(fn ($s) => $s->status() === 'submitted')->count();
            $gradedSubs = $submissions->filter(fn ($s) => $s->status() === 'graded');
            $gradedCount = $gradedSubs->count();
            $avgPct = $gradedSubs->isNotEmpty()
                ? round($gradedSubs->avg(fn ($s) => $s->homework->max_marks > 0 ? ($s->marks / $s->homework->max_marks) * 100 : 0))
                : null;
        @endphp

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon orange">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $total }}</div>
                    <div class="kpi-label">Total Assigned</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $pendingCount }}</div>
                    <div class="kpi-label">Not Submitted</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M4 20h16"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $submittedCount }}</div>
                    <div class="kpi-label">Awaiting Grading</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $gradedCount }}</div>
                    <div class="kpi-label">Graded</div>
                    @if($avgPct !== null)
                        <div class="kpi-note">{{ $avgPct }}% average</div>
                    @endif
                </div>
            </div>
        </div>

        @if($total)
            <div class="toolbar-card">
                <div class="hw-search-wrap">
                    <label for="hw-search" class="sr-only">Search homework</label>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="text" id="hw-search" class="hw-search-input" placeholder="Search by title…" autocomplete="off">
                </div>
                <div class="ui-tab-bar" id="hw-status-filter">
                    <label class="ui-tab-btn"><input type="radio" name="hw-status" value="" class="sr-only" checked>All</label>
                    <label class="ui-tab-btn"><input type="radio" name="hw-status" value="pending" class="sr-only">Not submitted</label>
                    <label class="ui-tab-btn"><input type="radio" name="hw-status" value="submitted" class="sr-only">Submitted</label>
                    <label class="ui-tab-btn"><input type="radio" name="hw-status" value="graded" class="sr-only">Graded</label>
                    <label class="ui-tab-btn"><input type="radio" name="hw-status" value="expired" class="sr-only">Expired</label>
                </div>
            </div>
        @endif

        <section class="section-card">
            @forelse($submissions as $submission)
                @php
                    $homework = $submission->homework;
                    $status = $submission->status();
                    $statusLabel = ['pending' => 'Not submitted', 'submitted' => 'Submitted', 'graded' => 'Graded', 'expired' => 'Expired'][$status];
                    $dueSoon = $status === 'pending' && $homework->due_at && $homework->due_at->diffInHours(now()) <= 24;
                @endphp
                <a href="{{ route('parent.homework.show', $homework) }}" class="hw-row" data-search="{{ strtolower($homework->title) }}" data-status="{{ $status }}">
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
                            @if($dueSoon)
                                <span class="hw-due-pill">Due soon</span>
                            @endif
                        </span>
                        <span class="hw-meta block">
                            {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
                            @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min @endif
                        </span>
                    </span>
                    <span class="flex shrink-0 flex-col items-end gap-1">
                        <span class="ui-status-pill ui-status-{{ $status }}">{{ $statusLabel }}</span>
                        @if($submission->isGraded())
                            <span class="ui-tag ui-tag-orange">{{ $submission->marks }}/{{ $homework->max_marks }}</span>
                        @endif
                    </span>
                    <svg class="hw-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p>No homework assigned yet.</p>
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
        const statusFilter = document.getElementById('hw-status-filter');
        const noResults = document.getElementById('hw-no-results');
        if (!rows.length) return;

        function applyFilters() {
            const term = (searchInput?.value || '').trim().toLowerCase();
            const status = statusFilter?.querySelector('input:checked')?.value || '';
            let visibleCount = 0;

            rows.forEach((row) => {
                const matchesSearch = !term || row.dataset.search.includes(term);
                const matchesStatus = !status || row.dataset.status === status;
                const visible = matchesSearch && matchesStatus;
                row.classList.toggle('is-hidden', !visible);
                if (visible) visibleCount++;
            });

            if (noResults) noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput?.addEventListener('input', applyFilters);
        statusFilter?.addEventListener('change', applyFilters);
    })();
</script>
@endpush
@endsection
