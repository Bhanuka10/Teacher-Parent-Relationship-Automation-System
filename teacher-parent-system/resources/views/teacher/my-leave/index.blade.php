@extends('layouts.teacher')
@section('title', 'My Leave')

@push('styles')
<style>
    :root {
        --t-accent:       #0f766e;
        --t-accent-light: #ccfbf1;
        --t-accent-mid:   #14b8a6;
    }

    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0; }
    .db-head  {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 14px; margin-bottom: 26px;
    }

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

    /* ── KPI stat cards (always 4: Total/Pending/Approved/Rejected) ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 700px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 420px) { .kpi-grid { grid-template-columns: 1fr; } }
    .kpi-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 18px 20px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 14px;
    }
    .kpi-icon {
        width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .kpi-icon.teal   { background: var(--t-accent-light); color: var(--t-accent); }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-icon.rose   { background: #ffe4e6; color: #be123c; }
    .kpi-val   { font-size: 22px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── list ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .ml-row { display: flex; flex-direction: column; gap: 8px; padding: 16px 20px; border-bottom: 1px solid #f3f4f6; }
    .ml-row:last-child { border-bottom: none; }
    .ml-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; }
    .ml-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
    .ml-dates { font-size: 13.5px; font-weight: 700; color: #111827; }
    .ml-reason { font-size: 12.5px; color: #6b7280; }
    .ml-note {
        margin-top: 2px; font-size: 11.5px; color: #4b5563;
        background: #f9fafb; border-radius: 8px; padding: 8px 10px;
    }
    .ml-tag {
        display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 10px;
        font-size: 11px; font-weight: 700; background: #f3f4f6; color: #4b5563;
    }

    .ml-actions { display: flex; gap: 8px; flex-shrink: 0; }
    .ml-act {
        padding: 6px 14px; border-radius: 8px; font-size: 11.5px; font-weight: 700;
        text-decoration: none; border: 1px solid transparent; cursor: pointer; transition: all .15s;
    }
    .ml-act.edit { background: #fff; border-color: #e5e7eb; color: #374151; }
    .ml-act.edit:hover { background: #f9fafb; }
    .ml-act.cancel { background: #fef2f2; color: #b91c1c; }
    .ml-act.cancel:hover { background: #fee2e2; }

    .lr-status { display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0; border-radius: 20px; padding: 4px 12px; font-size: 11px; font-weight: 700; }
    .lr-status .dot { width: 5px; height: 5px; border-radius: 50%; }
    .lr-status.pending  { background: #fef3c7; color: #92400e; }
    .lr-status.pending .dot  { background: #d97706; }
    .lr-status.approved { background: #d1fae5; color: #065f46; }
    .lr-status.approved .dot { background: #10b981; }
    .lr-status.rejected { background: #fee2e2; color: #991b1b; }
    .lr-status.rejected .dot { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">My Leave</h1>
            <p class="db-sub">Request leave and track admin approval.</p>
        </div>
        <a href="{{ route('teacher.my-leave.create') }}" class="qa-btn primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Request
        </a>
    </div>

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $counts['total'] }}</div>
                <div class="kpi-label">Total Requests</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $counts['pending'] }}</div>
                <div class="kpi-label">Pending Review</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon teal">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $counts['approved'] }}</div>
                <div class="kpi-label">Approved</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon rose">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $counts['rejected'] }}</div>
                <div class="kpi-label">Rejected</div>
            </div>
        </div>
    </div>

    {{-- ── list ── --}}
    <section class="section-card">
        @forelse($leaveRequests as $leaveRequest)
            <div class="ml-row">
                <div class="ml-top">
                    <div class="ml-meta">
                        <span class="lr-status {{ $leaveRequest->status }}">
                            <span class="dot"></span>
                            {{ ucfirst($leaveRequest->status) }}
                        </span>
                        <span class="ml-dates">
                            {{ $leaveRequest->start_date->format('d M Y') }}
                            @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
                        </span>
                        <span class="ml-tag">{{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}</span>
                    </div>

                    @if($leaveRequest->isPending())
                        <div class="ml-actions">
                            <a href="{{ route('teacher.my-leave.edit', $leaveRequest) }}" class="ml-act edit">Edit</a>
                            <form method="POST" action="{{ route('teacher.my-leave.destroy', $leaveRequest) }}" onsubmit="return confirm('Cancel this leave request?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-act cancel">Cancel</button>
                            </form>
                        </div>
                    @endif
                </div>

                <p class="ml-reason">{{ $leaveRequest->reason }}</p>

                @if($leaveRequest->status !== 'pending' && $leaveRequest->review_note)
                    <p class="ml-note"><span class="font-semibold">Admin's note:</span> {{ $leaveRequest->review_note }}</p>
                @endif
            </div>
        @empty
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                <p>No leave requests yet.</p>
                <p class="ui-empty-sub">Submit one when you need time off — your admin will review it.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
