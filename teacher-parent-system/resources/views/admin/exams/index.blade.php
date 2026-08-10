@extends('layouts.admin')
@section('title', 'Exams')

@section('content')
<div class="max-w-6xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-indigo">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Exams</h1>
            <p class="mt-0.5 text-sm text-gray-500">Read-only view of term exams and marks entered across all classes.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.exams.index') }}" class="ui-card mb-6 flex flex-wrap items-end gap-3 p-5">
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
        <button type="submit" class="ui-submit-btn" style="padding:10px 20px">Filter</button>
        <a href="{{ route('admin.exams.index') }}" class="ui-tab-btn" style="background:#fff;border:1px solid #e5e7eb;color:#374151;padding:10px 16px">Reset</a>
    </form>

    <section class="ui-card overflow-hidden">
        @forelse($exams as $exam)
            <a href="{{ route('admin.exams.show', $exam) }}" class="flex items-start gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0" style="text-decoration:none">
                <span class="ui-tag ui-tag-indigo" style="margin-top:2px">Term {{ $exam->term }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-gray-800">{{ $exam->name }}</span>
                    <span class="mt-1 block text-xs text-gray-400">
                        {{ $exam->schoolClass?->name ?? '—' }} · {{ $exam->teacher?->name ?? '—' }}
                        · {{ $exam->academic_year }} · {{ $exam->exam_date->format('d M Y') }}
                    </span>
                </span>
                <span class="ui-tag ui-tag-gray">{{ $exam->subjects_count }} subject{{ $exam->subjects_count === 1 ? '' : 's' }}</span>
            </a>
        @empty
            <div class="ui-empty"><p>No exams match these filters.</p></div>
        @endforelse

        @if($exams->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $exams->links() }}</div>@endif
    </section>
</div>
@endsection
