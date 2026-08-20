@extends('layouts.teacher')
@section('title', 'Edit Leave Request')

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

    /* ── two-column layout (matches dashboard's content-grid) ── */
    .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .content-grid { grid-template-columns: 1fr; } }

    /* ── form card ── */
    .form-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    /* ── sidebar summary card ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }
    .summary-body { padding: 6px 20px 18px; }
    .summary-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 12.5px; gap: 10px;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { color: #9ca3af; font-weight: 600; flex-shrink: 0; }
    .summary-value { color: #111827; font-weight: 700; text-align: right; }
    .tips-box {
        margin: 14px 20px 20px; background: var(--t-accent-light); border: 1px solid #99f6e4;
        border-radius: 10px; padding: 12px 14px; font-size: 11.5px; color: #0f766e; line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Edit Leave Request</h1>
            <p class="db-sub">Still pending — you can update the dates or reason before the admin reviews it.</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.my-leave.index') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to my leave
            </a>
        </div>
    </div>

    <div class="content-grid">
        <form method="POST" action="{{ route('teacher.my-leave.update', $teacherLeaveRequest) }}" class="space-y-5" id="leave-form">
            @csrf
            @method('PUT')

            <section class="form-card">
                @include('leaves._form', ['leaveRequest' => $teacherLeaveRequest])
            </section>

            <button type="submit" class="qa-btn primary w-full justify-center" style="width:100%;padding:12px;font-size:13.5px">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                Save changes
            </button>
        </form>

        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                    Leave Summary
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-row"><span class="summary-label">Status</span><span class="summary-value"><span class="ui-status-pill ui-status-pending">Pending</span></span></div>
                <div class="summary-row"><span class="summary-label">Requested By</span><span class="summary-value">{{ auth()->user()->name }}</span></div>
                <div class="summary-row"><span class="summary-label">Dates</span><span class="summary-value" id="lv-summary-dates">Not set</span></div>
                <div class="summary-row"><span class="summary-label">Days Requested</span><span class="summary-value" id="lv-summary-days">—</span></div>
            </div>
            <div class="tips-box">
                Tip: you'll be notified once the admin reviews this request. It can only be edited while it's still pending.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('leave-form');
    const startInput = document.getElementById('lr-start-date');
    const endInput = document.getElementById('lr-end-date');
    if (!form || !startInput || !endInput) return;

    function formatDate(value) {
        if (!value) return null;
        const date = new Date(value + 'T00:00:00');
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function updateSummary() {
        const start = startInput.value;
        const end = endInput.value;
        const datesEl = document.getElementById('lv-summary-dates');
        const daysEl = document.getElementById('lv-summary-days');

        if (!start) {
            datesEl.textContent = 'Not set';
            daysEl.textContent = '—';
            return;
        }

        datesEl.textContent = end && end !== start
            ? `${formatDate(start)} – ${formatDate(end)}`
            : formatDate(start);

        if (end) {
            const diff = Math.round((new Date(end) - new Date(start)) / 86400000) + 1;
            daysEl.textContent = diff > 0 ? `${diff} day${diff === 1 ? '' : 's'}` : '—';
        } else {
            daysEl.textContent = '1 day';
        }
    }

    form.addEventListener('input', updateSummary);
    form.addEventListener('change', updateSummary);
    updateSummary();
})();
</script>
@endpush
