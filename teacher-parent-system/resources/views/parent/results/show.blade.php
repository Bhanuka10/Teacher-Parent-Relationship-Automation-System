@extends('layouts.parent')
@section('title', $exam->name)

@section('content')
@php
    $trendLabels = $history->map(fn ($entry) => $entry['exam']->termLabel())->all();
    $trendValues = $history->map(fn ($entry) => $entry['average'])->all();

    $subjectLabels = $exam->subjects->pluck('name')->all();
    $studentValues = $exam->subjects->map(fn ($subject) => $row['marks'][$subject->id]['mark'])->all();
    $classAverageValues = $exam->subjects->map(fn ($subject) => $subjectAverages[$subject->id] ?? null)->all();

    $latestDelta = $history->last()['delta'] ?? null;
    $rank = $summary['class_rank'] ?? null;
    $rankClass = match(true) {
        $rank === null => 'ui-rank-none',
        $rank === 1 => 'ui-rank-1',
        $rank === 2 => 'ui-rank-2',
        $rank === 3 => 'ui-rank-3',
        default => 'ui-rank-other',
    };
    [$passedCount, $enteredCount] = array_pad(explode('/', $summary['subjects_passed'] ?? '0/0'), 2, 0);
    $passedPct = $enteredCount > 0 ? round(($passedCount / $enteredCount) * 100) : 0;
@endphp
<div class="mx-auto max-w-4xl">
    <a href="{{ route('parent.results.index') }}" class="ui-back" style="color:#c2410c">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to results
    </a>

    <section class="ui-card ui-animate-in mt-4 p-6">
        <span class="ui-tag ui-tag-orange">Term {{ $exam->term }} · {{ $exam->academic_year }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $exam->name }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">Exam date {{ $exam->exam_date->format('d M Y') }}</p>
        @if(!$row['is_complete'])
            <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">Some subjects haven't been marked yet — averages and ranking will appear once every subject is entered.</p>
        @endif
    </section>

    {{-- ═══════ PERFORMANCE SUMMARY ═══════ --}}
    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="ui-stat-card ui-animate-in ui-animate-in-1">
            <p class="ui-stat-label">Average</p>
            <p class="ui-stat-value">{{ $summary['average'] ?? '—' }}</p>
        </div>
        <div class="ui-stat-card ui-animate-in ui-animate-in-2">
            <p class="ui-stat-label">Class rank</p>
            <p class="mt-1"><span class="ui-rank-badge {{ $rankClass }}">{{ $rank ?? '—' }}</span></p>
        </div>
        <div class="ui-stat-card ui-animate-in ui-animate-in-3">
            <p class="ui-stat-label">Highest subject</p>
            <p class="mt-1.5 text-sm font-bold text-gray-800">{{ $summary['highest_subject'] ?? '—' }}</p>
        </div>
        <div class="ui-stat-card ui-animate-in ui-animate-in-4">
            <p class="ui-stat-label">Lowest subject</p>
            <p class="mt-1.5 text-sm font-bold text-gray-800">{{ $summary['lowest_subject'] ?? '—' }}</p>
        </div>
    </div>

    <section class="ui-card ui-animate-in ui-animate-in-2 mt-4 p-6">
        <div class="flex items-center justify-between">
            <p class="ui-field-label" style="margin-bottom:0">Subjects passed</p>
            <span class="text-sm font-bold text-gray-800">{{ $summary['subjects_passed'] ?? '0/0' }}</span>
        </div>
        <span class="ui-progress mt-2"><span class="ui-progress-fill" style="width:{{ $passedPct }}%"></span></span>

        @if(!empty($summary['grade_counts']))
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($summary['grade_counts'] as $grade => $count)
                    <span class="ui-tag ui-tag-gray">{{ $grade }} × {{ $count }}</span>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ═══════ SUBJECT BREAKDOWN ═══════ --}}
    <section class="ui-card ui-animate-in mt-6 overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800">Subject breakdown</h2>
        </div>
        <div class="p-4">
            @include('exams._student-subject-table', ['exam' => $exam, 'row' => $row, 'subjectAverages' => $subjectAverages])
        </div>
    </section>

    {{-- ═══════ CHARTS ═══════ --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="ui-card ui-animate-in p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Term-over-term average</h2>
                @if($latestDelta !== null)
                    <span class="ui-trend-pill {{ $latestDelta > 0 ? 'ui-trend-up' : ($latestDelta < 0 ? 'ui-trend-down' : 'ui-trend-flat') }}">
                        {{ $latestDelta > 0 ? '+' : '' }}{{ $latestDelta }}
                    </span>
                @endif
            </div>
            <div style="margin-top:16px;height:220px">
                <canvas data-trend-chart data-labels='@json($trendLabels)' data-values='@json($trendValues)'></canvas>
            </div>
        </section>

        <section class="ui-card ui-animate-in p-6">
            <h2 class="font-bold text-gray-800">Subject vs. class average</h2>
            <div style="margin-top:16px;height:220px">
                <canvas data-subject-chart data-labels='@json($subjectLabels)' data-student-values='@json($studentValues)' data-class-averages='@json($classAverageValues)'></canvas>
            </div>
        </section>
    </div>

    {{-- ═══════ ATTENDANCE CORRELATION ═══════ --}}
    <section class="ui-card ui-animate-in mt-6 p-6">
        <h2 class="font-bold text-gray-800">Attendance this term</h2>
        <div class="mt-3 flex items-center gap-3">
            <span class="ui-progress flex-1"><span class="ui-progress-fill" style="width:{{ $attendance['percent'] ?? 0 }}%;background:linear-gradient(90deg,#fb923c,#c2410c)"></span></span>
            <span class="whitespace-nowrap text-sm font-bold text-gray-800">{{ $attendance['percent'] !== null ? $attendance['percent'].'%' : 'No records' }}</span>
        </div>
        <div class="mt-3 flex gap-2">
            <span class="ui-status-pill ui-status-graded">{{ $attendance['present'] }} present</span>
            <span class="ui-status-pill ui-status-expired">{{ $attendance['absent'] }} absent</span>
        </div>
    </section>
</div>
@endsection

@push('scripts')
@vite(['resources/js/results-charts.js'])
@endpush
