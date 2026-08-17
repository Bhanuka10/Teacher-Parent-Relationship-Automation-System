@extends('layouts.admin')
@section('title', 'Message read status')

@section('content')
@php
    $totalRecipients = $message->recipients->count();
    $readRecipients = $message->recipients->whereNotNull('read_at')->count();
    $pct = $totalRecipients > 0 ? round(($readRecipients / $totalRecipients) * 100) : 0;
    $isTeacherMsg = $message->audience === 'teacher';
@endphp
<div class="msg-page mx-auto max-w-4xl">
    <a href="{{ route('admin.messages.index') }}" class="msg-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to messages
    </a>

    <section class="msg-detail-card mt-4">
        <div class="msg-detail-header">
            <span class="msg-tag {{ $isTeacherMsg ? 'msg-tag-teacher' : 'msg-tag-student' }}">{{ $isTeacherMsg ? 'Teacher message' : 'Student message' }}</span>
            <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $message->subject }}</h1>
            <p class="mt-1.5 text-sm text-gray-500">
                Sent {{ $message->created_at->format('d M Y, h:i A') }} by {{ $message->sender->name ?? 'Admin' }}
                {{ $message->target_class_ids ? '· Selected classes' : '· All classes' }}
            </p>

            <div class="mt-5 flex items-center gap-3">
                <span class="msg-progress flex-1">
                    <span class="msg-progress-fill" style="width:{{ $pct }}%"></span>
                </span>
                <span class="whitespace-nowrap text-xs font-semibold text-gray-600">{{ $readRecipients }}/{{ $totalRecipients }} read · {{ $pct }}%</span>
            </div>

            <p class="mt-5 whitespace-pre-line border-t border-gray-100 pt-5 text-sm leading-7 text-gray-700">{{ $message->body }}</p>
        </div>
    </section>

    <section class="msg-detail-card mt-6 overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800">Recipient read status</h2>
            <div class="msg-search-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="recipient-search" placeholder="Filter by name or email…">
            </div>
        </div>

        <div id="recipient-list">
            @foreach($message->recipients->sortBy('user.name') as $recipient)
                @php
                    $classes = $recipient->user->isTeacher()
                        ? collect([$recipient->user->schoolClass])->filter()
                        : $recipient->user->students->pluck('schoolClass')->filter();
                    $initials = collect(explode(' ', $recipient->user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                @endphp
                <div class="msg-recipient-row" data-search="{{ mb_strtolower($recipient->user->name.' '.$recipient->user->email) }}">
                    <span class="msg-avatar {{ $isTeacherMsg ? 'msg-avatar-teacher' : 'msg-avatar-student' }}">{{ strtoupper($initials) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $recipient->user->name }}</p>
                        <p class="mt-0.5 wrap-break-word text-xs text-gray-500">{{ $recipient->user->email }}@if($classes->isNotEmpty()) · {{ $classes->pluck('name')->unique()->join(', ') }}@endif</p>
                    </div>
                    @if($recipient->read_at)
                        <span class="msg-status-pill msg-status-read">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Read {{ $recipient->read_at->format('d M, h:i A') }}
                        </span>
                    @else
                        <span class="msg-status-pill msg-status-unread">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Not read
                        </span>
                    @endif
                </div>
            @endforeach
            <p id="recipient-empty" class="hidden px-6 py-10 text-center text-sm text-gray-400">No recipients match your search.</p>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .msg-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--accent); text-decoration: none; transition: color .15s;
    }
    .msg-back:hover { color: var(--accent-mid); }
    .msg-back svg { width: 14px; height: 14px; }

    .msg-detail-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .msg-detail-header { padding: 26px 28px; }

    .msg-tag {
        display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 10px;
        font-size: 11px; font-weight: 700; letter-spacing: .02em;
    }
    .msg-tag-teacher { background: #e0e7ff; color: #4338ca; }
    .msg-tag-student { background: #ffedd5; color: #c2410c; }

    .msg-progress {
        display: block; height: 7px; border-radius: 999px; background: #f3f4f6; overflow: hidden;
    }
    .msg-progress-fill {
        display: block; height: 100%; border-radius: 999px;
        background: linear-gradient(90deg, var(--accent-mid), var(--accent));
        transition: width .5s ease;
    }

    .msg-search-wrap { position: relative; width: 220px; max-width: 100%; }
    .msg-search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #9ca3af; }
    .msg-search-wrap input {
        width: 100%; padding: 7px 10px 7px 30px; border: 1px solid #e5e7eb; border-radius: 8px;
        font-size: 12.5px; color: #374151; outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .msg-search-wrap input:focus { border-color: var(--accent-mid); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }

    .msg-recipient-row {
        display: flex; align-items: center; gap: 12px;
        border-bottom: 1px solid #f3f4f6; padding: 14px 24px; transition: background .12s;
    }
    .msg-recipient-row:last-child { border-bottom: none; }
    .msg-recipient-row:hover { background: #fafafa; }

    .msg-avatar {
        width: 34px; height: 34px; flex-shrink: 0; border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
    }
    .msg-avatar-teacher { background: #e0e7ff; color: #4338ca; }
    .msg-avatar-student { background: #ffedd5; color: #c2410c; }

    .msg-status-pill {
        display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;
        border-radius: 999px; padding: 4px 10px; font-size: 11.5px; font-weight: 700; white-space: nowrap;
    }
    .msg-status-pill svg { width: 12px; height: 12px; }
    .msg-status-read { background: #dcfce7; color: #15803d; }
    .msg-status-unread { background: #fef3c7; color: #92400e; }

    @media (max-width: 640px) {
        .msg-recipient-row { flex-wrap: wrap; }
        .msg-status-pill { margin-left: 46px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .msg-progress-fill { transition: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const search = document.getElementById('recipient-search');
        const rows = Array.from(document.querySelectorAll('.msg-recipient-row'));
        const emptyState = document.getElementById('recipient-empty');

        search.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            let visible = 0;
            rows.forEach((row) => {
                const match = row.dataset.search.includes(q);
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            emptyState.classList.toggle('hidden', visible !== 0);
        });
    })();
</script>
@endpush
