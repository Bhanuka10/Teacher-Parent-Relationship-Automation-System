@extends($layout)
@section('title', 'Message')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route(auth()->user()->isTeacher() ? 'teacher.messages.index' : 'parent.messages.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">← Back to messages</a>
    <article class="mt-4 rounded-xl border border-gray-200 bg-white p-7 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">School administration</p>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">{{ $recipient->message->subject }}</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $recipient->message->created_at->format('d M Y, h:i A') }}</p>
        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $recipient->message->body }}</div>
    </article>
</div>
@endsection
