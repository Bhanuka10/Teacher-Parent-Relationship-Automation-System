@extends('layouts.teacher')
@section('title', $recipient->message->subject)

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
    .qa-btn.ghost { background: #fff; color: var(--t-accent); border-color: #d1fae5; }
    .qa-btn.ghost:hover { background: var(--t-accent-light); border-color: var(--t-accent-mid); }

    /* ── two-column layout (matches dashboard's content-grid) ── */
    .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .content-grid { grid-template-columns: 1fr; } }

    .exam-form-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .msg-avatar-lg {
        width: 44px; height: 44px; flex-shrink: 0; border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13.5px; font-weight: 700; color: #fff;
        background: linear-gradient(135deg, #0f766e, #14b8a6);
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
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">{{ $recipient->message->subject }}</h1>
            <p class="db-sub">From <span>{{ $recipient->message->sender->name ?? 'School Administration' }}</span> · {{ $recipient->message->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.messages.index') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to messages
            </a>
        </div>
    </div>

    <div class="content-grid">
        <article class="exam-form-card">
            <div class="flex items-center gap-3 border-b border-gray-100 pb-5">
                <span class="msg-avatar-lg">{{ strtoupper(substr($recipient->message->sender->name ?? 'SA', 0, 2)) }}</span>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $recipient->message->sender->name ?? 'School Administration' }}</p>
                    <p class="text-xs text-gray-400">{{ $recipient->message->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
            <div class="mt-5 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $recipient->message->body }}</div>
        </article>

        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                    Message Details
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-row"><span class="summary-label">From</span><span class="summary-value">{{ $recipient->message->sender->name ?? 'School Administration' }}</span></div>
                <div class="summary-row"><span class="summary-label">Sent</span><span class="summary-value">{{ $recipient->message->created_at->format('d M Y, h:i A') }}</span></div>
                <div class="summary-row"><span class="summary-label">Audience</span><span class="summary-value">{{ ucfirst($recipient->message->audience) }}s</span></div>
                <div class="summary-row"><span class="summary-label">Status</span><span class="summary-value">Read</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
