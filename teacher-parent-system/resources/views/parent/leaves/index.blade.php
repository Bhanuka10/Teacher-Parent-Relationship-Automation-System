@extends('layouts.parent')
@section('title', 'Leave Requests')

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

    .qa-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 10px;
        font-size: 12.5px; font-weight: 700;
        text-decoration: none; transition: all .15s;
        border: 1.5px solid transparent;
    }
    .qa-btn svg { width: 15px; height: 15px; }
    .qa-btn.primary { background: var(--p-accent); color: #fff; box-shadow: 0 4px 14px rgba(194,65,12,.28); }
    .qa-btn.primary:hover { background: #9a3412; transform: translateY(-1px); }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
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
    .kpi-icon.green  { background: #d1fae5; color: #065f46; }
    .kpi-icon.red    { background: #fee2e2; color: #991b1b; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── toolbar ── */
    .toolbar-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 14px 18px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: flex-end;
        margin-bottom: 20px;
    }

    /* ── section card / leave rows ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .lv-row {
        display: flex; flex-direction: column; gap: 10px; padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6; transition: background .12s;
    }
    .lv-row:last-child { border-bottom: none; }
    .lv-row:hover { background: #fafafa; }
    .lv-row.is-hidden { display: none; }
    @media (min-width: 640px) {
        .lv-row { flex-direction: row; align-items: flex-start; justify-content: space-between; }
    }
    .lv-dates { font-size: 13px; font-weight: 700; color: #111827; }
    .lv-reason { font-size: 12px; color: #6b7280; margin-top: 5px; }
    .lv-note {
        margin-top: 8px; border-radius: 10px; background: #f9fafb;
        padding: 8px 12px; font-size: 11.5px; color: #4b5563;
    }
    .lv-note strong { color: #111827; font-weight: 700; }
    .lv-actions { display: flex; flex-shrink: 0; gap: 8px; }
    .lv-btn {
        display: inline-flex; align-items: center; padding: 7px 14px; border-radius: 9px;
        font-size: 12px; font-weight: 700; text-decoration: none; border: 1.5px solid transparent;
        cursor: pointer; transition: all .15s;
    }
    .lv-btn.edit { background: #fff; border-color: #e5e7eb; color: #374151; }
    .lv-btn.edit:hover { background: #f9fafb; border-color: #d1d5db; }
    .lv-btn.cancel { background: #fef2f2; color: #b91c1c; }
    .lv-btn.cancel:hover { background: #fee2e2; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Leave Requests</h1>
            <p class="db-sub">
                @if($student)
                    Request leave and track approval status for <span>{{ $student->name }}</span>{{ $student->schoolClass ? ' · '.$student->schoolClass->name : '' }}
                @else
                    <span>No student linked yet</span>
                @endif
            </p>
        </div>
        @if($student)
            <a href="{{ route('parent.leaves.create') }}" class="qa-btn primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New request
            </a>
        @endif
    </div>

    @if(!$student)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                <p>No student record is linked to this account yet.</p>
                <p class="ui-empty-sub">Contact an admin to have your child linked — leave requests will appear here automatically.</p>
            </div>
        </div>
    @else
        @php
            $total = $leaveRequests->count();
            $pendingCount = $leaveRequests->where('status', 'pending')->count();
            $approvedCount = $leaveRequests->where('status', 'approved')->count();
            $rejectedCount = $leaveRequests->where('status', 'rejected')->count();
        @endphp

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon orange">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $total }}</div>
                    <div class="kpi-label">Total Requests</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $pendingCount }}</div>
                    <div class="kpi-label">Pending Review</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $approvedCount }}</div>
                    <div class="kpi-label">Approved</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="m9.5 9.5 5 5m0-5-5 5"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $rejectedCount }}</div>
                    <div class="kpi-label">Rejected</div>
                </div>
            </div>
        </div>

        @if($total)
            <div class="toolbar-card">
                <div class="ui-tab-bar" id="lv-status-filter">
                    <label class="ui-tab-btn"><input type="radio" name="lv-status" value="" class="sr-only" checked>All</label>
                    <label class="ui-tab-btn"><input type="radio" name="lv-status" value="pending" class="sr-only">Pending</label>
                    <label class="ui-tab-btn"><input type="radio" name="lv-status" value="approved" class="sr-only">Approved</label>
                    <label class="ui-tab-btn"><input type="radio" name="lv-status" value="rejected" class="sr-only">Rejected</label>
                </div>
            </div>
        @endif

        <section class="section-card">
            @forelse($leaveRequests as $index => $leaveRequest)
                @php
                    $statusClass = match($leaveRequest->status) {
                        'approved' => 'ui-status-graded',
                        'rejected' => 'ui-status-expired',
                        default => 'ui-status-pending',
                    };
                @endphp
                <div class="lv-row ui-animate-in" style="animation-delay:{{ min($index, 4) * .05 }}s" data-status="{{ $leaveRequest->status }}">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="ui-status-pill {{ $statusClass }}">{{ ucfirst($leaveRequest->status) }}</span>
                            <span class="lv-dates">
                                {{ $leaveRequest->start_date->format('d M Y') }}
                                @if(!$leaveRequest->start_date->eq($leaveRequest->end_date))
                                    – {{ $leaveRequest->end_date->format('d M Y') }}
                                @endif
                            </span>
                            <span class="ui-tag ui-tag-gray">{{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}</span>
                        </div>
                        <p class="lv-reason">{{ $leaveRequest->reason }}</p>
                        @if($leaveRequest->status !== 'pending' && $leaveRequest->review_note)
                            <p class="lv-note"><strong>Teacher's note:</strong> {{ $leaveRequest->review_note }}</p>
                        @endif
                    </div>

                    @if($leaveRequest->isPending())
                        <div class="lv-actions">
                            <a href="{{ route('parent.leaves.edit', $leaveRequest) }}" class="lv-btn edit">Edit</a>
                            <form method="POST" action="{{ route('parent.leaves.destroy', $leaveRequest) }}" onsubmit="return confirm('Cancel this leave request?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="lv-btn cancel">Cancel</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                    <p>No leave requests yet.</p>
                    <p class="ui-empty-sub">Submit one when your child needs to be away from school.</p>
                </div>
            @endforelse
            <div class="ui-empty" id="lv-no-results" style="display:none">
                <p>No leave requests match this filter.</p>
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const rows = document.querySelectorAll('.lv-row');
        const statusFilter = document.getElementById('lv-status-filter');
        const noResults = document.getElementById('lv-no-results');
        if (!rows.length || !statusFilter) return;

        function applyFilter() {
            const status = statusFilter.querySelector('input:checked')?.value || '';
            let visibleCount = 0;

            rows.forEach((row) => {
                const visible = !status || row.dataset.status === status;
                row.classList.toggle('is-hidden', !visible);
                if (visible) visibleCount++;
            });

            if (noResults) noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        statusFilter.addEventListener('change', applyFilter);
    })();
</script>
@endpush
@endsection
