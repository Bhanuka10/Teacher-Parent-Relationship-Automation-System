@extends('layouts.admin')
@section('title', 'Homework')

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    .hw-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .hw-sub   { font-size: 13px; color: #6b7280; margin: 0 0 28px; }
    .hw-sub span { color: var(--accent); font-weight: 600; }

    .hw-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .hw-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 20px; border-radius: 9px;
        font-size: 13px; font-weight: 700;
        background: var(--accent-mid); color: #fff; border: none;
        text-decoration: none; cursor: pointer;
        box-shadow: 0 4px 14px rgba(79,70,229,.25);
        transition: background .15s;
    }
    .hw-btn:hover { background: #4338ca; }
    .hw-btn-ghost {
        background: #fff; color: #374151; border: 1px solid #e5e7eb;
        box-shadow: none; padding: 10px 16px;
    }
    .hw-btn-ghost:hover { background: #f9fafb; }

    .hw-tag {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
    }
    .hw-tag .dot { width: 5px; height: 5px; border-radius: 50%; }
    .hw-tag.quiz { background: #fee2e2; color: #991b1b; }
    .hw-tag.quiz .dot { background: #ef4444; }
    .hw-tag.file { background: var(--accent-light); color: var(--accent); }
    .hw-tag.file .dot { background: var(--accent); }

    .hw-chip {
        background: #f3f4f6; color: #374151;
        border-radius: 5px; padding: 2px 8px;
        font-size: 11px; font-weight: 600;
    }

    .hw-empty { text-align: center; padding: 40px; color: #9ca3af; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="max-w-6xl">
    <h1 class="hw-title">Homework</h1>
    <p class="hw-sub">Read-only view of <span>homework and quizzes</span> assigned across all classes.</p>

    <form method="GET" action="{{ route('admin.homework.index') }}" class="hw-card mb-6 flex flex-wrap items-end gap-3 p-5">
        <div style="min-width:180px">
            <label class="ui-field-label">Class</label>
            <select name="class_id" class="ui-input">
                <option value="">All classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px">
            <label class="ui-field-label">Type</label>
            <select name="type" class="ui-input">
                <option value="">All types</option>
                <option value="file" @selected($type === 'file')>File submission</option>
                <option value="quiz" @selected($type === 'quiz')>Quiz</option>
            </select>
        </div>
        <button type="submit" class="hw-btn">Filter</button>
        <a href="{{ route('admin.homework.index') }}" class="hw-btn hw-btn-ghost">Reset</a>
    </form>

    <section class="hw-card overflow-hidden">
        @forelse($homeworks as $homework)
            <a href="{{ route('admin.homework.show', $homework) }}" class="flex items-start gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0" style="text-decoration:none">
                <span class="hw-tag {{ $homework->type === 'quiz' ? 'quiz' : 'file' }}" style="margin-top:2px"><span class="dot"></span>{{ $homework->type === 'quiz' ? 'Quiz' : 'File' }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-gray-800">{{ $homework->title }}</span>
                    <span class="mt-1 block text-xs text-gray-400">
                        {{ $homework->schoolClass?->name ?? '—' }} · {{ $homework->teacher?->name ?? '—' }}
                        · {{ $homework->due_at ? 'Due '.$homework->due_at->format('d M Y') : 'No due date' }}
                    </span>
                </span>
                <span class="flex shrink-0 flex-col items-end gap-1">
                    <span class="hw-chip">{{ $homework->submitted_count }}/{{ $homework->total_count }} submitted</span>
                    <span class="hw-chip">{{ $homework->graded_count }} graded</span>
                </span>
            </a>
        @empty
            <div class="hw-empty"><p>No homework matches these filters.</p></div>
        @endforelse

        @if($homeworks->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $homeworks->links() }}</div>@endif
    </section>
</div>
@endsection
