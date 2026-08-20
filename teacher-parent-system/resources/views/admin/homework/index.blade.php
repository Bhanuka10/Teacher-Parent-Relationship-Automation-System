@extends('layouts.admin')
@section('title', 'Homework')

@push('styles')
<style>
    /* ── Shared tokens ── */
    :root {
        --accent: #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid: #4f46e5;
    }

    /* ── Page header row ── */
    .mt-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
    .mt-title   { font-size: 22px; font-weight: 800; color: #111827; margin: 0; }
    .mt-sub     { font-size: 13px; color: #6b7280; margin: 4px 0 24px; }
    .mt-sub span { color: var(--accent); font-weight: 600; }

    /* ── Stats card ── */
    .stats-strip {
        display: flex; gap: 16px; flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.purple { background: var(--accent-light); color: var(--accent); }
    .stat-icon.rose   { background: #fee2e2; color: #991b1b; }
    .stat-icon.teal   { background: #ccfbf1; color: #0f766e; }
    .stat-value { font-size: 20px; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

    /* ── Search / filter bar ── */
    .mt-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .mt-filter-inner {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; pointer-events: none;
    }
    .search-wrap input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        font-size: 13px; color: #374151;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .search-wrap input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    .search-wrap input::placeholder { color: #9ca3af; }
    .status-select {
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        font-size: 13px; color: #374151;
        background: #fff;
        min-width: 150px;
    }
    .status-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    .btn-search {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: var(--accent-mid); color: #fff;
        border: none; cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-search:hover { background: #4338ca; }
    .btn-reset-sm {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 16px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-reset-sm:hover { background: #f9fafb; }

    /* ── Table ── */
    .mt-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        overflow-x: auto;
    }
    .mt-table-wrap table { width: 100%; border-collapse: collapse; min-width: 820px; table-layout: fixed; }
    .mt-table-wrap thead th {
        padding: 12px 18px;
        font-size: 11px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #9ca3af;
        background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
        text-align: left;
    }
    .mt-table-wrap tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .12s;
    }
    .mt-table-wrap tbody tr:last-child { border-bottom: none; }
    .mt-table-wrap tbody tr:hover { background: #fafafa; }
    .mt-table-wrap tbody td {
        padding: 14px 18px;
        font-size: 13px; color: #374151;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Homework title cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff;
        flex-shrink: 0;
    }
    .user-avatar.quiz { background: #ef4444; }
    .user-avatar.file { background: var(--accent-mid); }
    .user-name  { font-weight: 700; color: #111827; font-size: 13px; }
    .user-email { font-size: 11px; color: #9ca3af; margin-top: 1px; }

    /* Badges */
    .class-badge {
        display: inline-flex; align-items: center;
        background: #f3f4f6; color: #374151;
        border-radius: 6px; padding: 3px 10px;
        font-size: 12px; font-weight: 600;
    }
    .hw-chip {
        display: inline-flex; align-items: center;
        background: #f3f4f6; color: #374151;
        border-radius: 5px; padding: 2px 8px;
        font-size: 11px; font-weight: 600;
        white-space: nowrap;
    }
    .progress-cell {
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }
    .progress-stack { display: flex; flex-direction: column; gap: 4px; align-items: flex-start; }

    /* Action buttons */
    .act-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 7px;
        font-size: 12px; font-weight: 600;
        text-decoration: none;
        transition: background .12s;
        border: none; cursor: pointer;
    }
    .act-btn.edit   { background: #ede9fe; color: #6d28d9; }
    .act-btn.edit:hover { background: #ddd6fe; }

    /* Empty state */
    .mt-empty { padding: 56px 24px; text-align: center; color: #9ca3af; }
    .mt-empty svg { margin: 0 auto 14px; opacity: .3; }
    .mt-empty p   { font-size: 14px; }

    /* Pagination */
    .mt-pagination { padding: 12px 18px; border-top: 1px solid #f3f4f6; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">

    {{-- ── Page heading ── --}}
    <div class="mt-header">
        <div>
            <h1 class="mt-title">Homework</h1>
            <p class="mt-sub">
                Read-only view of <span>homework and quizzes</span> assigned across all classes.
            </p>
        </div>
    </div>

    {{-- ── Stats cards ── --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['total'] }}</div>
                <div class="stat-label">Total Homework</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['quizzes'] }}</div>
                <div class="stat-label">Quizzes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['files'] }}</div>
                <div class="stat-label">File Submissions</div>
            </div>
        </div>
    </div>

    {{-- ── Search / filter bar ── --}}
    <div class="mt-filter-bar">
        <form method="GET" action="{{ route('admin.homework.index') }}" id="search-form">
            <div class="mt-filter-inner">
                <div class="search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" id="search-input"
                           value="{{ $search }}"
                           list="homework-search-options"
                           placeholder="Search by homework title…">
                    <datalist id="homework-search-options">
                        @foreach($searchOptions as $option)
                            <option value="{{ $option }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <select name="class_id" class="status-select">
                    <option value="">All classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                <select name="type" class="status-select">
                    <option value="">All types</option>
                    <option value="file" @selected($type === 'file')>File submission</option>
                    <option value="quiz" @selected($type === 'quiz')>Quiz</option>
                </select>
                <button type="submit" class="btn-search" id="search-submit-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('admin.homework.index') }}" class="btn-reset-sm" id="reset-search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ── Table ── --}}
    <div class="mt-table-wrap">
        <table id="homework-table">
            <colgroup>
                <col style="width:28%"><col style="width:14%"><col style="width:16%">
                <col style="width:14%"><col style="width:16%"><col style="width:12%">
            </colgroup>
            <thead>
                <tr>
                    <th>Homework</th>
                    <th>Class</th>
                    <th>Teacher</th>
                    <th>Due Date</th>
                    <th>Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($homeworks as $homework)
                    @php
                        $isQuiz = $homework->type === 'quiz';
                    @endphp
                    <tr id="homework-row-{{ $homework->id }}">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar {{ $isQuiz ? 'quiz' : 'file' }}">{{ $isQuiz ? 'Q' : 'F' }}</div>
                                <div class="min-w-0">
                                    <div class="user-name">{{ $homework->title }}</div>
                                    <div class="user-email">{{ $isQuiz ? 'Quiz' : 'File submission' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($homework->schoolClass)
                                <span class="class-badge">{{ $homework->schoolClass->name }}</span>
                            @else
                                <span style="color:#d1d5db">—</span>
                            @endif
                        </td>
                        <td>{{ $homework->teacher?->name ?? '—' }}</td>
                        <td>{{ $homework->due_at ? $homework->due_at->format('d M Y') : '—' }}</td>
                        <td class="progress-cell">
                            <div class="progress-stack">
                                <span class="hw-chip">{{ $homework->submitted_count }}/{{ $homework->total_count }} submitted</span>
                                <span class="hw-chip">{{ $homework->graded_count }} graded</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.homework.show', $homework) }}" class="act-btn edit" id="view-homework-{{ $homework->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="mt-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                <p>No homework matches these filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($homeworks->hasPages())
        <div class="mt-pagination">
            {{ $homeworks->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
