@extends('layouts.teacher')
@section('title', 'Edit '.$homework->title)

@push('styles')
<style>
    :root {
        --t-accent:       #0f766e;
        --t-accent-light: #ccfbf1;
        --t-accent-mid:   #14b8a6;
    }

    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0; }
    .db-sub span { color: var(--t-accent); font-weight: 600; }

    .db-head {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 14px; margin-bottom: 26px;
    }

    .quick-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .qa-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 10px;
        font-size: 12.5px; font-weight: 700;
        text-decoration: none; transition: all .15s;
        border: 1.5px solid transparent; cursor: pointer;
    }
    .qa-btn svg { width: 15px; height: 15px; }
    .qa-btn.primary { background: var(--t-accent); color: #fff; box-shadow: 0 4px 14px rgba(15,118,110,.28); }
    .qa-btn.primary:hover { background: #0d5f58; transform: translateY(-1px); }
    .qa-btn.ghost { background: #fff; color: var(--t-accent); border-color: #d1fae5; }
    .qa-btn.ghost:hover { background: var(--t-accent-light); border-color: var(--t-accent-mid); }

    /* ── two-column layout (matches dashboard's content-grid) ── */
    .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .content-grid { grid-template-columns: 1fr; } }

    /* ── form card ── */
    .exam-form-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .exam-field-label {
        font-size: 10.5px; font-weight: 700; color: #9ca3af;
        text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; display: block;
    }
    .att-input {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 13px;
        font-size: 13.5px; color: #111827; background: #fafafa; outline: none; box-sizing: border-box;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .att-input:focus { border-color: var(--t-accent-mid); box-shadow: 0 0 0 3px rgba(20,184,166,.15); background: #fff; }

    /* ── sidebar summary card ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }
    .summary-body { padding: 6px 20px 18px; }
    .summary-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 12.5px; gap: 10px;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { color: #9ca3af; font-weight: 600; flex-shrink: 0; }
    .summary-value { color: #111827; font-weight: 700; text-align: right; }
    .tips-box {
        margin: 14px 20px 20px; background: var(--t-accent-light); border: 1px solid #99f6e4;
        border-radius: 10px; padding: 12px 14px; font-size: 11.5px; color: #0f766e; line-height: 1.5;
    }
</style>
@endpush

@section('content')
@php
    $submittedCount = $homework->submissions->filter->isSubmitted()->count();
    $totalCount = $homework->submissions->count();
@endphp
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Edit Timeline</h1>
            <p class="db-sub">
                Extending the due date or duration for <span>{{ $homework->title }}</span> only ever gives students
                more time — it never takes time away from an attempt already in progress.
            </p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.homework.show', $homework) }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to {{ $homework->title }}
            </a>
        </div>
    </div>

    <div class="content-grid">
        <form method="POST" action="{{ route('teacher.homework.update', $homework) }}" class="exam-form-card space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="exam-field-label">Title</label>
                <input id="title" name="title" value="{{ old('title', $homework->title) }}" maxlength="255" required class="att-input">
                @error('title')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="instructions" class="exam-field-label">Instructions</label>
                <textarea id="instructions" name="instructions" rows="3" class="att-input resize-none">{{ old('instructions', $homework->instructions) }}</textarea>
                @error('instructions')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="due_at" class="exam-field-label">Due date/time</label>
                    <input type="datetime-local" id="due_at" name="due_at"
                           value="{{ old('due_at', optional($homework->due_at)->format('Y-m-d\TH:i')) }}" class="att-input">
                    @error('due_at')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                @if($homework->isQuiz())
                    <div>
                        <label for="duration_minutes" class="exam-field-label">Duration once started (minutes)</label>
                        <input type="number" id="duration_minutes" name="duration_minutes" min="1" max="600"
                               value="{{ old('duration_minutes', $homework->duration_minutes) }}" class="att-input">
                        @error('duration_minutes')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endif
            </div>

            <button type="submit" class="qa-btn primary w-full justify-center" style="width:100%;padding:12px;font-size:13.5px">Save changes</button>
        </form>

        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Current Status
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-row"><span class="summary-label">Class</span><span class="summary-value">{{ $homework->schoolClass->name ?? '—' }}</span></div>
                <div class="summary-row"><span class="summary-label">Type</span><span class="summary-value">{{ $homework->isQuiz() ? 'Quiz' : 'File submission' }}</span></div>
                <div class="summary-row"><span class="summary-label">Current due</span><span class="summary-value">{{ $homework->due_at ? $homework->due_at->format('d M Y, h:i A') : 'No due date' }}</span></div>
                @if($homework->isQuiz())
                    <div class="summary-row"><span class="summary-label">Duration</span><span class="summary-value">{{ $homework->duration_minutes }} min</span></div>
                @endif
                <div class="summary-row"><span class="summary-label">Max marks</span><span class="summary-value">{{ $homework->max_marks }}</span></div>
                <div class="summary-row"><span class="summary-label">Submitted</span><span class="summary-value">{{ $submittedCount }}/{{ $totalCount }}</span></div>
            </div>
            <div class="tips-box">
                Tip: subjects and questions can't be edited here — go back to the homework page to manage grading.
            </div>
        </div>
    </div>
</div>
@endsection
