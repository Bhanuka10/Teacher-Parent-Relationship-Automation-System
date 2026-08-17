@extends('layouts.parent')
@section('title', $exam->name)

@push('styles')
<style>
    :root {
        --p-accent:       #c2410c;
        --p-accent-light: #ffedd5;
        --p-accent-mid:   #f97316;
    }

    /* ── KPI stat cards (identical to the dashboard's kit) ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 16px;
    }
    .kpi-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 16px;
    }
    .kpi-icon {
        width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .kpi-icon.orange { background: var(--p-accent-light); color: var(--p-accent); }
    .kpi-icon.green  { background: #d1fae5; color: #065f46; }
    .kpi-icon.red    { background: #fee2e2; color: #991b1b; }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-val   { font-size: 18px; font-weight: 800; color: #111827; line-height: 1.2; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── section card (same radius/shadow as the dashboard's cards) ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    /* ── two-column content layout (same ratio as the dashboard) ── */
    .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .content-grid { grid-template-columns: 1fr; } }
</style>
@endpush

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
<div class="max-w-7xl">
    <a href="{{ route('parent.results.index') }}" class="ui-back" style="color:#c2410c">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to results
    </a>

    <section class="section-card ui-animate-in mt-4 p-6">
        <span class="ui-tag ui-tag-orange">Term {{ $exam->term }} · {{ $exam->academic_year }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $exam->name }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">Exam date {{ $exam->exam_date->format('d M Y') }}</p>
        @if(!$row['is_complete'])
            <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">Some subjects haven't been marked yet — averages and ranking will appear once every subject is entered.</p>
        @endif
    </section>

    {{-- ═══════ PERFORMANCE SUMMARY ═══════ --}}
    <div class="kpi-grid mt-4 ui-animate-in ui-animate-in-1">
        <div class="kpi-card">
            <div class="kpi-icon orange">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M8 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $summary['average'] ?? '—' }}</div>
                <div class="kpi-label">Average</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-3.5 1.5L7 21l5-2 5 2-1.5-4.5"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $rank ?? '—' }}</div>
                <div class="kpi-label">Class Rank</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div>
                <div class="kpi-val" style="font-size:15px">{{ $summary['highest_subject'] ?? '—' }}</div>
                <div class="kpi-label">Highest Subject</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon red">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 3 9 21 6 12 2 12"/></svg>
            </div>
            <div>
                <div class="kpi-val" style="font-size:15px">{{ $summary['lowest_subject'] ?? '—' }}</div>
                <div class="kpi-label">Lowest Subject</div>
            </div>
        </div>
    </div>

    <div class="content-grid mt-6">
        {{-- ── Left: subject breakdown + charts ── --}}
        <div class="flex flex-col gap-6">
            <section class="section-card ui-animate-in overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="font-bold text-gray-800">Subject breakdown</h2>
                </div>
                <div class="p-4">
                    @include('exams._student-subject-table', ['exam' => $exam, 'row' => $row, 'subjectAverages' => $subjectAverages])
                </div>
            </section>

            <section class="section-card ui-animate-in p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-gray-800">Term-over-term average</h2>
                    @if($latestDelta !== null)
                        <span class="ui-trend-pill {{ $latestDelta > 0 ? 'ui-trend-up' : ($latestDelta < 0 ? 'ui-trend-down' : 'ui-trend-flat') }}">
                            {{ $latestDelta > 0 ? '+' : '' }}{{ $latestDelta }}
                        </span>
                    @endif
                </div>
                <div style="margin-top:16px;height:240px">
                    <canvas data-trend-chart data-labels='@json($trendLabels)' data-values='@json($trendValues)'></canvas>
                </div>
            </section>

            <section class="section-card ui-animate-in p-6">
                <h2 class="font-bold text-gray-800">Subject vs. class average</h2>
                <div style="margin-top:16px;height:240px">
                    <canvas data-subject-chart data-labels='@json($subjectLabels)' data-student-values='@json($studentValues)' data-class-averages='@json($classAverageValues)'></canvas>
                </div>
            </section>
        </div>

        {{-- ── Right: subjects passed + attendance ── --}}
        <div class="flex flex-col gap-6">
            <section class="section-card ui-animate-in ui-animate-in-2 p-6">
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

            <section class="section-card ui-animate-in p-6">
                <h2 class="font-bold text-gray-800">Attendance this term</h2>
                <div class="mt-3 flex items-center gap-3">
                    <span class="ui-progress flex-1"><span class="ui-progress-fill" style="width:{{ $attendance['percent'] ?? 0 }}%;background:linear-gradient(90deg,#fb923c,#c2410c)"></span></span>
                    <span class="whitespace-nowrap text-sm font-bold text-gray-800">{{ $attendance['percent'] !== null ? $attendance['percent'].'%' : 'No records' }}</span>
                </div>
                <div class="mt-3 flex gap-2">
                    <span class="ui-status-pill ui-status-graded">{{ $attendance['present'] }} present</span>
                    <span class="ui-status-pill ui-status-expired">{{ $attendance['absent'] }} absent</span>
                    <span class="ui-status-pill ui-status-pending">{{ $attendance['leave'] }} leave</span>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/results-charts.js'])
@endpush
