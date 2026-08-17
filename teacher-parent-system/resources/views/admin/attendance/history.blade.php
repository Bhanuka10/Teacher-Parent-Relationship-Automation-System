@extends('layouts.admin')
@section('title', 'Attendance History')

@push('styles')
<style>
    /* ── Page header date card ── */
    .ah-header-card {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 20px;
        margin-bottom: 28px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }
    .ah-header-card .icon-wrap {
        width: 42px; height: 42px;
        background: #ede9fe;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .ah-header-card .icon-wrap svg { color: #7c3aed; }
    .ah-header-card .lbl {
        font-size: 10px; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase;
        color: #7c3aed; margin-bottom: 2px;
    }
    .ah-header-card .date-text {
        font-size: 15px; font-weight: 700; color: #1f2937;
    }

    /* ── Filter panel ── */
    .ah-filter-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 24px 24px 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .ah-filter-panel .filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
        margin-bottom: 18px;
    }
    @media(max-width:768px){ .ah-filter-panel .filter-grid{ grid-template-columns:1fr; } }
    .ah-filter-panel label {
        display: block;
        font-size: 11px; font-weight: 700;
        letter-spacing: .06em; text-transform: uppercase;
        color: #6b7280; margin-bottom: 6px;
    }
    .ah-filter-panel select,
    .ah-filter-panel input[type="date"] {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 36px 10px 14px;
        font-size: 14px;
        color: #374151;
        background-color: #fff;
        transition: border-color .15s, box-shadow .15s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        box-sizing: border-box;
    }
    .ah-filter-panel input[type="date"] {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
        color: #9ca3af;
    }
    .ah-filter-panel select:focus,
    .ah-filter-panel input[type="date"]:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
        color: #374151;
    }
    .ah-filter-actions { display: flex; justify-content: flex-end; gap: 10px; }
    .btn-reset {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 8px;
        font-size: 13px; font-weight: 600;
        border: 1px solid #e5e7eb;
        background: #fff; color: #374151;
        text-decoration: none;
        transition: background .15s, border-color .15s;
        cursor: pointer;
    }
    .btn-reset:hover { background: #f9fafb; border-color: #d1d5db; }
    .btn-search {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 20px; border-radius: 8px;
        font-size: 13px; font-weight: 600;
        background: #4f46e5; color: #fff;
        border: none; cursor: pointer;
        transition: background .15s, box-shadow .15s;
    }
    .btn-search:hover { background: #4338ca; box-shadow: 0 4px 12px rgba(79,70,229,.3); }

    /* ── Table ── */
    .ah-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .ah-table-wrap table { width: 100%; border-collapse: collapse; }
    .ah-table-wrap thead th {
        padding: 12px 18px;
        font-size: 11px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #9ca3af;
        background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
    }
    .ah-table-wrap tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .12s;
    }
    .ah-table-wrap tbody tr:last-child { border-bottom: none; }
    .ah-table-wrap tbody tr:hover { background: #fafafa; }
    .ah-table-wrap tbody td {
        padding: 14px 18px;
        font-size: 13px; color: #374151;
        vertical-align: middle;
    }
    .student-cell { display: flex; align-items: center; gap: 10px; }
    .student-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff;
        flex-shrink: 0; text-transform: uppercase;
    }
    .student-name { font-weight: 600; color: #111827; }
    .index-no { color: #6b7280; font-size: 13px; }
    .class-badge {
        display: inline-flex; align-items: center;
        background: #f3f4f6; color: #374151;
        border-radius: 6px; padding: 3px 10px;
        font-size: 12px; font-weight: 600;
    }
    .date-cell { color: #6b7280; white-space: nowrap; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 4px 12px;
        font-size: 12px; font-weight: 600;
    }
    .status-badge.present { background: #d1fae5; color: #065f46; }
    .status-badge.absent  { background: #fee2e2; color: #991b1b; }
    .status-badge.leave   { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; }
    .status-badge.present .status-dot { background: #10b981; }
    .status-badge.absent  .status-dot { background: #ef4444; }
    .status-badge.leave   .status-dot { background: #d97706; }
    .marked-cell { display: flex; align-items: center; gap: 8px; }
    .marked-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        background: #e0e7ff; color: #4338ca;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700;
        flex-shrink: 0; text-transform: uppercase;
    }
    .action-btn {
        background: none; border: none; cursor: pointer;
        color: #9ca3af; padding: 4px 8px; border-radius: 6px;
        transition: background .12s, color .12s;
        font-size: 18px; line-height: 1; letter-spacing: 1px;
    }
    .action-btn:hover { background: #f3f4f6; color: #374151; }

    /* ── Pagination footer ── */
    .ah-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px;
        border-top: 1px solid #f3f4f6;
        font-size: 13px; color: #6b7280;
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
    .page-btn.active { background: #4f46e5; border-color: #4f46e5; color: #fff; font-weight: 700; }
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

    /* ── Empty state ── */
    .ah-empty { padding: 56px 24px; text-align: center; color: #9ca3af; }
    .ah-empty svg { margin: 0 auto 12px; opacity: .35; }
    .ah-empty p { font-size: 14px; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">

    {{-- Page heading --}}
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Manage Student Records</h1>
    <p class="text-sm mb-6" style="color:#6b7280">
        View and manage student attendance records across all classes.
        Use filters to narrow down
        <span style="color:#7c3aed;cursor:pointer;font-weight:600">specific sections</span> or
        <span style="color:#7c3aed;cursor:pointer;font-weight:600">dates</span>.
    </p>

    {{-- Date card --}}
    <div class="ah-header-card">
        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="lbl">Today</div>
            <div class="date-text">{{ now()->format('M j, Y') }}</div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="ah-filter-panel">
        <form method="GET" action="{{ route('admin.attendance.history') }}" id="filter-form">
            <div class="filter-grid">
                <div>
                    <label for="grade-select">Grade</label>
                    <select name="grade" id="grade-select">
                        <option value="">All grades</option>
                        @for($g = 1; $g <= 12; $g++)
                            <option value="{{ $g }}" {{ (string) $grade === (string) $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="section-select">Section</label>
                    <select name="section" id="section-select">
                        <option value="">All sections</option>
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $sec)
                            <option value="{{ $sec }}" {{ $section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date-picker">Date</label>
                    <input type="date" name="date" id="date-picker"
                           value="{{ $date }}" placeholder="mm/dd/yyyy">
                </div>
            </div>
            <div class="ah-filter-actions">
                <a href="{{ route('admin.attendance.history') }}" class="btn-reset" id="reset-filters-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2.2">
                        <polyline points="1 4 1 10 7 10"/>
                        <path d="M3.51 15a9 9 0 1 0 .49-5"/>
                    </svg>
                    Reset Filters
                </a>
                <button type="submit" class="btn-search" id="search-records-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search Records
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="ah-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Index No.</th>
                    <th>Class</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Marked By</th>
                    <th>Actions</th>
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
                            <div class="student-cell">
                                <div class="student-avatar" style="background:{{ $avatarColor }}">
                                    {{ strtoupper(substr($student?->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="student-name">{{ $student?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="index-no">{{ $indexNumber }}</td>
                        <td><span class="class-badge">{{ $className }}</span></td>
                        <td class="date-cell">{{ $record->date->format('M j, Y') }}</td>
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
                        <td>
                            <button class="action-btn" title="More actions" id="action-btn-{{ $record->id }}">&#8942;</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="ah-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="8" y1="12" x2="16" y2="12"/>
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
