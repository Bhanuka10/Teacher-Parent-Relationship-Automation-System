@extends($layout)
@section('title', 'Messages')

@section('content')
@php
    $isTeacher = auth()->user()->isTeacher();
@endphp
<div class="msg-page mx-auto max-w-4xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="msg-hero-icon {{ $isTeacher ? 'msg-hero-teacher' : 'msg-hero-student' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.77 59.77 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Messages</h1>
            <p class="mt-0.5 text-sm text-gray-500">Notices sent by the school administration.</p>
        </div>
    </div>

    <section class="msg-card overflow-hidden">
        @forelse($recipients as $recipient)
            @php($unread = !$recipient->read_at)
            <a href="{{ route($isTeacher ? 'teacher.messages.show' : 'parent.messages.show', $recipient) }}"
               class="msg-inbox-row {{ $isTeacher ? 'msg-inbox-row-teacher' : 'msg-inbox-row-student' }} {{ $unread ? 'is-unread' : '' }}">
                <span class="msg-inbox-dot-wrap">
                    @if($unread)<span class="msg-inbox-dot {{ $isTeacher ? 'msg-inbox-dot-teacher' : 'msg-inbox-dot-student' }}"></span>@endif
                </span>

                <span class="msg-inbox-icon {{ $isTeacher ? 'msg-inbox-icon-teacher' : 'msg-inbox-icon-student' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z" />
                    </svg>
                </span>

                <span class="min-w-0 flex-1">
                    <span class="flex items-center gap-2">
                        <span class="truncate text-sm {{ $unread ? 'font-bold text-gray-900' : 'font-semibold text-gray-700' }}">{{ $recipient->message->subject }}</span>
                    </span>
                    <span class="mt-1 line-clamp-2 block text-sm text-gray-500">{{ $recipient->message->body }}</span>
                    <span class="mt-1.5 block text-xs text-gray-400">School administration · {{ $recipient->created_at->diffForHumans() }}</span>
                </span>

                <svg class="msg-chevron shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @empty
            <div class="msg-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z" />
                </svg>
                <p>You have no messages yet.</p>
                <p class="msg-empty-sub">Notices from the school administration will show up here.</p>
            </div>
        @endforelse
    </section>

    @if($recipients->hasPages())<div class="mt-5">{{ $recipients->links() }}</div>@endif
</div>
@endsection

@push('styles')
<style>
    .msg-hero-icon {
        width: 46px; height: 46px; flex-shrink: 0; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; color: #fff;
    }
    .msg-hero-icon svg { width: 22px; height: 22px; }
    .msg-hero-teacher { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); box-shadow: 0 4px 14px rgba(15,118,110,.28); }
    .msg-hero-student { background: linear-gradient(135deg, #c2410c 0%, #f97316 100%); box-shadow: 0 4px 14px rgba(194,65,12,.28); }

    .msg-card {
        background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .msg-inbox-row {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 16px 22px; border-bottom: 1px solid #f3f4f6; text-decoration: none;
        transition: background .12s, transform .12s;
        border-left: 3px solid transparent;
    }
    .msg-inbox-row:last-child { border-bottom: none; }
    .msg-inbox-row:hover { transform: translateX(2px); }
    .msg-inbox-row-teacher:hover { background: #f0fdfa; }
    .msg-inbox-row-student:hover { background: #fff7ed; }
    .msg-inbox-row.is-unread.msg-inbox-row-teacher { background: #f0fdfa50; border-left-color: #14b8a6; }
    .msg-inbox-row.is-unread.msg-inbox-row-student { background: #fff7ed50; border-left-color: #f97316; }

    .msg-inbox-dot-wrap { width: 6px; flex-shrink: 0; display: flex; align-items: center; padding-top: 6px; }
    .msg-inbox-dot { width: 6px; height: 6px; border-radius: 999px; display: block; animation: msg-pulse 2s ease-in-out infinite; }
    .msg-inbox-dot-teacher { background: #0d9488; }
    .msg-inbox-dot-student { background: #ea580c; }
    @keyframes msg-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }

    .msg-inbox-icon {
        width: 36px; height: 36px; flex-shrink: 0; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
    }
    .msg-inbox-icon svg { width: 18px; height: 18px; }
    .msg-inbox-icon-teacher { background: #ccfbf1; color: #0f766e; }
    .msg-inbox-icon-student { background: #ffedd5; color: #c2410c; }

    .msg-chevron { width: 15px; height: 15px; color: #d1d5db; margin-top: 8px; transition: transform .15s; }
    .msg-inbox-row:hover .msg-chevron { transform: translateX(2px); }

    .msg-empty { padding: 60px 24px; text-align: center; color: #9ca3af; }
    .msg-empty svg { width: 44px; height: 44px; margin: 0 auto 12px; opacity: .35; }
    .msg-empty p { font-size: 13.5px; }
    .msg-empty-sub { font-size: 12px; margin-top: 3px; color: #c1c5cc; }

    @media (prefers-reduced-motion: reduce) {
        .msg-inbox-row, .msg-chevron { transition: none; }
        .msg-inbox-dot { animation: none; }
    }
</style>
@endpush
