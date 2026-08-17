@extends('layouts.admin')
@section('title', $exam->name)

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .ex-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--accent); text-decoration: none; transition: color .15s;
        margin-bottom: 14px;
    }
    .ex-back:hover { color: var(--accent-mid); }
    .ex-back svg { width: 14px; height: 14px; }

    .ex-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .ex-sub   { font-size: 13px; color: #6b7280; margin: 0 0 26px; }
    .ex-sub span { color: var(--accent); font-weight: 600; }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
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
    .kpi-icon.purple { background: var(--accent-light); color: var(--accent); }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    .ex-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .ex-section-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .ex-section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }
    .ex-section-sub { font-size: 11.5px; color: #9ca3af; }

    /* Marks grid comes from a partial shared with the teacher's marks-entry page —
       recolour it here, scoped to this page, instead of touching the shared file. */
    .ex-card .ui-avatar-indigo { background: var(--accent-light); color: var(--accent); }
</style>
@endpush

@section('content')
@php
    $totalStudents = $grid->count();
    $totalSubjects = $exam->subjects->count();
    $completeCount = $grid->where('is_complete', true)->count();
    $averages = $grid->pluck('average')->filter(fn ($a) => $a !== null);
    $classAverage = $averages->isNotEmpty() ? round($averages->avg(), 1) : null;
@endphp
<div class="max-w-7xl">
    <a href="{{ route('admin.exams.index') }}" class="ex-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to exams
    </a>

    <h1 class="ex-title">{{ $exam->name }}</h1>
    <p class="ex-sub">
        Term {{ $exam->term }} · <span>{{ $exam->academic_year }}</span> ·
        {{ $exam->schoolClass?->name ?? '—' }} · {{ $exam->teacher?->name ?? '—' }} ·
        exam date {{ $exam->exam_date->format('d M Y') }} ·
        term window {{ $exam->term_start_date->format('d M') }}–{{ $exam->term_end_date->format('d M Y') }}
    </p>

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon purple">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $totalStudents }}</div>
                <div class="kpi-label">Students</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $totalSubjects }}</div>
                <div class="kpi-label">Subjects</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $completeCount }}/{{ $totalStudents }}</div>
                <div class="kpi-label">Fully Graded</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon purple">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $classAverage ?? '—' }}</div>
                <div class="kpi-label">Class Average</div>
            </div>
        </div>
    </div>

    <section class="ex-card">
        <div class="ex-section-header">
            <div class="ex-section-title">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                Marks &amp; rankings
            </div>
            <p class="ex-section-sub">Read-only — subject marks, grades and class rank</p>
        </div>
        <div class="p-4">
            @include('exams._marks-grid', ['exam' => $exam, 'grid' => $grid, 'canEdit' => false])
        </div>
    </section>
</div>
@endsection
