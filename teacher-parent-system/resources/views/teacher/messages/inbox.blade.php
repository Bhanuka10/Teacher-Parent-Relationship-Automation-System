@extends('layouts.teacher')
@section('title', 'Messages')

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
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-icon.gray   { background: #f3f4f6; color: #6b7280; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── section card / message rows (mirrors dashboard's Recent Homework) ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .hw-row {
        display: flex; align-items: flex-start; gap: 12px; padding: 15px 20px;
        border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background .12s;
        border-left: 3px solid transparent;
    }
    .hw-row:last-child { border-bottom: none; }
    .hw-row:hover { background: #fafafa; }
    .hw-row.is-unread { background: #f0fdfa; border-left-color: var(--t-accent-mid); }
    .hw-row.is-unread:hover { background: #e7fbf7; }
    .hw-title { font-size: 13px; font-weight: 700; color: #111827; }
    .hw-row.is-unread .hw-title { font-weight: 800; }
    .hw-meta  { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }
    .msg-preview {
        font-size: 12.5px; color: #6b7280; margin-top: 3px;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
    }

    .msg-avatar {
        width: 38px; height: 38px; flex-shrink: 0; border-radius: 999px; margin-top: 1px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff;
        background: linear-gradient(135deg, #0f766e, #14b8a6);
    }
    .msg-unread-dot {
        width: 7px; height: 7px; border-radius: 999px; background: var(--t-accent); flex-shrink: 0;
        animation: msg-pulse 2s ease-in-out infinite;
    }
    @keyframes msg-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }

    .hw-chevron { width: 16px; height: 16px; color: #d1d5db; flex-shrink: 0; margin-top: 10px; transition: transform .15s, color .15s; }
    .hw-row:hover .hw-chevron { color: #9ca3af; transform: translateX(2px); }

    @media (prefers-reduced-motion: reduce) {
        .msg-unread-dot { animation: none; }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Messages</h1>
            <p class="db-sub">Notices sent by the school <span>administration</span>.</p>
        </div>
    </div>

    @php
        $totalCount = $recipients->total();
        $readCount = max($totalCount - $unreadCount, 0);
    @endphp

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon gray">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $totalCount }}</div>
                <div class="kpi-label">Total Messages</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4l2.5 2.5"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $unreadCount }}</div>
                <div class="kpi-label">Unread</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon teal">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $readCount }}</div>
                <div class="kpi-label">Read</div>
            </div>
        </div>
    </div>

    <section class="section-card">
        @forelse($recipients as $recipient)
            @php($unread = !$recipient->read_at)
            <a href="{{ route('teacher.messages.show', $recipient) }}" class="hw-row {{ $unread ? 'is-unread' : '' }}">
                <span class="msg-avatar">SA</span>
                <span class="min-w-0 flex-1">
                    <span class="flex items-center gap-2">
                        <span class="hw-title truncate">{{ $recipient->message->subject }}</span>
                        @if($unread)<span class="msg-unread-dot"></span>@endif
                    </span>
                    <span class="msg-preview">{{ Str::limit(strip_tags($recipient->message->body), 110) }}</span>
                    <span class="hw-meta">School administration · {{ $recipient->created_at->diffForHumans() }}</span>
                </span>
                <svg class="hw-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
            </a>
        @empty
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                <p>You have no messages yet.</p>
                <p class="ui-empty-sub">Notices from the school administration will show up here.</p>
            </div>
        @endforelse
    </section>

    @if($recipients->hasPages())<div class="mt-5">{{ $recipients->links() }}</div>@endif
</div>
@endsection
