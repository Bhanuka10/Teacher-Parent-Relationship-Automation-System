@extends('layouts.teacher')
@section('title', 'New Homework')

@section('content')
@php
    $activeType = old('type', 'file');
@endphp
<div class="mx-auto max-w-3xl">
    <a href="{{ route('teacher.homework.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to homework
    </a>

    <h1 class="mt-4 text-2xl font-bold text-gray-800">Assign new homework</h1>
    <p class="mt-1 text-sm text-gray-500">For {{ $schoolClass->name }}. Every student in the class will get this assigned automatically.</p>

    <form method="POST" action="{{ route('teacher.homework.store') }}" class="mt-6 space-y-5" id="homework-form">
        @csrf

        <section class="ui-card p-6">
            <p class="ui-field-label">Type</p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="ui-option-card">
                    <input type="radio" name="type" value="file" class="sr-only qh-type-radio" @checked($activeType === 'file')>
                    <span class="ui-option-icon" style="background:#e0e7ff;color:#4338ca">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M4 20h16"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-gray-800">File submission</span>
                        <span class="mt-0.5 block text-xs text-gray-500">Students upload a document/video</span>
                    </span>
                    <span class="ui-option-check"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                </label>
                <label class="ui-option-card">
                    <input type="radio" name="type" value="quiz" class="sr-only qh-type-radio" @checked($activeType === 'quiz')>
                    <span class="ui-option-icon" style="background:#ffe4e6;color:#be123c">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-gray-800">Quiz</span>
                        <span class="mt-0.5 block text-xs text-gray-500">Timed, MCQ and/or writing questions</span>
                    </span>
                    <span class="ui-option-check"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                </label>
            </div>
        </section>

        <section class="ui-card p-6 space-y-4">
            <div>
                <label for="title" class="ui-field-label">Title</label>
                <input id="title" name="title" value="{{ old('title') }}" maxlength="255" required
                       placeholder="e.g. Chapter 4 worksheet" class="ui-input">
                @error('title')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="instructions" class="ui-field-label">Instructions <span class="font-normal text-gray-400">(optional)</span></label>
                <textarea id="instructions" name="instructions" rows="3" class="ui-input resize-none">{{ old('instructions') }}</textarea>
                @error('instructions')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="due_at" class="ui-field-label">Due date/time <span class="font-normal text-gray-400">(optional)</span></label>
                    <input type="datetime-local" id="due_at" name="due_at" value="{{ old('due_at') }}" class="ui-input">
                    @error('due_at')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="qh-panel-quiz" style="{{ $activeType === 'quiz' ? '' : 'display:none' }}">
                    <label for="duration_minutes" class="ui-field-label">Duration once started (minutes)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1" max="600"
                           value="{{ old('duration_minutes', 30) }}" class="ui-input">
                    @error('duration_minutes')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="qh-panel-file" style="{{ $activeType === 'file' ? '' : 'display:none' }}">
                    <label for="max_marks" class="ui-field-label">Max marks</label>
                    <input type="number" id="max_marks" name="max_marks" min="1" max="1000" value="{{ old('max_marks', 100) }}" class="ui-input">
                    @error('max_marks')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- ═══════ FILE-TYPE OPTIONS ═══════ --}}
        <section class="ui-card p-6 qh-panel-file" style="{{ $activeType === 'file' ? '' : 'display:none' }}">
            <p class="ui-field-label">Accepted file types</p>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach(['pdf' => 'PDF', 'pptx' => 'PPTX', 'video' => 'Video', 'png' => 'PNG'] as $value => $label)
                    <label class="ui-chip">
                        <input type="checkbox" name="allowed_file_types[]" value="{{ $value }}" class="sr-only"
                               @checked(in_array($value, old('allowed_file_types', ['pdf', 'pptx', 'video', 'png'])))>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('allowed_file_types')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </section>

        {{-- ═══════ QUIZ QUESTION BUILDER ═══════ --}}
        <section class="ui-card p-6 qh-panel-quiz" style="{{ $activeType === 'quiz' ? '' : 'display:none' }}">
            <div class="flex items-center justify-between">
                <p class="ui-field-label" style="margin-bottom:0">Questions</p>
                <button type="button" id="qh-add-question" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Add question</button>
            </div>
            @error('questions')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            @php($questionErrorKeys = collect($errors->keys())->filter(fn ($key) => str_starts_with($key, 'questions')))
            @if($questionErrorKeys->isNotEmpty())
                <div class="mt-2 rounded-lg bg-red-50 p-3">
                    <ul class="list-inside list-disc space-y-0.5 text-xs font-medium text-red-600">
                        @foreach($questionErrorKeys as $key)
                            <li>{{ $errors->first($key) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div id="qh-questions" class="mt-3 space-y-3"></div>
        </section>

        <button type="submit" class="ui-submit-btn w-full justify-center" style="width:100%">
            Create {{ $activeType === 'quiz' ? 'quiz' : 'homework' }}
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const typeRadios = document.querySelectorAll('.qh-type-radio');
    const filePanels = document.querySelectorAll('.qh-panel-file');
    const quizPanels = document.querySelectorAll('.qh-panel-quiz');
    const questionsContainer = document.getElementById('qh-questions');
    const addQuestionBtn = document.getElementById('qh-add-question');
    let qCounter = 0;

    function syncType() {
        const type = document.querySelector('.qh-type-radio:checked')?.value || 'file';
        filePanels.forEach((el) => el.style.display = type === 'file' ? '' : 'none');
        quizPanels.forEach((el) => el.style.display = type === 'quiz' ? '' : 'none');
    }
    typeRadios.forEach((r) => r.addEventListener('change', syncType));

    function optionRowHtml(qIndex, oIndex) {
        return `<div class="flex items-center gap-2" data-option-row>
            <input type="checkbox" name="questions[${qIndex}][options][${oIndex}][is_correct]" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" title="Correct answer">
            <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" placeholder="Option text" class="ui-input flex-1" style="padding:7px 10px">
            <button type="button" class="qh-remove-option text-gray-400 hover:text-red-600" title="Remove option">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>`;
    }

    function questionBlockHtml(qIndex) {
        return `<div class="qh-question rounded-lg border border-gray-200 p-4" data-qindex="${qIndex}">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm font-semibold text-gray-700">Question ${qIndex + 1}</span>
                <button type="button" class="qh-remove-question text-xs font-semibold text-red-500 hover:text-red-700">Remove</button>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-[1fr_110px_150px]">
                <div>
                    <label class="ui-field-label" style="margin-bottom:4px">Question</label>
                    <textarea name="questions[${qIndex}][question]" class="ui-input" rows="2" required></textarea>
                </div>
                <div>
                    <label class="ui-field-label" style="margin-bottom:4px">Marks</label>
                    <input type="number" name="questions[${qIndex}][marks]" min="1" value="1" class="ui-input" required>
                </div>
                <div>
                    <label class="ui-field-label" style="margin-bottom:4px">Answer type</label>
                    <select name="questions[${qIndex}][type]" class="ui-input qh-question-type" required>
                        <option value="mcq" selected>Multiple choice</option>
                        <option value="writing">Writing</option>
                    </select>
                </div>
            </div>
            <div class="qh-options mt-3 space-y-2"></div>
            <button type="button" class="qh-add-option mt-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Add option</button>
        </div>`;
    }

    function addQuestion(data) {
        const index = qCounter++;
        questionsContainer.insertAdjacentHTML('beforeend', questionBlockHtml(index));
        const block = questionsContainer.querySelector(`.qh-question[data-qindex="${index}"]`);
        const optionsWrap = block.querySelector('.qh-options');
        const typeSelect = block.querySelector('.qh-question-type');

        if (data) {
            block.querySelector('textarea[name*="[question]"]').value = data.question || '';
            block.querySelector('input[name*="[marks]"]').value = data.marks || 1;
            typeSelect.value = data.type || 'mcq';
            const options = (data.options && data.options.length) ? data.options : [{}, {}];
            options.forEach((option, oIndex) => {
                addOption(optionsWrap, index);
                const row = optionsWrap.querySelectorAll('[data-option-row]')[oIndex];
                row.querySelector('input[type="text"]').value = option.text || '';
                if (option.is_correct) row.querySelector('input[type="checkbox"]').checked = true;
            });
            typeSelect.dispatchEvent(new Event('change'));
        } else {
            // Chromium doesn't run the "pick a selected option" algorithm for
            // a <select> inserted via insertAdjacentHTML, so the `selected`
            // attribute on the first <option> is silently ignored and the
            // select is left at value="" (invalid for a required field).
            // Set it explicitly rather than relying on HTML parsing.
            typeSelect.value = 'mcq';
            addOption(optionsWrap, index);
            addOption(optionsWrap, index);
        }
    }

    function addOption(optionsWrap, qIndex) {
        const oIndex = optionsWrap.querySelectorAll('[data-option-row]').length;
        optionsWrap.insertAdjacentHTML('beforeend', optionRowHtml(qIndex, oIndex));
    }

    // Bound as a wrapper, not passed directly — addEventListener would
    // otherwise call addQuestion(clickEvent), and the click Event object's
    // own `.type` property ("click") would silently overwrite the intended
    // default question type.
    addQuestionBtn.addEventListener('click', () => addQuestion());

    questionsContainer.addEventListener('click', (event) => {
        const removeQuestionBtn = event.target.closest('.qh-remove-question');
        if (removeQuestionBtn) {
            removeQuestionBtn.closest('.qh-question').remove();
            return;
        }

        const addOptionBtn = event.target.closest('.qh-add-option');
        if (addOptionBtn) {
            const block = addOptionBtn.closest('.qh-question');
            addOption(block.querySelector('.qh-options'), block.dataset.qindex);
            return;
        }

        const removeOptionBtn = event.target.closest('.qh-remove-option');
        if (removeOptionBtn) {
            removeOptionBtn.closest('[data-option-row]').remove();
        }
    });

    questionsContainer.addEventListener('change', (event) => {
        if (!event.target.classList.contains('qh-question-type')) return;
        const block = event.target.closest('.qh-question');
        const isMcq = event.target.value === 'mcq';
        const optionsWrap = block.querySelector('.qh-options');
        optionsWrap.style.display = isMcq ? '' : 'none';
        block.querySelector('.qh-add-option').style.display = isMcq ? '' : 'none';
        // Disabled inputs are excluded from form submission entirely — needed
        // so a writing question's blank, hidden option rows don't trip the
        // "options.*.text required" rule for MCQ questions.
        optionsWrap.querySelectorAll('input').forEach((input) => { input.disabled = !isMcq; });
    });

    syncType();
    const oldQuestions = @json(old('questions', []));
    if (oldQuestions.length) {
        oldQuestions.forEach((question) => addQuestion(question));
    } else if (quizPanels[0] && quizPanels[0].style.display !== 'none') {
        addQuestion();
    }
})();
</script>
@endpush
