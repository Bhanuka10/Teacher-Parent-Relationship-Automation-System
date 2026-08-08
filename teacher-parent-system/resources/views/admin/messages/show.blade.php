@extends('layouts.admin')
@section('title', 'Message read status')

@section('content')
<div class="mx-auto max-w-5xl">
    <a href="{{ route('admin.messages.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">← Back to messages</a>
    <section class="mt-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ $message->audience === 'teacher' ? 'Teacher' : 'Student' }} message</p>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">{{ $message->subject }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $message->created_at->format('d M Y, h:i A') }} · {{ $message->recipients->whereNotNull('read_at')->count() }}/{{ $message->recipients->count() }} recipients have read this</p>
        <p class="mt-5 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $message->body }}</p>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4"><h2 class="font-bold text-gray-800">Recipient read status</h2></div>
        @foreach($message->recipients->sortBy('user.name') as $recipient)
            @php($classes = $recipient->user->isTeacher() ? collect([$recipient->user->schoolClass])->filter() : $recipient->user->students->pluck('schoolClass')->filter())
            <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-4 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $recipient->user->name }}</p>
                    <p class="mt-0.5 text-xs text-gray-500">{{ $recipient->user->email }}@if($classes->isNotEmpty()) · {{ $classes->pluck('name')->unique()->join(', ') }}@endif</p>
                </div>
                @if($recipient->read_at)
                    <span class="shrink-0 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">Read {{ $recipient->read_at->format('d M, h:i A') }}</span>
                @else
                    <span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Not read</span>
                @endif
            </div>
        @endforeach
    </section>
</div>
@endsection
