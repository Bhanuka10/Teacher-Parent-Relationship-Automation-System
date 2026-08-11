@extends('layouts.teacher')
@section('title', 'Attendance History')

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
    .kpi-icon.rose   { background: #ffe4e6; color: #be123c; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-val.sm { font-size: 17px; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── toolbar / filter ── */
    .toolbar-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 18px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
        margin-bottom: 20px;
    }
    .toolbar-field label {
        display: block; font-size: 10.5px; font-weight: 700; color: #9ca3af;
        text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px;
    }
    .att-input {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 9px 13px;
        font-size: 13px; color: #111827; background: #fafafa; outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .att-input:focus { border-color: var(--t-accent-mid); box-shadow: 0 0 0 3px rgba(20,184,166,.15); background: #fff; }
    .toolbar-actions { display: flex; gap: 8px; }

    .due-banner {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        background: var(--t-accent-light); border: 1px solid #99f6e4;
        border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        font-size: 12.5px; color: #0f766e;
    }
    .due-banner strong { font-weight: 800; }
    .due-banner a {
        display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0;
        background: var(--t-accent); color: #fff; font-weight: 700; font-size: 11.5px;
        padding: 7px 14px; border-radius: 8px; text-decoration: none; transition: background .15s;
    }
    .due-banner a:hover { background: #0d5f58; }

    /* ── section card / date groups ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .date-group { margin-bottom: 18px; }
    .date-group-head {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 0 4px 10px;
    }
    .date-group-label { font-size: 13px; font-weight: 800; color: #111827; }
    .date-group-sub { font-size: 11px; color: #9ca3af; margin-top: 1px; }
    .date-group-tag {
        display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 11px;
        font-size: 11px; font-weight: 700; background: #f3f4f6; color: #6b7280;
    }
    .date-group-tag.full { background: var(--t-accent-light); color: var(--t-accent); }

    .roster-row {
        display: flex; align-items: center; gap: 10px; padding: 11px 20px;
        border-bottom: 1px solid #f3f4f6;
    }
    .roster-row:last-child { border-bottom: none; }
    .roster-av {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 11.5px; font-weight: 700; color: #fff;
    }
    .roster-nm { font-size: 12.5px; font-weight: 600; color: #111827; }
    .roster-adm { font-size: 10.5px; color: #9ca3af; }

    .status-dot {
        display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0; margin-left: auto;
        border-radius: 20px; padding: 3px 10px; font-size: 10.5px; font-weight: 700;
    }
    .status-dot.present { background: #d1fae5; color: #065f46; }
    .status-dot.absent  { background: #fee2e2; color: #991b1b; }
    .dot { width: 5px; height: 5px; border-radius: 50%; }
    .status-dot.present .dot { background: #10b981; }
    .status-dot.absent  .dot { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Attendance History</h1>
            <p class="db-sub">
                @if($schoolClass)
                    Browse past records for class <span>{{ $schoolClass->name }}</span>
                @else
                    <span>No class assigned yet</span>
                @endif
            </p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.attendance.index') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                Mark Attendance
            </a>
        </div>
    </div>

    @if(!$schoolClass)
        <div class="section-card">
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

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="kpi-val sm">{{ $schoolClass->name }}</div>
                    <div class="kpi-label">Class</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $presentCount }}</div>
                    <div class="kpi-label">Present</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon rose">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $absentCount }}</div>
                    <div class="kpi-label">Absent</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $rate }}%</div>
                    <div class="kpi-label">Attendance Rate</div>
                </div>
            </div>
        </div>

        {{-- ── filter ── --}}
        <div class="toolbar-card">
            <form method="GET" action="{{ route('teacher.attendance.history') }}" class="flex flex-wrap items-end gap-3 w-full">
                <div class="toolbar-field">
                    <label for="filter-date">Filter by Date</label>
                    <input type="date" id="filter-date" name="date" value="{{ $filterDate }}" class="att-input">
                </div>
                <div class="toolbar-actions">
                    <button type="submit" class="qa-btn primary">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('teacher.attendance.history') }}" class="qa-btn ghost">Reset</a>
                </div>
            </form>
        </div>

        @if($filterDate)
            <div class="due-banner">
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
            <div class="date-group">
                <div class="date-group-head">
                    <div>
                        <div class="date-group-label">{{ \Illuminate\Support\Carbon::parse($dateKey)->format('l, d M Y') }}</div>
                        <div class="date-group-sub">{{ $groupTotal }} {{ Str::plural('record', $groupTotal) }}</div>
                    </div>
                    <span class="date-group-tag {{ $groupPresent === $groupTotal ? 'full' : '' }}">{{ $groupPresent }}/{{ $groupTotal }} present</span>
                </div>
                <div class="section-card">
                    @foreach($groupRecords as $record)
                        @php
                            $indexNumber = $record->student->profile?->index_number ?? $record->student->admission_number;
                            $avatarColours = ['#0f766e','#6366f1','#8b5cf6','#ec4899','#f59e0b','#0ea5e9','#ef4444','#14b8a6'];
                            $aColor = $avatarColours[ord(strtolower($record->student->name[0] ?? 'a')) % count($avatarColours)];
                        @endphp
                        <div class="roster-row">
                            <div class="roster-av" style="background:{{ $aColor }}">{{ strtoupper(substr($record->student->name, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <div class="roster-nm truncate">{{ $record->student->name }}</div>
                                <div class="roster-adm">Index {{ $indexNumber }}</div>
                            </div>
                            <span class="status-dot {{ $record->status === 'present' ? 'present' : 'absent' }}">
                                <span class="dot"></span>
                                {{ ucfirst($record->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="section-card">
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
