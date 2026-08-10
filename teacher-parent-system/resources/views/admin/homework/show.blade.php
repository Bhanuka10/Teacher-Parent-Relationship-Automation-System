@extends('layouts.admin')
@section('title', $homework->title)

@section('content')
@php
    $total = $homework->submissions->count();
    $submittedCount = $homework->submissions->filter->isSubmitted()->count();
    $gradedCount = $homework->submissions->filter->isGraded()->count();
    $pct = $total > 0 ? round(($submittedCount / $total) * 100) : 0;
@endphp
<div class="mx-auto max-w-4xl">
    <a href="{{ route('admin.homework.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to homework
    </a>

    <section class="ui-card mt-4 p-6">
        <span class="ui-tag {{ $homework->isQuiz() ? 'ui-tag-rose' : 'ui-tag-indigo' }}">{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $homework->title }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">
            {{ $homework->schoolClass?->name ?? '—' }} · Assigned by {{ $homework->teacher?->name ?? '—' }}
            · {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y, h:i A') : 'No due date' }}
            @if($homework->isQuiz()) · {{ $homework->duration_minutes }} min @endif
        </p>

        <div class="mt-5 flex items-center gap-3">
            <span class="ui-progress flex-1"><span class="ui-progress-fill" style="width:{{ $pct }}%"></span></span>
            <span class="whitespace-nowrap text-xs font-semibold text-gray-600">{{ $submittedCount }}/{{ $total }} submitted · {{ $gradedCount }} graded</span>
        </div>
    </section>

    <section class="ui-card mt-6 overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800">Submission status</h2>
        </div>
        @forelse($homework->submissions as $submission)
            @include('homework._submission-row', ['homework' => $homework, 'submission' => $submission, 'canGrade' => false])
        @empty
            <div class="ui-empty"><p>No students in this class.</p></div>
        @endforelse
    </section>
</div>
@endsection
