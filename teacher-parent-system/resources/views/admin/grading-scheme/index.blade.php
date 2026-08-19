@extends('layouts.admin')
@section('title', 'Grading Scheme')

@push('styles')
<style>
    /* ── Shared tokens ── */
    :root {
        --accent: #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid: #4f46e5;
    }

    /* ── Page header row ── */
    .mt-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
    .mt-title   { font-size: 22px; font-weight: 800; color: #111827; margin: 0; }
    .mt-sub     { font-size: 13px; color: #6b7280; margin: 4px 0 24px; }
    .mt-sub span { color: var(--accent); font-weight: 600; }

    /* ── Stats card ── */
    .stats-strip {
        display: flex; gap: 16px; flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.purple { background: var(--accent-light); color: var(--accent); }
    .stat-icon.teal   { background: #ccfbf1; color: #0f766e; }
    .stat-icon.rose   { background: #fee2e2; color: #991b1b; }
    .stat-value { font-size: 20px; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

    /* ── Table-style card ── */
    .mt-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        margin-bottom: 24px;
    }
    .gs-head-row {
        display: grid;
        grid-template-columns: 1fr 1.4fr 1fr 90px;
        padding: 12px 20px;
        font-size: 11px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #9ca3af;
        background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
    }
    .gs-band-row { border-bottom: 1px solid #f3f4f6; }
    .gs-band-row:last-child { border-bottom: none; }
    .gs-band-row summary {
        display: grid;
        grid-template-columns: 1fr 1.4fr 1fr 90px;
        align-items: center;
        padding: 14px 20px;
        cursor: pointer;
        list-style: none;
        font-size: 13px;
        transition: background .12s;
    }
    .gs-band-row summary:hover { background: #fafafa; }
    .gs-band-row summary::-webkit-details-marker { display: none; }
    .gs-band-row summary::marker { content: ''; }
    .gs-grade { font-weight: 700; color: #111827; }
    .gs-range { color: #374151; }
    .gs-edit-link { color: var(--accent); font-size: 12px; font-weight: 600; text-align: right; }
    .gs-edit-link:hover { color: var(--accent-mid); }

    /* Badges */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
        width: fit-content;
    }
    .status-badge.active   { background: #d1fae5; color: #065f46; }
    .status-badge.rejected { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-badge.active .status-dot   { background: #10b981; }
    .status-badge.rejected .status-dot { background: #ef4444; }

    /* Inline edit panel */
    .gs-edit-panel { padding: 4px 20px 20px; }
    .mt-field-label {
        display: block; font-size: 11px; font-weight: 700;
        letter-spacing: .04em; text-transform: uppercase;
        color: #9ca3af; margin-bottom: 5px;
    }
    .mt-input {
        border: 1px solid #d1d5db;
        border-radius: 9px;
        padding: 8px 12px;
        font-size: 13px; color: #374151;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .mt-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    .mt-chip {
        display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 9px;
        font-size: 12.5px; color: #374151; cursor: pointer;
        border: 1px solid #d1d5db;
    }

    /* Buttons */
    .btn-search {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 9px;
        font-size: 12.5px; font-weight: 700;
        background: var(--accent-mid); color: #fff;
        border: none; cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-search:hover { background: #4338ca; }
    .act-remove {
        background: none; border: none; cursor: pointer;
        padding: 0; margin-top: 10px;
        font-size: 12px; font-weight: 600; color: #ef4444;
    }
    .act-remove:hover { color: #b91c1c; text-decoration: underline; }

    /* Empty state */
    .mt-empty { padding: 56px 24px; text-align: center; color: #9ca3af; }
    .mt-empty p { font-size: 14px; }
</style>
@endpush

@section('content')
@php
    $totalBands = $bands->count();
    $passingCount = $bands->where('is_passing', true)->count();
    $failingCount = $totalBands - $passingCount;
@endphp

<div class="max-w-4xl">

    {{-- ── Page heading ── --}}
    <div class="mt-header">
        <div>
            <h1 class="mt-title">Grading Scheme</h1>
            <p class="mt-sub">
                Mark-percentage bands used to grade <span>every subject in every exam</span>, school-wide. Changes apply immediately.
            </p>
        </div>
    </div>

    {{-- ── Stats cards ── --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 17V7m6 10V11m-3 6V4"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $totalBands }}</div>
                <div class="stat-label">Total Bands</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $passingCount }}</div>
                <div class="stat-label">Passing Grades</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $failingCount }}</div>
                <div class="stat-label">Failing Grades</div>
            </div>
        </div>
    </div>

    {{-- ── Bands list ── --}}
    <div class="mt-table-wrap">
        <div class="gs-head-row">
            <span>Grade</span>
            <span>Range</span>
            <span>Status</span>
            <span></span>
        </div>

        @forelse($bands as $band)
            <details class="gs-band-row">
                <summary>
                    <span class="gs-grade">{{ $band->grade }}</span>
                    <span class="gs-range">{{ $band->min_mark }}% – {{ $band->max_mark }}%</span>
                    <span class="status-badge {{ $band->is_passing ? 'active' : 'rejected' }}">
                        <span class="status-dot"></span>
                        {{ $band->is_passing ? 'Passing' : 'Failing' }}
                    </span>
                    <span class="gs-edit-link">Edit</span>
                </summary>
                <div class="gs-edit-panel">
                    <form method="POST" action="{{ route('admin.grading-scheme.update', $band) }}" class="flex flex-wrap items-end gap-3">
                        @csrf @method('PUT')
                        <div style="width:90px">
                            <label class="mt-field-label">Min %</label>
                            <input type="number" name="min_mark" min="0" max="100" value="{{ $band->min_mark }}" class="mt-input" style="width:100%" required>
                        </div>
                        <div style="width:90px">
                            <label class="mt-field-label">Max %</label>
                            <input type="number" name="max_mark" min="0" max="100" value="{{ $band->max_mark }}" class="mt-input" style="width:100%" required>
                        </div>
                        <div style="width:80px">
                            <label class="mt-field-label">Grade</label>
                            <input type="text" name="grade" maxlength="5" value="{{ $band->grade }}" class="mt-input" style="width:100%" required>
                        </div>
                        <label class="mt-chip">
                            <input type="checkbox" name="is_passing" value="1" @checked($band->is_passing)>
                            <span>Passing grade</span>
                        </label>
                        <button type="submit" class="btn-search">Save</button>
                    </form>
                    <form method="POST" action="{{ route('admin.grading-scheme.destroy', $band) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="act-remove">Remove this band</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="mt-empty"><p>No grading bands configured yet.</p></div>
        @endforelse
    </div>

    {{-- ── Add new band ── --}}
    <div class="mt-table-wrap" style="padding:20px 20px 22px">
        <p class="mt-field-label" style="margin-bottom:12px">Add a new band</p>
        <form method="POST" action="{{ route('admin.grading-scheme.store') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div style="width:90px">
                <label class="mt-field-label">Min %</label>
                <input type="number" name="min_mark" min="0" max="100" value="{{ old('min_mark') }}" class="mt-input" style="width:100%" required>
            </div>
            <div style="width:90px">
                <label class="mt-field-label">Max %</label>
                <input type="number" name="max_mark" min="0" max="100" value="{{ old('max_mark') }}" class="mt-input" style="width:100%" required>
            </div>
            <div style="width:80px">
                <label class="mt-field-label">Grade</label>
                <input type="text" name="grade" maxlength="5" value="{{ old('grade') }}" class="mt-input" style="width:100%" required>
            </div>
            <label class="mt-chip">
                <input type="checkbox" name="is_passing" value="1" @checked(old('is_passing', true))>
                <span>Passing grade</span>
            </label>
            <button type="submit" class="btn-search">Add band</button>
        </form>
        @error('min_mark')<p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
@endsection
