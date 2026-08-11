@extends('layouts.teacher')
@section('title', 'New Homework')

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

    .exam-add-link {
        font-size: 12px; font-weight: 700; color: var(--t-accent); background: none; border: none;
        cursor: pointer; transition: color .15s;
    }
    .exam-add-link:hover { color: #0d5f58; }

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
    $activeType = old('type', 'file');
@endphp
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">New Homework</h1>
            <p class="db-sub">For class <span>{{ $schoolClass->name }}</span> · every student gets this assigned automatically.</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.homework.index') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to homework
            </a>
        </div>
    </div>

    <div class="content-grid">
        <form method="POST" action="{{ route('teacher.homework.store') }}" class="space-y-5" id="homework-form">
            @csrf

            <section class="exam-form-card">
                <p class="exam-field-label">Type</p>
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

            <section class="exam-form-card space-y-4">
                <div>
                    <label for="title" class="exam-field-label">Title</label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="255" required
                           placeholder="e.g. Chapter 4 worksheet" class="att-input">
                    @error('title')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="instructions" class="exam-field-label">Instructions <span class="font-normal text-gray-400" style="text-transform:none;letter-spacing:normal">(optional)</span></label>
                    <textarea id="instructions" name="instructions" rows="3" class="att-input resize-none">{{ old('instructions') }}</textarea>
                    @error('instructions')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="due_at" class="exam-field-label">Due date/time <span class="font-normal text-gray-400" style="text-transform:none;letter-spacing:normal">(optional)</span></label>
                        <input type="datetime-local" id="due_at" name="due_at" value="{{ old('due_at') }}" class="att-input">
                        @error('due_at')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="qh-panel-quiz" style="{{ $activeType === 'quiz' ? '' : 'display:none' }}">
                        <label for="duration_minutes" class="exam-field-label">Duration once started (minutes)</label>
                        <input type="number" id="duration_minutes" name="duration_minutes" min="1" max="600"
                               value="{{ old('duration_minutes', 30) }}" class="att-input">
                        @error('duration_minutes')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="qh-panel-file" style="{{ $activeType === 'file' ? '' : 'display:none' }}">
                        <label for="max_marks" class="exam-field-label">Max marks</label>
                        <input type="number" id="max_marks" name="max_marks" min="1" max="1000" value="{{ old('max_marks', 100) }}" class="att-input">
                        @error('max_marks')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- ═══════ FILE-TYPE OPTIONS ═══════ --}}
            <section class="exam-form-card qh-panel-file" style="{{ $activeType === 'file' ? '' : 'display:none' }}">
                <p class="exam-field-label">Accepted file types</p>
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
            <section class="exam-form-card qh-panel-quiz" style="{{ $activeType === 'quiz' ? '' : 'display:none' }}">
                <div class="flex items-center justify-between">
                    <p class="exam-field-label" style="margin-bottom:0">Questions</p>
                    <button type="button" id="qh-add-question" class="exam-add-link">+ Add question</button>
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

            <button type="submit" class="qa-btn primary w-full justify-center" style="width:100%;padding:12px;font-size:13.5px">
                Create {{ $activeType === 'quiz' ? 'quiz' : 'homework' }}
            </button>
        </form>

        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Homework Summary
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-row"><span class="summary-label">Class</span><span class="summary-value">{{ $schoolClass->name }}</span></div>
                <div class="summary-row"><span class="summary-label">Type</span><span class="summary-value" id="summary-type">File submission</span></div>
                <div class="summary-row"><span class="summary-label">Due</span><span class="summary-value" id="summary-due">No due date</span></div>
                <div class="summary-row"><span class="summary-label">Total Marks</span><span class="summary-value" id="summary-marks">100</span></div>
                <div class="summary-row" id="summary-questions-row" style="display:none">
                    <span class="summary-label">Questions</span><span class="summary-value" id="summary-questions">0</span>
                </div>
            </div>
            <div class="tips-box">
                Tip: for quizzes, the timer starts the moment a student opens it — extend the due date if anyone needs more time.
            </div>
        </div>
    </div>
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
    const form = document.getElementById('homework-form');
    let qCounter = 0;

    function syncType() {
        const type = document.querySelector('.qh-type-radio:checked')?.value || 'file';
        filePanels.forEach((el) => el.style.display = type === 'file' ? '' : 'none');
        quizPanels.forEach((el) => el.style.display = type === 'quiz' ? '' : 'none');
        updateSummary();
    }
    typeRadios.forEach((r) => r.addEventListener('change', syncType));

    function optionRowHtml(qIndex, oIndex) {
        return `<div class="flex items-center gap-2" data-option-row>
            <input type="checkbox" name="questions[${qIndex}][options][${oIndex}][is_correct]" value="1" style="accent-color:#0f766e" title="Correct answer">
            <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" placeholder="Option text" class="att-input flex-1" style="padding:7px 10px">
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
                    <label class="exam-field-label" style="margin-bottom:4px">Question</label>
                    <textarea name="questions[${qIndex}][question]" class="att-input" rows="2" required></textarea>
                </div>
                <div>
                    <label class="exam-field-label" style="margin-bottom:4px">Marks</label>
                    <input type="number" name="questions[${qIndex}][marks]" min="1" value="1" class="att-input" required>
                </div>
                <div>
                    <label class="exam-field-label" style="margin-bottom:4px">Answer type</label>
                    <select name="questions[${qIndex}][type]" class="att-input qh-question-type" required>
                        <option value="mcq" selected>Multiple choice</option>
                        <option value="writing">Writing</option>
                    </select>
                </div>
            </div>
            <div class="qh-options mt-3 space-y-2"></div>
            <button type="button" class="qh-add-option exam-add-link mt-2">+ Add option</button>
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
        updateSummary();
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
            updateSummary();
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

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function updateSummary() {
        const type = document.querySelector('.qh-type-radio:checked')?.value || 'file';
        setText('summary-type', type === 'quiz' ? 'Quiz' : 'File submission');

        const dueInput = document.getElementById('due_at');
        let dueLabel = 'No due date';
        if (dueInput?.value) {
            const parsed = new Date(dueInput.value);
            dueLabel = parsed.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
        setText('summary-due', dueLabel);

        const questionsRow = document.getElementById('summary-questions-row');
        if (type === 'quiz') {
            const qBlocks = questionsContainer.querySelectorAll('.qh-question');
            let totalMarks = 0;
            qBlocks.forEach((b) => { totalMarks += Number(b.querySelector('input[name*="[marks]"]')?.value || 0); });
            setText('summary-marks', totalMarks);
            setText('summary-questions', qBlocks.length);
            if (questionsRow) questionsRow.style.display = '';
        } else {
            setText('summary-marks', document.getElementById('max_marks')?.value || 0);
            if (questionsRow) questionsRow.style.display = 'none';
        }
    }

    form.addEventListener('input', updateSummary);
    form.addEventListener('change', updateSummary);

    syncType();
    const oldQuestions = @json(old('questions', []));
    if (oldQuestions.length) {
        oldQuestions.forEach((question) => addQuestion(question));
    } else if (quizPanels[0] && quizPanels[0].style.display !== 'none') {
        addQuestion();
    }

    updateSummary();
})();
</script>
@endpush
