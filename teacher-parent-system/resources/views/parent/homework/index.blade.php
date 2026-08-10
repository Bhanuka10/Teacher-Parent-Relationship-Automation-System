@extends('layouts.parent')
@section('title', 'Homework')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-orange">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Homework</h1>
            <p class="mt-0.5 text-sm text-gray-500">File assignments and quizzes from your teacher.</p>
        </div>
    </div>

    @if(!$student)
        <div class="ui-card">
            <div class="ui-empty"><p>No student record is linked to this account yet.</p></div>
        </div>
    @else
        <section class="ui-card overflow-hidden">
            @forelse($submissions as $submission)
                @php
                    $homework = $submission->homework;
                    $status = $submission->status();
                    $statusLabel = ['pending' => 'Not submitted', 'submitted' => 'Submitted', 'graded' => 'Graded', 'expired' => 'Expired'][$status];
                @endphp
                <a href="{{ route('parent.homework.show', $homework) }}" class="flex items-start gap-3 border-b border-gray-100 px-6 py-4 text-decoration-none transition hover:bg-orange-50/40 last:border-0" style="text-decoration:none">
                    <span class="ui-tag {{ $homework->type === 'quiz' ? 'ui-tag-rose' : 'ui-tag-orange' }}" style="margin-top:2px">{{ $homework->type === 'quiz' ? 'Quiz' : 'File' }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-800">{{ $homework->title }}</span>
                        <span class="mt-1 block text-xs text-gray-400">
                            {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
                            @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min @endif
                        </span>
                    </span>
                    <span class="flex shrink-0 flex-col items-end gap-1">
                        <span class="ui-status-pill ui-status-{{ $status }}">{{ $statusLabel }}</span>
                        @if($submission->isGraded())
                            <span class="ui-tag ui-tag-orange">{{ $submission->marks }}/{{ $homework->max_marks }}</span>
                        @endif
                    </span>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p>No homework assigned yet.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
