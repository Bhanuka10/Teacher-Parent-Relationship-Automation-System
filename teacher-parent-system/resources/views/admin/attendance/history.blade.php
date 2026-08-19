@extends('layouts.admin')
@section('title', 'Attendance History')

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
    .stat-icon.teal   { background: #ccfbf1; color: #0f766e; }
    .stat-icon.amber  { background: #fef3c7; color: #b45309; }
    .stat-icon.rose   { background: #fee2e2; color: #991b1b; }
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
    .status-select, .mt-filter-inner input[type="date"] {
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        font-size: 13px; color: #374151;
        background: #fff;
        min-width: 150px;
    }
    .status-select:focus, .mt-filter-inner input[type="date"]:focus {
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

    /* ── Export panel ── */
    .export-panel {
        border-color: #ddd6fe;
        background: linear-gradient(180deg, #f5f3ff, #fff);
    }
    .export-title {
        display: flex; align-items: center; gap: 7px;
        font-size: 13px; font-weight: 700; color: #6d28d9;
        margin-bottom: 14px;
    }
    .export-hint { font-size: 11.5px; color: #9ca3af; margin-top: 10px; }

    /* ── Table ── */
    .mt-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        overflow-x: auto;
    }
    .mt-table-wrap table { width: 100%; border-collapse: collapse; min-width: 760px; table-layout: fixed; }
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

    /* Student name cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
        flex-shrink: 0; text-transform: uppercase;
    }
    .user-name  { font-weight: 700; color: #111827; font-size: 13px; }
    .user-email { font-size: 11px; color: #9ca3af; margin-top: 1px; }

    /* Badges */
    .class-badge {
        display: inline-flex; align-items: center;
        background: #f3f4f6; color: #374151;
        border-radius: 6px; padding: 3px 10px;
        font-size: 12px; font-weight: 600;
    }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
    }
    .status-badge.present { background: #d1fae5; color: #065f46; }
    .status-badge.absent  { background: #fee2e2; color: #991b1b; }
    .status-badge.leave   { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-badge.present .status-dot { background: #10b981; }
    .status-badge.absent  .status-dot { background: #ef4444; }
    .status-badge.leave   .status-dot { background: #d97706; }

    /* Marked-by cell */
    .marked-cell { display: flex; align-items: center; gap: 8px; }
    .marked-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        background: #e0e7ff; color: #4338ca;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700;
        flex-shrink: 0; text-transform: uppercase;
    }

    /* Empty state */
    .mt-empty { padding: 56px 24px; text-align: center; color: #9ca3af; }
    .mt-empty svg { margin: 0 auto 14px; opacity: .3; }
    .mt-empty p   { font-size: 14px; }

    /* ── Pagination footer ── */
    .ah-footer {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
        padding: 14px 18px;
        border-top: 1px solid #f3f4f6;
        font-size: 12px; color: #9ca3af;
    }
    .pagination-wrap { display: flex; align-items: center; gap: 4px; }
    .page-btn {
        width: 32px; height: 32px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff; color: #374151;
        text-decoration: none;
        transition: background .12s, border-color .12s, color .12s;
    }
    .page-btn:hover:not(.active):not(.disabled) { background: #f9fafb; }
    .page-btn.active { background: var(--accent-mid); border-color: var(--accent-mid); color: #fff; font-weight: 700; }
    .page-btn.disabled { pointer-events: none; color: #d1d5db; border-color: transparent; background: none; }
    .page-arrow {
        width: 32px; height: 32px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid #e5e7eb; background: #fff;
        text-decoration: none; color: #374151;
        font-size: 17px;
        transition: background .12s;
    }
    .page-arrow:hover:not(.disabled) { background: #f3f4f6; }
    .page-arrow.disabled { pointer-events: none; color: #d1d5db; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">

    {{-- ── Page heading ── --}}
    <div class="mt-header">
        <div>
            <h1 class="mt-title">Attendance History</h1>
            <p class="mt-sub">
                View and manage <span>student attendance records</span> across all classes.
            </p>
        </div>
    </div>

    {{-- ── Stats cards ── --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['total'] }}</div>
                <div class="stat-label">Total Records</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['present_today'] }}</div>
                <div class="stat-label">Present Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['absent_today'] }}</div>
                <div class="stat-label">Absent Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['leave_today'] }}</div>
                <div class="stat-label">On Leave Today</div>
            </div>
        </div>
    </div>

    {{-- ── Search / filter bar ── --}}
    <div class="mt-filter-bar">
        <form method="GET" action="{{ route('admin.attendance.history') }}" id="filter-form">
            <div class="mt-filter-inner">
                <div class="search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" id="search-input"
                           value="{{ $search }}"
                           placeholder="Search by student name or admission no…">
                </div>
                <select name="grade" id="grade-select" class="status-select">
                    <option value="">All grades</option>
                    @for($g = 1; $g <= 12; $g++)
                        <option value="{{ $g }}" {{ (string) $grade === (string) $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                    @endfor
                </select>
                <select name="section" id="section-select" class="status-select">
                    <option value="">All sections</option>
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $sec)
                        <option value="{{ $sec }}" {{ $section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" id="date-picker" value="{{ $date }}">
                <button type="submit" class="btn-search" id="search-records-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('admin.attendance.history') }}" class="btn-reset-sm" id="reset-filters-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ── Export panel ── --}}
    <div class="mt-filter-bar export-panel">
        <div class="export-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download attendance sheet
        </div>
        <form method="GET" action="{{ route('admin.attendance.export') }}">
            <input type="hidden" name="grade" value="{{ $grade }}">
            <input type="hidden" name="section" value="{{ $section }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <div class="mt-filter-inner">
                <input type="date" name="from" id="export-from" placeholder="From">
                <input type="date" name="to" id="export-to" placeholder="To">
                <button type="submit" class="btn-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download CSV
                </button>
            </div>
        </form>
        <p class="export-hint">Uses the Grade/Section/Search filters above, if set. Leave both dates blank to download the full history.</p>
    </div>

    {{-- ── Table ── --}}
    <div class="mt-table-wrap">
        <table id="attendance-table">
            <colgroup>
                <col style="width:32%"><col style="width:16%"><col style="width:16%">
                <col style="width:16%"><col style="width:20%">
            </colgroup>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Marked By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    @php
                        $student     = $record->student;
                        $indexNumber = $student?->profile?->index_number ?? $student?->admission_number ?? '-';
                        $className   = $student?->schoolClass?->name ?? '-';
                        $markedBy    = $record->markedBy;
                        $colours     = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
                        $avatarColor = $colours[ord(strtolower($student?->name[0] ?? 'a')) % count($colours)];
                    @endphp
                    <tr id="record-{{ $record->id }}">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background:{{ $avatarColor }}">
                                    {{ strtoupper(substr($student?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="user-name">{{ $student?->name ?? '-' }}</div>
                                    <div class="user-email">{{ $indexNumber }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="class-badge">{{ $className }}</span></td>
                        <td>{{ $record->date->format('M j, Y') }}</td>
                        <td>
                            <span class="status-badge {{ $record->status }}">
                                <span class="status-dot"></span>
                                {{ $record->status === 'leave' ? 'On Leave' : ucfirst($record->status) }}
                            </span>
                        </td>
                        <td>
                            @if($markedBy)
                                <div class="marked-cell">
                                    <div class="marked-avatar">{{ strtoupper(substr($markedBy->name, 0, 1)) }}</div>
                                    <span>{{ $markedBy->name }}</span>
                                </div>
                            @else
                                <span style="color:#d1d5db">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="mt-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                <p>No attendance records found for the selected filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer / Pagination --}}
        @if($records->total() > 0)
        <div class="ah-footer" id="table-footer">
            <span id="pagination-info">
                Showing {{ $records->firstItem() }}&ndash;{{ $records->lastItem() }} of {{ $records->total() }} records
            </span>
            <div class="pagination-wrap" id="pagination-controls">
                @if($records->onFirstPage())
                    <span class="page-arrow disabled" id="prev-page-btn">&#8249;</span>
                @else
                    <a href="{{ $records->previousPageUrl() }}" class="page-arrow" id="prev-page-btn">&#8249;</a>
                @endif

                @php
                    $lastPage    = $records->lastPage();
                    $currentPage = $records->currentPage();
                    $rangeStart  = max(1, $currentPage - 2);
                    $rangeEnd    = min($lastPage, $currentPage + 2);
                @endphp

                @if($rangeStart > 1)
                    <a href="{{ $records->url(1) }}" class="page-btn" id="page-btn-1">1</a>
                    @if($rangeStart > 2)<span class="page-btn disabled">&hellip;</span>@endif
                @endif

                @for($p = $rangeStart; $p <= $rangeEnd; $p++)
                    <a href="{{ $records->url($p) }}"
                       class="page-btn {{ $p === $currentPage ? 'active' : '' }}"
                       id="page-btn-{{ $p }}">{{ $p }}</a>
                @endfor

                @if($rangeEnd < $lastPage)
                    @if($rangeEnd < $lastPage - 1)<span class="page-btn disabled">&hellip;</span>@endif
                    <a href="{{ $records->url($lastPage) }}" class="page-btn" id="page-btn-{{ $lastPage }}">{{ $lastPage }}</a>
                @endif

                @if($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" class="page-arrow" id="next-page-btn">&#8250;</a>
                @else
                    <span class="page-arrow disabled" id="next-page-btn">&#8250;</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
