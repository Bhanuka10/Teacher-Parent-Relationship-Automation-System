@extends('layouts.teacher')
@section('title', 'Attendance History')

@push('styles')
<style>
    /* ── Attendance History page ── */
    .att-ghost-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; border: 1px solid #e5e7eb; color: #0f766e;
        font-size: .82rem; font-weight: 600; padding: 9px 16px; border-radius: 10px;
        text-decoration: none; transition: all .15s;
    }
    .att-ghost-btn:hover { background: #f0fdfa; border-color: #99f6e4; }
    .att-ghost-btn svg { width: 15px; height: 15px; }

    .att-stat-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
    .att-stat-icon {
        width: 30px; height: 30px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .att-stat-icon svg { width: 15px; height: 15px; }
    .att-stat-icon-gray  { background: #f3f4f6; color: #6b7280; }
    .att-stat-icon-green { background: #dcfce7; color: #15803d; }
    .att-stat-icon-red   { background: #fee2e2; color: #b91c1c; }
    .att-stat-icon-teal  { background: #ccfbf1; color: #0f766e; }

    .att-filter-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
        padding: 18px 20px; margin-bottom: 22px;
        display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .att-filter-field label {
        display: block; font-size: .72rem; font-weight: 600; color: #6b7280;
        text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px;
    }
    .att-filter-actions { display: flex; gap: 8px; }

    .att-edit-banner {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        background: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e;
        border-radius: 12px; padding: 12px 18px; margin-bottom: 22px; font-size: .82rem;
    }
    .att-edit-banner a {
        display: inline-flex; align-items: center; gap: 5px;
        background: #0f766e; color: #fff; font-weight: 600; font-size: .78rem;
        padding: 7px 14px; border-radius: 8px; text-decoration: none; transition: background .15s;
        flex-shrink: 0;
    }
    .att-edit-banner a:hover { background: #0d5f58; }

    .att-date-group { margin-bottom: 20px; }
    .att-date-header {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 0 4px 10px;
    }
    .att-date-label { font-size: .82rem; font-weight: 700; color: #374151; }
    .att-date-sub { font-size: .74rem; color: #9ca3af; margin-top: 1px; }

    .att-row {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        padding: 12px 20px; border-bottom: 1px solid #f3f4f6; transition: background .15s;
    }
    .att-row:last-child { border-bottom: none; }
    .att-row:hover { background: #fafafa; }
    .att-row.is-absent { background: #fef2f2; }

    .att-status-pill {
        display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0;
        border-radius: 999px; padding: 4px 11px; font-size: 11.5px; font-weight: 700;
    }
    .att-status-pill .dot { width: 6px; height: 6px; border-radius: 999px; }
    .att-status-present { background: #dcfce7; color: #15803d; }
    .att-status-present .dot { background: #22c55e; }
    .att-status-absent { background: #fee2e2; color: #b91c1c; }
    .att-status-absent .dot { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl">

    <div class="mb-7 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="ui-hero-icon ui-hero-teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Attendance History</h1>
                <p class="mt-0.5 text-sm text-gray-500">Browse and audit past attendance records for your class.</p>
            </div>
        </div>
        <a href="{{ route('teacher.attendance.index') }}" class="att-ghost-btn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z" />
            </svg>
            Mark Attendance
        </a>
    </div>

    @if(!$schoolClass)
        <div class="ui-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before viewing attendance history.</p>
            </div>
        </div>
    @else
        @php
            $totalRecords = $records->count();
            $presentCount = $records->where('status', 'present')->count();
            $absentCount = $totalRecords - $presentCount;
            $rate = $totalRecords ? round(($presentCount / $totalRecords) * 100) : 0;
            $groups = $records->groupBy(fn ($r) => $r->date->format('Y-m-d'));
        @endphp

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-5">
            <div class="ui-stat-card ui-animate-in ui-animate-in-1">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Class</p>
                    <span class="att-stat-icon att-stat-icon-gray">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="font-size:1.1rem">{{ $schoolClass->name }}</p>
            </div>
            <div class="ui-stat-card ui-animate-in ui-animate-in-2">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Present</p>
                    <span class="att-stat-icon att-stat-icon-green">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="color:#15803d">{{ $presentCount }}</p>
            </div>
            <div class="ui-stat-card ui-animate-in ui-animate-in-3">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Absent</p>
                    <span class="att-stat-icon att-stat-icon-red">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="color:#b91c1c">{{ $absentCount }}</p>
            </div>
            <div class="ui-stat-card ui-animate-in ui-animate-in-4">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Attendance Rate</p>
                    <span class="att-stat-icon att-stat-icon-teal">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="color:#0f766e">{{ $rate }}%</p>
                <span class="ui-progress mt-2">
                    <span class="ui-progress-fill" style="width:{{ $rate }}%;background:linear-gradient(90deg,#5eead4,#0f766e)"></span>
                </span>
            </div>
        </div>

        <div class="att-filter-card">
            <form method="GET" action="{{ route('teacher.attendance.history') }}" class="flex flex-wrap items-end gap-3 w-full">
                <div class="att-filter-field">
                    <label for="filter-date">Filter by Date</label>
                    <input type="date" id="filter-date" name="date" value="{{ $filterDate }}" class="ui-input" style="width:auto">
                </div>
                <div class="att-filter-actions">
                    <button type="submit" class="ui-submit-btn" style="padding:10.5px 18px">
                        <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('teacher.attendance.history') }}" class="att-ghost-btn">Reset</a>
                </div>
            </form>
        </div>

        @if($filterDate)
            <div class="att-edit-banner">
                <span>Need to make a correction? You can edit the attendance marked for <strong>{{ $filterDate }}</strong>.</span>
                <a href="{{ route('teacher.attendance.index', ['date' => $filterDate]) }}">
                    <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L20 8l-4-4L4 16v4Z"/></svg>
                    Edit this date
                </a>
            </div>
        @endif

        @forelse($groups as $dateKey => $groupRecords)
            @php
                $groupPresent = $groupRecords->where('status', 'present')->count();
                $groupTotal = $groupRecords->count();
            @endphp
            <div class="att-date-group">
                <div class="att-date-header">
                    <div>
                        <span class="att-date-label">{{ \Illuminate\Support\Carbon::parse($dateKey)->format('l, d M Y') }}</span>
                        <span class="att-date-sub block">{{ $groupTotal }} {{ Str::plural('record', $groupTotal) }}</span>
                    </div>
                    <span class="ui-tag {{ $groupPresent === $groupTotal ? 'ui-tag-teal' : 'ui-tag-gray' }}">{{ $groupPresent }}/{{ $groupTotal }} present</span>
                </div>
                <div class="ui-card overflow-hidden">
                    @foreach($groupRecords as $record)
                        @php
                            $indexNumber = $record->student->profile?->index_number ?? $record->student->admission_number;
                            $initials = collect(explode(' ', $record->student->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                            $colours = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
                            $avatarColor = $colours[ord(strtolower($record->student->name[0] ?? 'a')) % count($colours)];
                        @endphp
                        <div class="att-row {{ $record->status === 'absent' ? 'is-absent' : '' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="ui-avatar" style="background:{{ $avatarColor }};color:#fff">{{ strtoupper($initials) }}</span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-gray-800">{{ $record->student->name }}</span>
                                    <span class="block text-xs text-gray-400">Index {{ $indexNumber }}</span>
                                </span>
                            </div>
                            <span class="att-status-pill {{ $record->status === 'present' ? 'att-status-present' : 'att-status-absent' }}">
                                <span class="dot"></span>
                                {{ ucfirst($record->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="ui-card">
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p>No attendance records found.</p>
                    <p class="ui-empty-sub">{{ $filterDate ? 'Try a different date or reset the filter.' : 'Mark attendance to start building history.' }}</p>
                </div>
            </div>
        @endforelse
    @endif
</div>
@endsection
