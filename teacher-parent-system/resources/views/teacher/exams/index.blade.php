@extends('layouts.teacher')
@section('title', 'Exams')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-7 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="ui-hero-icon ui-hero-indigo">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Exams</h1>
                <p class="mt-0.5 text-sm text-gray-500">Create term exams and enter your class's marks.</p>
            </div>
        </div>
        @if($schoolClass)
            <a href="{{ route('teacher.exams.create') }}" class="ui-submit-btn">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New
            </a>
        @endif
    </div>

    @if(!$schoolClass)
        <div class="ui-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before creating an exam.</p>
            </div>
        </div>
    @else
        <section class="ui-card overflow-hidden">
            @forelse($exams as $exam)
                <a href="{{ route('teacher.exams.show', $exam) }}" style="display:flex;align-items:center;gap:12px;padding:16px 24px;border-bottom:1px solid #f3f4f6;text-decoration:none;transition:background .12s">
                    <span class="ui-tag ui-tag-indigo">Term {{ $exam->term }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-800">{{ $exam->name }}</span>
                        <span class="mt-1 block text-xs text-gray-400">
                            {{ $exam->academic_year }} · {{ $exam->exam_date->format('d M Y') }} · {{ $exam->subjects_count }} subject{{ $exam->subjects_count === 1 ? '' : 's' }}
                        </span>
                    </span>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                    <p>No exams created yet.</p>
                    <p class="ui-empty-sub">Create a term exam for {{ $schoolClass->name }}.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
