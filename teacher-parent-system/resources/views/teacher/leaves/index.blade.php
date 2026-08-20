@extends('layouts.teacher')
@section('title', 'Leave Requests')

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
        transition: transform .15s, box-shadow .15s;
    }
    .kpi-card.is-active { border-color: var(--t-accent-mid); box-shadow: 0 0 0 2px var(--t-accent-light); }
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

    /* ── filter tabs ── */
    .lr-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
    .lr-tab {
        padding: 8px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700;
        text-decoration: none; background: #f3f4f6; color: #6b7280; transition: all .15s;
    }
    .lr-tab:hover { background: #e5e7eb; }
    .lr-tab.active { background: var(--t-accent); color: #fff; box-shadow: 0 4px 14px rgba(15,118,110,.25); }

    /* ── list ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .lr-row {
        display: flex; align-items: center; gap: 12px; padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background .12s;
    }
    .lr-row:last-child { border-bottom: none; }
    .lr-row:hover { background: #fafafa; }
    .lr-av {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff;
    }
    .lr-name { font-size: 13.5px; font-weight: 700; color: #111827; }
    .lr-meta { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }

    .lr-status {
        display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0;
        border-radius: 20px; padding: 4px 12px; font-size: 11px; font-weight: 700;
    }
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
@php
    $avatarColours = ['#0f766e','#6366f1','#8b5cf6','#ec4899','#f59e0b','#0ea5e9','#ef4444','#14b8a6'];
@endphp

<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Leave Requests</h1>
            <p class="db-sub">Review and decide leave requests from your class.</p>
        </div>
    </div>

    @if(!$schoolClass)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned.</p>
            </div>
        </div>
    @else
        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card {{ !$status ? 'is-active' : '' }}">
                <div class="kpi-icon indigo">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $counts['total'] }}</div>
                    <div class="kpi-label">Total Requests</div>
                </div>
            </div>

            <div class="kpi-card {{ $status === 'pending' ? 'is-active' : '' }}">
                <div class="kpi-icon amber">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $counts['pending'] }}</div>
                    <div class="kpi-label">Pending Review</div>
                </div>
            </div>

            <div class="kpi-card {{ $status === 'approved' ? 'is-active' : '' }}">
                <div class="kpi-icon teal">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $counts['approved'] }}</div>
                    <div class="kpi-label">Approved</div>
                </div>
            </div>

            <div class="kpi-card {{ $status === 'rejected' ? 'is-active' : '' }}">
                <div class="kpi-icon rose">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $counts['rejected'] }}</div>
                    <div class="kpi-label">Rejected</div>
                </div>
            </div>
        </div>

        {{-- ── filter tabs ── --}}
        <div class="lr-tabs">
            @foreach(['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                <a href="{{ route('teacher.leaves.index', $value ? ['status' => $value] : []) }}"
                   class="lr-tab {{ (string) $status === $value ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- ── list ── --}}
        <section class="section-card">
            @forelse($leaveRequests as $leaveRequest)
                @php
                    $initials = collect(explode(' ', $leaveRequest->student->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                    $aColor = $avatarColours[ord(strtolower($leaveRequest->student->name[0] ?? 'a')) % count($avatarColours)];
                @endphp
                <a href="{{ route('teacher.leaves.show', $leaveRequest) }}" class="lr-row">
                    <div class="lr-av" style="background:{{ $aColor }}">{{ strtoupper($initials) }}</div>
                    <span class="min-w-0 flex-1">
                        <span class="lr-name block truncate">{{ $leaveRequest->student->name }}</span>
                        <span class="lr-meta block">
                            {{ $leaveRequest->start_date->format('d M Y') }}
                            @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
                            · {{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}
                        </span>
                    </span>
                    <span class="lr-status {{ $leaveRequest->status }}">
                        <span class="dot"></span>
                        {{ ucfirst($leaveRequest->status) }}
                    </span>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                    <p>No leave requests{{ $status ? ' with this status' : '' }}.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
