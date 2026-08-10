@extends('layouts.teacher')
@section('title', 'Homework')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-7 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="ui-hero-icon ui-hero-teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Homework</h1>
                <p class="mt-0.5 text-sm text-gray-500">Assign file submissions or quizzes to your class.</p>
            </div>
        </div>
        @if($schoolClass)
            <a href="{{ route('teacher.homework.create') }}" class="ui-submit-btn">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New
            </a>
        @endif
    </div>

    @if(!$schoolClass)
        <div class="ui-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before creating homework.</p>
            </div>
        </div>
    @else
        <section class="ui-card overflow-hidden">
            @forelse($homeworks as $homework)
                @php
                    $pct = $homework->total_count > 0 ? round(($homework->submitted_count / $homework->total_count) * 100) : 0;
                @endphp
                <a href="{{ route('teacher.homework.show', $homework) }}" class="msg-sent-row" style="display:flex;align-items:flex-start;gap:12px;padding:16px 24px;border-bottom:1px solid #f3f4f6;text-decoration:none;transition:background .12s">
                    <span class="ui-tag {{ $homework->type === 'quiz' ? 'ui-tag-rose' : 'ui-tag-indigo' }}" style="margin-top:2px">{{ $homework->type === 'quiz' ? 'Quiz' : 'File' }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-800">{{ $homework->title }}</span>
                        <span class="mt-1 block text-xs text-gray-400">
                            {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
                            @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min @endif
                        </span>
                        <span class="ui-progress mt-2" style="max-width:240px">
                            <span class="ui-progress-fill" style="width:{{ $pct }}%"></span>
                        </span>
                    </span>
                    <span class="flex shrink-0 flex-col items-end gap-1">
                        <span class="ui-tag ui-tag-gray">{{ $homework->submitted_count }}/{{ $homework->total_count }} submitted</span>
                        <span class="ui-tag ui-tag-gray">{{ $homework->graded_count }} graded</span>
                    </span>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p>No homework assigned yet.</p>
                    <p class="ui-empty-sub">Create a file assignment or quiz for {{ $schoolClass->name }}.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
