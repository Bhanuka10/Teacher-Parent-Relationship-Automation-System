@extends('layouts.teacher')
@section('title', 'New Exam')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('teacher.exams.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to exams
    </a>

    <h1 class="mt-4 text-2xl font-bold text-gray-800">Create term exam</h1>
    <p class="mt-1 text-sm text-gray-500">For {{ $schoolClass->name }}. Add subjects now — marks are entered afterward.</p>

    <form method="POST" action="{{ route('teacher.exams.store') }}" class="mt-6 space-y-5">
        @csrf

        <section class="ui-card p-6 space-y-4">
            <div>
                <label for="name" class="ui-field-label">Exam name</label>
                <input id="name" name="name" value="{{ old('name') }}" maxlength="255" required
                       placeholder="e.g. First Term Test" class="ui-input">
                @error('name')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="academic_year" class="ui-field-label">Academic year</label>
                    <input id="academic_year" name="academic_year" value="{{ old('academic_year', now()->year.'/'.(now()->year + 1)) }}"
                           placeholder="2025/2026" required class="ui-input">
                    @error('academic_year')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="term" class="ui-field-label">Term</label>
                    <select id="term" name="term" required class="ui-input">
                        @foreach([1 => 'Term 1', 2 => 'Term 2', 3 => 'Term 3'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('term') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('term')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="exam_date" class="ui-field-label">Exam date</label>
                    <input type="date" id="exam_date" name="exam_date" value="{{ old('exam_date') }}" required class="ui-input">
                    @error('exam_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="term_start_date" class="ui-field-label">Term start date</label>
                    <input type="date" id="term_start_date" name="term_start_date" value="{{ old('term_start_date') }}" required class="ui-input">
                    <p class="mt-1 text-[11px] text-gray-400">Used for the attendance-vs-performance summary.</p>
                    @error('term_start_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="term_end_date" class="ui-field-label">Term end date</label>
                    <input type="date" id="term_end_date" name="term_end_date" value="{{ old('term_end_date') }}" required class="ui-input">
                    @error('term_end_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="ui-card p-6">
            <div class="flex items-center justify-between">
                <p class="ui-field-label" style="margin-bottom:0">Subjects</p>
                <button type="button" id="es-add-subject" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Add subject</button>
            </div>
            @error('subjects')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            <div id="es-subjects" class="mt-3 space-y-2"></div>
        </section>

        <button type="submit" class="ui-submit-btn w-full justify-center" style="width:100%">Create exam</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const subjectsContainer = document.getElementById('es-subjects');
    const addSubjectBtn = document.getElementById('es-add-subject');
    let sCounter = 0;

    function subjectRowHtml(index) {
        return `<div class="flex items-center gap-2" data-subject-row data-sindex="${index}">
            <input type="text" name="subjects[${index}][name]" placeholder="Subject name" class="ui-input flex-1" style="padding:8px 10px" required>
            <input type="number" name="subjects[${index}][max_mark]" placeholder="Max" value="100" min="1" max="1000" class="ui-input" style="padding:8px 10px;width:90px" required>
            <button type="button" class="es-remove-subject text-gray-400 hover:text-red-600" title="Remove subject">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>`;
    }

    function addSubject(data) {
        const index = sCounter++;
        subjectsContainer.insertAdjacentHTML('beforeend', subjectRowHtml(index));
        if (data) {
            const row = subjectsContainer.querySelector(`[data-sindex="${index}"]`);
            row.querySelector('input[type="text"]').value = data.name || '';
            row.querySelector('input[type="number"]').value = data.max_mark || 100;
        }
    }

    // Bound as a wrapper — passing addSubject directly would receive the
    // click Event as its `data` argument (same pitfall hit in the homework
    // quiz builder), silently corrupting the row's fields.
    addSubjectBtn.addEventListener('click', () => addSubject());

    subjectsContainer.addEventListener('click', (event) => {
        const removeBtn = event.target.closest('.es-remove-subject');
        if (removeBtn) {
            removeBtn.closest('[data-subject-row]').remove();
        }
    });

    const oldSubjects = @json(old('subjects', []));
    if (oldSubjects.length) {
        oldSubjects.forEach((subject) => addSubject(subject));
    } else {
        addSubject();
    }
})();
</script>
@endpush
