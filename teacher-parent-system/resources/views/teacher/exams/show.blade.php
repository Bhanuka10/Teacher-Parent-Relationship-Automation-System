@extends('layouts.teacher')
@section('title', $exam->name)

@section('content')
<div class="mx-auto max-w-5xl">
    <a href="{{ route('teacher.exams.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to exams
    </a>

    <section class="ui-card mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <span class="ui-tag ui-tag-indigo">Term {{ $exam->term }} · {{ $exam->academic_year }}</span>
                <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $exam->name }}</h1>
                <p class="mt-1.5 text-sm text-gray-500">
                    Exam date {{ $exam->exam_date->format('d M Y') }} · Term window {{ $exam->term_start_date->format('d M') }}–{{ $exam->term_end_date->format('d M Y') }}
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('teacher.exams.edit', $exam) }}" class="ui-tab-btn" style="background:#fff;border:1px solid #e5e7eb;color:#374151">Edit exam</a>
                <form method="POST" action="{{ route('teacher.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this exam and all its marks? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="ui-tab-btn" style="background:#fef2f2;color:#b91c1c">Delete</button>
                </form>
            </div>
        </div>
    </section>

    <section class="ui-card mt-6 overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800">Marks entry</h2>
            <p class="text-xs text-gray-400">Tab/Enter/arrow keys move between cells · only complete rows are ranked</p>
        </div>
        <form method="POST" action="{{ route('teacher.exams.marks.save', $exam) }}" id="marks-form">
            @csrf
            @method('PUT')
            <div class="p-4">
                @include('exams._marks-grid', ['exam' => $exam, 'grid' => $grid, 'canEdit' => true])
            </div>
            <div class="border-t border-gray-100 px-6 py-4">
                <button type="submit" class="ui-submit-btn w-full justify-center" style="width:100%">Save all marks</button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const inputs = Array.from(document.querySelectorAll('.exam-mark-input'));

    function moveFocus(row, col) {
        const el = inputs.find((i) => +i.dataset.row === row && +i.dataset.col === col);
        if (el) el.focus();
    }

    inputs.forEach((input) => {
        input.addEventListener('keydown', (event) => {
            const row = +input.dataset.row;
            const col = +input.dataset.col;

            if (event.key === 'Enter' || event.key === 'ArrowDown') {
                event.preventDefault();
                moveFocus(row + 1, col);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveFocus(row - 1, col);
            } else if (event.key === 'ArrowRight' && input.selectionStart === input.value.length) {
                moveFocus(row, col + 1);
            } else if (event.key === 'ArrowLeft' && input.selectionStart === 0) {
                moveFocus(row, col - 1);
            }
        });
        input.addEventListener('focus', () => input.select());
    });
})();
</script>
@endpush
