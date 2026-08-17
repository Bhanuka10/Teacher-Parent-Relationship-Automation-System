@extends('layouts.admin')
@section('title', 'Exams')

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .ex-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .ex-sub   { font-size: 13px; color: #6b7280; margin: 0 0 28px; }
    .ex-sub span { color: var(--accent); font-weight: 600; }

    .ex-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .ex-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 20px; border-radius: 9px;
        font-size: 13px; font-weight: 700;
        background: var(--accent-mid); color: #fff; border: none;
        text-decoration: none; cursor: pointer;
        box-shadow: 0 4px 14px rgba(79,70,229,.25);
        transition: background .15s;
    }
    .ex-btn:hover { background: #4338ca; }
    .ex-btn-ghost {
        background: #fff; color: #374151; border: 1px solid #e5e7eb;
        box-shadow: none; padding: 10px 16px;
    }
    .ex-btn-ghost:hover { background: #f9fafb; }

    .ex-tag {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
        background: var(--accent-light); color: var(--accent);
    }
    .ex-tag .dot { width: 5px; height: 5px; border-radius: 50%; background: var(--accent); }

    .ex-chip {
        background: #f3f4f6; color: #374151;
        border-radius: 5px; padding: 2px 8px;
        font-size: 11px; font-weight: 600;
    }

    .ex-empty { text-align: center; padding: 40px; color: #9ca3af; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="max-w-6xl">
    <h1 class="ex-title">Exams</h1>
    <p class="ex-sub">Read-only view of <span>term exams and marks</span> entered across all classes.</p>

    <form method="GET" action="{{ route('admin.exams.index') }}" class="ex-card mb-6 flex flex-wrap items-end gap-3 p-5">
        <div style="min-width:180px">
            <label class="ui-field-label">Class</label>
            <select name="class_id" class="ui-input">
                <option value="">All classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:140px">
            <label class="ui-field-label">Academic year</label>
            <input type="text" name="academic_year" value="{{ $academicYear }}" placeholder="2025/2026" class="ui-input">
        </div>
        <div style="min-width:140px">
            <label class="ui-field-label">Term</label>
            <select name="term" class="ui-input">
                <option value="">All terms</option>
                @foreach([1 => 'Term 1', 2 => 'Term 2', 3 => 'Term 3'] as $value => $label)
                    <option value="{{ $value }}" @selected($term == $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="ex-btn">Filter</button>
        <a href="{{ route('admin.exams.index') }}" class="ex-btn ex-btn-ghost">Reset</a>
    </form>

    <section class="ex-card overflow-hidden">
        @forelse($exams as $exam)
            <a href="{{ route('admin.exams.show', $exam) }}" class="flex items-start gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0" style="text-decoration:none">
                <span class="ex-tag" style="margin-top:2px"><span class="dot"></span>Term {{ $exam->term }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-gray-800">{{ $exam->name }}</span>
                    <span class="mt-1 block text-xs text-gray-400">
                        {{ $exam->schoolClass?->name ?? '—' }} · {{ $exam->teacher?->name ?? '—' }}
                        · {{ $exam->academic_year }} · {{ $exam->exam_date->format('d M Y') }}
                    </span>
                </span>
                <span class="ex-chip">{{ $exam->subjects_count }} subject{{ $exam->subjects_count === 1 ? '' : 's' }}</span>
            </a>
        @empty
            <div class="ex-empty"><p>No exams match these filters.</p></div>
        @endforelse

        @if($exams->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $exams->links() }}</div>@endif
    </section>
</div>
@endsection
