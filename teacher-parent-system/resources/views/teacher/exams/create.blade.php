@extends('layouts.teacher')
@section('title', 'New Exam')

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
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">New Exam</h1>
            <p class="db-sub">For class <span>{{ $schoolClass->name }}</span> · add subjects now, marks are entered afterward.</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.exams.index') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to exams
            </a>
        </div>
    </div>

    <div class="content-grid">
        <form method="POST" action="{{ route('teacher.exams.store') }}" class="space-y-5" id="exam-form">
            @csrf

            <section class="exam-form-card space-y-4">
                <div>
                    <label for="name" class="exam-field-label">Exam name</label>
                    <input id="name" name="name" value="{{ old('name') }}" maxlength="255" required
                           placeholder="e.g. First Term Test" class="att-input">
                    @error('name')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="academic_year" class="exam-field-label">Academic year</label>
                        <input id="academic_year" name="academic_year" value="{{ old('academic_year', now()->year.'/'.(now()->year + 1)) }}"
                               placeholder="2025/2026" required class="att-input">
                        @error('academic_year')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="term" class="exam-field-label">Term</label>
                        <select id="term" name="term" required class="att-input">
                            @foreach([1 => 'Term 1', 2 => 'Term 2', 3 => 'Term 3'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('term') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('term')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="exam_date" class="exam-field-label">Exam date</label>
                        <input type="date" id="exam_date" name="exam_date" value="{{ old('exam_date') }}" required class="att-input">
                        @error('exam_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="term_start_date" class="exam-field-label">Term start date</label>
                        <input type="date" id="term_start_date" name="term_start_date" value="{{ old('term_start_date') }}" required class="att-input">
                        <p class="mt-1 text-[11px] text-gray-400">Used for the attendance-vs-performance summary.</p>
                        @error('term_start_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="term_end_date" class="exam-field-label">Term end date</label>
                        <input type="date" id="term_end_date" name="term_end_date" value="{{ old('term_end_date') }}" required class="att-input">
                        @error('term_end_date')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="exam-form-card">
                <div class="flex items-center justify-between">
                    <p class="exam-field-label" style="margin-bottom:0">Subjects</p>
                    <button type="button" id="es-add-subject" class="exam-add-link">+ Add subject</button>
                </div>
                @error('subjects')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                <div id="es-subjects" class="mt-3 space-y-2"></div>
            </section>

            <button type="submit" class="qa-btn primary w-full justify-center" style="width:100%;padding:12px;font-size:13.5px">Create exam</button>
        </form>

        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                    Exam Summary
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-row"><span class="summary-label">Class</span><span class="summary-value">{{ $schoolClass->name }}</span></div>
                <div class="summary-row"><span class="summary-label">Term</span><span class="summary-value" id="summary-term">—</span></div>
                <div class="summary-row"><span class="summary-label">Academic Year</span><span class="summary-value" id="summary-year">—</span></div>
                <div class="summary-row"><span class="summary-label">Exam Date</span><span class="summary-value" id="summary-date">—</span></div>
                <div class="summary-row"><span class="summary-label">Subjects</span><span class="summary-value" id="summary-subjects">0</span></div>
                <div class="summary-row"><span class="summary-label">Total Marks</span><span class="summary-value" id="summary-marks">0</span></div>
            </div>
            <div class="tips-box">
                Tip: subject names can't be changed once marks are entered for them, so double-check spelling before saving.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const subjectsContainer = document.getElementById('es-subjects');
    const addSubjectBtn = document.getElementById('es-add-subject');
    const form = document.getElementById('exam-form');
    let sCounter = 0;

    function subjectRowHtml(index) {
        return `<div class="flex items-center gap-2" data-subject-row data-sindex="${index}">
            <input type="text" name="subjects[${index}][name]" placeholder="Subject name" class="att-input flex-1" style="padding:8px 10px" required>
            <input type="number" name="subjects[${index}][max_mark]" placeholder="Max" value="100" min="1" max="1000" class="att-input" style="padding:8px 10px;width:90px" required>
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
        updateSummary();
    }

    // Bound as a wrapper — passing addSubject directly would receive the
    // click Event as its `data` argument (same pitfall hit in the homework
    // quiz builder), silently corrupting the row's fields.
    addSubjectBtn.addEventListener('click', () => addSubject());

    subjectsContainer.addEventListener('click', (event) => {
        const removeBtn = event.target.closest('.es-remove-subject');
        if (removeBtn) {
            removeBtn.closest('[data-subject-row]').remove();
            updateSummary();
        }
    });

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function updateSummary() {
        const year = document.getElementById('academic_year')?.value.trim();
        const termSelect = document.getElementById('term');
        const termLabel = termSelect ? termSelect.options[termSelect.selectedIndex]?.text : null;
        const dateInput = document.getElementById('exam_date');
        let dateLabel = '—';
        if (dateInput?.value) {
            const parsed = new Date(dateInput.value + 'T00:00:00');
            dateLabel = parsed.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
        const subjectRows = subjectsContainer.querySelectorAll('[data-subject-row]');
        let totalMarks = 0;
        subjectRows.forEach((row) => { totalMarks += Number(row.querySelector('input[type="number"]')?.value || 0); });

        setText('summary-year', year || '—');
        setText('summary-term', termLabel || '—');
        setText('summary-date', dateLabel);
        setText('summary-subjects', subjectRows.length);
        setText('summary-marks', totalMarks);
    }

    form.addEventListener('input', updateSummary);
    form.addEventListener('change', updateSummary);

    const oldSubjects = @json(old('subjects', []));
    if (oldSubjects.length) {
        oldSubjects.forEach((subject) => addSubject(subject));
    } else {
        addSubject();
    }

    updateSummary();
})();
</script>
@endpush
