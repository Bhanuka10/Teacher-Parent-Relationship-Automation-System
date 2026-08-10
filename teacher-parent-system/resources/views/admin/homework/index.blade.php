@extends('layouts.admin')
@section('title', 'Homework')

@section('content')
<div class="max-w-6xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-indigo">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Homework</h1>
            <p class="mt-0.5 text-sm text-gray-500">Read-only view of homework and quizzes assigned across all classes.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.homework.index') }}" class="ui-card mb-6 flex flex-wrap items-end gap-3 p-5">
        <div style="min-width:180px">
            <label class="ui-field-label">Class</label>
            <select name="class_id" class="ui-input">
                <option value="">All classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px">
            <label class="ui-field-label">Type</label>
            <select name="type" class="ui-input">
                <option value="">All types</option>
                <option value="file" @selected($type === 'file')>File submission</option>
                <option value="quiz" @selected($type === 'quiz')>Quiz</option>
            </select>
        </div>
        <button type="submit" class="ui-submit-btn" style="padding:10px 20px">Filter</button>
        <a href="{{ route('admin.homework.index') }}" class="ui-tab-btn" style="background:#fff;border:1px solid #e5e7eb;color:#374151;padding:10px 16px">Reset</a>
    </form>

    <section class="ui-card overflow-hidden">
        @forelse($homeworks as $homework)
            <a href="{{ route('admin.homework.show', $homework) }}" class="flex items-start gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0" style="text-decoration:none">
                <span class="ui-tag {{ $homework->type === 'quiz' ? 'ui-tag-rose' : 'ui-tag-indigo' }}" style="margin-top:2px">{{ $homework->type === 'quiz' ? 'Quiz' : 'File' }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-gray-800">{{ $homework->title }}</span>
                    <span class="mt-1 block text-xs text-gray-400">
                        {{ $homework->schoolClass?->name ?? '—' }} · {{ $homework->teacher?->name ?? '—' }}
                        · {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y') : 'No due date' }}
                    </span>
                </span>
                <span class="flex shrink-0 flex-col items-end gap-1">
                    <span class="ui-tag ui-tag-gray">{{ $homework->submitted_count }}/{{ $homework->total_count }} submitted</span>
                    <span class="ui-tag ui-tag-gray">{{ $homework->graded_count }} graded</span>
                </span>
            </a>
        @empty
            <div class="ui-empty"><p>No homework matches these filters.</p></div>
        @endforelse

        @if($homeworks->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $homeworks->links() }}</div>@endif
    </section>
</div>
@endsection
