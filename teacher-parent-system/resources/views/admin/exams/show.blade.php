@extends('layouts.admin')
@section('title', $exam->name)

@section('content')
<div class="max-w-6xl">
    <a href="{{ route('admin.exams.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to exams
    </a>

    <section class="ui-card mt-4 p-6">
        <span class="ui-tag ui-tag-indigo">Term {{ $exam->term }} · {{ $exam->academic_year }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $exam->name }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">
            {{ $exam->schoolClass?->name ?? '—' }} · {{ $exam->teacher?->name ?? '—' }}
            · Exam date {{ $exam->exam_date->format('d M Y') }}
            · Term window {{ $exam->term_start_date->format('d M') }}–{{ $exam->term_end_date->format('d M Y') }}
        </p>
    </section>

    <section class="ui-card mt-6 overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800">Marks &amp; rankings</h2>
        </div>
        <div class="p-4">
            @include('exams._marks-grid', ['exam' => $exam, 'grid' => $grid, 'canEdit' => false])
        </div>
    </section>
</div>
@endsection
