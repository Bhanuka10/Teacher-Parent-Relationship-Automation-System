@extends('layouts.admin')
@section('title', 'Leave Requests')

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
    .status-badge.active  { background: #d1fae5; color: #065f46; }
    .status-badge.pending { background: #fef9c3; color: #854d0e; }
    .status-badge.rejected { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-badge.active .status-dot   { background: #10b981; }
    .status-badge.pending .status-dot  { background: #eab308; }
    .status-badge.rejected .status-dot { background: #ef4444; }

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

    /* Info cell (truncate) */
    .info-cell { max-width: 220px; overflow: hidden; text-overflow: ellipsis; color: #6b7280; }
</style>
@endpush

@section('content')
@php
    $colours = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
@endphp

<div class="max-w-7xl">

    {{-- ── Page heading ── --}}
    <div class="mt-header">
        <div>
            <h1 class="mt-title">Leave Requests</h1>
            <p class="mt-sub">
                Read-only view of <span>student leave requests</span> and their status across all classes.
            </p>
        </div>
    </div>

    {{-- ── Stats cards ── --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['total'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['pending'] }}</div>
                <div class="stat-label">Pending Review</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $counts['rejected'] }}</div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
    </div>

    {{-- ── Search / filter bar ── --}}
    <div class="mt-filter-bar">
        <form method="GET" action="{{ route('admin.leaves.index') }}" id="search-form">
            <div class="mt-filter-inner">
                <div class="search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" id="search-input"
                           value="{{ $search }}"
                           list="leave-search-options"
                           placeholder="Search by student name or admission no…">
                    <datalist id="leave-search-options">
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
                <select name="status" class="status-select">
                    <option value="">All statuses</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="approved" @selected($status === 'approved')>Approved</option>
                    <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                </select>
                <button type="submit" class="btn-search" id="search-submit-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('admin.leaves.index') }}" class="btn-reset-sm" id="reset-search-btn">
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
        <table id="leaves-table">
            <colgroup>
                <col style="width:22%"><col style="width:10%"><col style="width:16%">
                <col style="width:10%"><col style="width:22%"><col style="width:10%"><col style="width:10%">
            </colgroup>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Dates</th>
                    <th>Duration</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveRequests as $leaveRequest)
                    @php
                        $badgeClass = match($leaveRequest->status) {
                            'approved' => 'active',
                            'rejected' => 'rejected',
                            default => 'pending',
                        };
                        $avatarColor = $colours[ord(strtolower($leaveRequest->student->name[0] ?? 'a')) % count($colours)];
                    @endphp
                    <tr id="leave-row-{{ $leaveRequest->id }}">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background:{{ $avatarColor }}">
                                    {{ strtoupper(substr($leaveRequest->student->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="user-name">{{ $leaveRequest->student->name ?? '—' }}</div>
                                    <div class="user-email">{{ $leaveRequest->student->admission_number ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($leaveRequest->schoolClass)
                                <span class="class-badge">{{ $leaveRequest->schoolClass->name }}</span>
                            @else
                                <span style="color:#d1d5db">—</span>
                            @endif
                        </td>
                        <td>
                            {{ $leaveRequest->start_date->format('d M Y') }}
                            @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
                        </td>
                        <td>{{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}</td>
                        <td class="info-cell" title="{{ $leaveRequest->reason }}">{{ $leaveRequest->reason }}</td>
                        <td>
                            <span class="status-badge {{ $badgeClass }}">
                                <span class="status-dot"></span>
                                {{ ucfirst($leaveRequest->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.leaves.show', $leaveRequest) }}" class="act-btn edit" id="view-leave-{{ $leaveRequest->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="mt-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                </svg>
                                <p>No leave requests match these filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($leaveRequests->hasPages())
        <div style="padding:12px 18px;border-top:1px solid #f3f4f6;">
            {{ $leaveRequests->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
