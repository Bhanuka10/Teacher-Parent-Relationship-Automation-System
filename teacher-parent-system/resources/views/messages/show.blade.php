@extends($layout)
@section('title', 'Message')

@section('content')
@php
    $isTeacher = auth()->user()->isTeacher();
@endphp
<div class="msg-page mx-auto max-w-3xl">
    <a href="{{ route($isTeacher ? 'teacher.messages.index' : 'parent.messages.index') }}" class="msg-back {{ $isTeacher ? 'msg-back-teacher' : 'msg-back-student' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to messages
    </a>

    <article class="msg-detail-card mt-4">
        <div class="flex items-center gap-3">
            <span class="msg-sender-avatar {{ $isTeacher ? 'msg-sender-avatar-teacher' : 'msg-sender-avatar-student' }}">SA</span>
            <div>
                <p class="text-sm font-semibold text-gray-800">School Administration</p>
                <p class="text-xs text-gray-400">{{ $recipient->message->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        <h1 class="mt-5 text-2xl font-bold text-gray-800">{{ $recipient->message->subject }}</h1>
        <div class="mt-5 whitespace-pre-line border-t border-gray-100 pt-5 text-sm leading-7 text-gray-700">{{ $recipient->message->body }}</div>
    </article>
</div>
@endsection

@push('styles')
<style>
    .msg-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: .8rem; font-weight: 500; text-decoration: none; transition: color .15s;
    }
    .msg-back-teacher { color: #0f766e; }
    .msg-back-teacher:hover { color: #115e59; }
    .msg-back-student { color: #c2410c; }
    .msg-back-student:hover { color: #9a3412; }
    .msg-back svg { width: 14px; height: 14px; }

    .msg-detail-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
        padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .msg-sender-avatar {
        width: 40px; height: 40px; flex-shrink: 0; border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
    }
    .msg-sender-avatar-teacher { background: linear-gradient(135deg, #0f766e, #14b8a6); }
    .msg-sender-avatar-student { background: linear-gradient(135deg, #c2410c, #f97316); }
</style>
@endpush
