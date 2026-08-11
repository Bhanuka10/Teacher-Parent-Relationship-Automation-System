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
</style>
@endpush

@section('content')
<div class="max-w-4xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">{{ $recipient->message->subject }}</h1>
            <p class="db-sub">From <span>School Administration</span> · {{ $recipient->message->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.messages.index') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to messages
            </a>
        </div>
    </div>

    <article class="exam-form-card">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-5">
            <span class="msg-avatar-lg">SA</span>
            <div>
                <p class="text-sm font-semibold text-gray-800">School Administration</p>
                <p class="text-xs text-gray-400">{{ $recipient->message->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
        <div class="mt-5 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $recipient->message->body }}</div>
    </article>
</div>
@endsection
