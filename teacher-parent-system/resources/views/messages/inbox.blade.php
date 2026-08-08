@extends($layout)
@section('title', 'Messages')

@section('content')
<div class="mx-auto max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-800">Messages</h1>
    <p class="mb-7 mt-1 text-sm text-gray-500">Notices sent by the school administration.</p>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @forelse($recipients as $recipient)
            <a href="{{ route(auth()->user()->isTeacher() ? 'teacher.messages.show' : 'parent.messages.show', $recipient) }}" class="block border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0 {{ $recipient->read_at ? '' : 'bg-indigo-50/40' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            @if(!$recipient->read_at)<span class="h-2 w-2 rounded-full bg-indigo-600"></span>@endif
                            <h2 class="truncate text-sm font-semibold text-gray-800">{{ $recipient->message->subject }}</h2>
                        </div>
                        <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $recipient->message->body }}</p>
                    </div>
                    <time class="shrink-0 text-xs text-gray-400">{{ $recipient->created_at->format('d M Y') }}</time>
                </div>
            </a>
        @empty
            <p class="px-6 py-12 text-center text-sm text-gray-500">You have no messages yet.</p>
        @endforelse
    </section>
    @if($recipients->hasPages())<div class="mt-5">{{ $recipients->links() }}</div>@endif
</div>
@endsection
