@extends('layouts.teacher')
@section('title', 'Mark Attendance')

@push('styles')
<style>
    /* ── Mark Attendance page ── */
    .att-ghost-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; border: 1px solid #e5e7eb; color: #0f766e;
        font-size: .82rem; font-weight: 600; padding: 9px 16px; border-radius: 10px;
        text-decoration: none; transition: all .15s;
    }
    .att-ghost-btn:hover { background: #f0fdfa; border-color: #99f6e4; }
    .att-ghost-btn svg { width: 15px; height: 15px; }

    .att-stat-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
    .att-stat-icon {
        width: 30px; height: 30px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .att-stat-icon svg { width: 15px; height: 15px; }
    .att-stat-icon-gray  { background: #f3f4f6; color: #6b7280; }
    .att-stat-icon-green { background: #dcfce7; color: #15803d; }
    .att-stat-icon-red   { background: #fee2e2; color: #b91c1c; }
    .att-stat-icon-teal  { background: #ccfbf1; color: #0f766e; }

    .att-toolbar-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
        padding: 18px 20px; margin-bottom: 18px;
        display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .att-toolbar-field label {
        display: block; font-size: .72rem; font-weight: 600; color: #6b7280;
        text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px;
    }
    .att-search-wrap { position: relative; flex: 1; min-width: 220px; }
    .att-search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        width: 15px; height: 15px; color: #9ca3af; pointer-events: none;
    }
    .att-search-wrap input.ui-input { padding-left: 34px; }

    .att-mark-btn {
        display: inline-flex; align-items: center; gap: 6px;
        border: none; cursor: pointer; font-size: .8rem; font-weight: 600;
        padding: 10.5px 16px; border-radius: 10px; transition: all .15s; white-space: nowrap;
    }
    .att-mark-btn svg { width: 14px; height: 14px; }
    .att-mark-present { background: #ccfbf1; color: #0f766e; }
    .att-mark-present:hover { background: #99f6e4; }
    .att-mark-absent { background: #ffe4e6; color: #be123c; }
    .att-mark-absent:hover { background: #fecdd3; }

    .att-row {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        padding: 13px 20px; border-bottom: 1px solid #f3f4f6; transition: background .15s;
    }
    .att-row:last-child { border-bottom: none; }
    .att-row:hover { background: #fafafa; }
    .att-row.is-absent { background: #fef2f2; }
    .att-row.is-absent:hover { background: #fee5e5; }
    .att-row.is-hidden { display: none; }

    .att-toggle { display: inline-flex; border: 1.5px solid #e5e7eb; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
    .att-toggle-btn {
        display: flex; align-items: center; gap: 5px; cursor: pointer; user-select: none;
        padding: 8px 14px; font-size: .76rem; font-weight: 700; color: #9ca3af; background: #fff;
        transition: all .15s;
    }
    .att-toggle-btn svg { width: 13px; height: 13px; }
    .att-toggle-present.is-active { background: #dcfce7; color: #15803d; }
    .att-toggle-absent.is-active { background: #fee2e2; color: #b91c1c; }

    .att-save-bar {
        position: sticky; bottom: 0; left: 0; right: 0; margin-top: 20px; z-index: 10;
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        background: rgba(255,255,255,.92); backdrop-filter: blur(6px);
        border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 20px;
        box-shadow: 0 -6px 20px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.06);
    }
    .att-save-summary { font-size: .85rem; color: #374151; }
    .att-save-summary strong { color: #111827; }
    .att-no-results { display: none; padding: 40px 20px; text-align: center; color: #9ca3af; font-size: .85rem; }

    @media (max-width: 640px) {
        .att-save-bar { flex-direction: column; align-items: stretch; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl">

    <div class="mb-7 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="ui-hero-icon ui-hero-teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Mark Attendance</h1>
                <p class="mt-0.5 text-sm text-gray-500">Record who's present today, one tap per student.</p>
            </div>
        </div>
        <a href="{{ route('teacher.attendance.history') }}" class="att-ghost-btn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            View History
        </a>
    </div>

    @if($errors->has('attendance'))
        <div class="flash-error">
            <svg style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
            {{ $errors->first('attendance') }}
        </div>
    @endif

    @if(!$schoolClass)
        <div class="ui-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before marking attendance.</p>
            </div>
        </div>
    @else
        {{-- Live stats --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-5">
            <div class="ui-stat-card ui-animate-in ui-animate-in-1">
                <div class="att-stat-top">
                    <p class="ui-stat-label">{{ $schoolClass->name }}</p>
                    <span class="att-stat-icon att-stat-icon-gray">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" id="stat-total">{{ $students->count() }}</p>
            </div>
            <div class="ui-stat-card ui-animate-in ui-animate-in-2">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Present</p>
                    <span class="att-stat-icon att-stat-icon-green">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="color:#15803d" id="stat-present">0</p>
            </div>
            <div class="ui-stat-card ui-animate-in ui-animate-in-3">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Absent</p>
                    <span class="att-stat-icon att-stat-icon-red">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="color:#b91c1c" id="stat-absent">0</p>
            </div>
            <div class="ui-stat-card ui-animate-in ui-animate-in-4">
                <div class="att-stat-top">
                    <p class="ui-stat-label">Attendance Rate</p>
                    <span class="att-stat-icon att-stat-icon-teal">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
                    </span>
                </div>
                <p class="ui-stat-value" style="color:#0f766e" id="stat-rate">0%</p>
                <span class="ui-progress mt-2">
                    <span class="ui-progress-fill" id="stat-rate-fill" style="width:0%;background:linear-gradient(90deg,#5eead4,#0f766e)"></span>
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('teacher.attendance.store') }}" id="attendance-form">
            @csrf

            <div class="att-toolbar-card">
                <div class="att-toolbar-field">
                    <label for="att-date">Attendance Date</label>
                    <input type="date" id="att-date" name="date" value="{{ $date }}"
                        class="ui-input @error('date') border-red-400 @enderror" style="width:auto">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="att-search-wrap">
                    <label for="student-search" class="sr-only">Search students</label>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="text" id="student-search" class="ui-input" placeholder="Search students by name…" autocomplete="off">
                </div>

                <button type="button" id="mark-all-present" class="att-mark-btn att-mark-present">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    All Present
                </button>
                <button type="button" id="mark-all-absent" class="att-mark-btn att-mark-absent">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    All Absent
                </button>
            </div>

            <div class="ui-card overflow-hidden">
                @forelse($students as $student)
                    @php
                        $record = $attendanceByStudent->get($student->id);
                        $status = old('status.'.$student->id, $record?->status ?? 'present');
                        $indexNumber = $student->profile?->index_number ?? $student->admission_number;
                        $initials = collect(explode(' ', $student->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                        $colours = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
                        $avatarColor = $colours[ord(strtolower($student->name[0] ?? 'a')) % count($colours)];
                    @endphp
                    <div class="att-row {{ $status === 'absent' ? 'is-absent' : '' }}" data-name="{{ strtolower($student->name) }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="ui-avatar" style="background:{{ $avatarColor }};color:#fff">{{ strtoupper($initials) }}</span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-gray-800">{{ $student->name }}</span>
                                <span class="block text-xs text-gray-400">Index {{ $indexNumber }}</span>
                            </span>
                        </div>
                        <div class="att-toggle">
                            <label class="cursor-pointer">
                                <input type="radio" class="sr-only attendance-radio" name="status[{{ $student->id }}]" value="present" {{ $status === 'present' ? 'checked' : '' }}>
                                <span class="att-toggle-btn att-toggle-present {{ $status === 'present' ? 'is-active' : '' }}">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Present
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" class="sr-only attendance-radio" name="status[{{ $student->id }}]" value="absent" {{ $status === 'absent' ? 'checked' : '' }}>
                                <span class="att-toggle-btn att-toggle-absent {{ $status === 'absent' ? 'is-active' : '' }}">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Absent
                                </span>
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="ui-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"/></svg>
                        <p>No students in your class.</p>
                    </div>
                @endforelse
                <div class="att-no-results" id="att-no-results">No students match your search.</div>
            </div>

            @if($students->count())
                <div class="att-save-bar">
                    <p class="att-save-summary">
                        <strong id="live-present-count">0</strong> present ·
                        <strong id="live-absent-count">0</strong> absent
                        of <strong>{{ $students->count() }}</strong> students
                    </p>
                    <button type="submit" class="ui-submit-btn">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Save Attendance
                    </button>
                </div>
            @endif
        </form>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const radios = document.querySelectorAll('.attendance-radio');
        const rows = document.querySelectorAll('.att-row');
        const searchInput = document.getElementById('student-search');
        const noResults = document.getElementById('att-no-results');

        function refreshRow(row) {
            const presentInput = row.querySelector('input[value="present"]');
            const absentInput = row.querySelector('input[value="absent"]');
            const presentBtn = row.querySelector('.att-toggle-present');
            const absentBtn = row.querySelector('.att-toggle-absent');
            if (!presentInput || !absentInput || !presentBtn || !absentBtn) return;

            const isPresent = presentInput.checked;
            presentBtn.classList.toggle('is-active', isPresent);
            absentBtn.classList.toggle('is-active', !isPresent);
            row.classList.toggle('is-absent', !isPresent);
        }

        function updateStats() {
            const total = radios.length ? new Set(Array.from(radios).map(r => r.name)).size : 0;
            const present = document.querySelectorAll('input.attendance-radio[value="present"]:checked').length;
            const absent = Math.max(total - present, 0);
            const rate = total ? Math.round((present / total) * 100) : 0;

            const setText = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
            setText('stat-present', present);
            setText('stat-absent', absent);
            setText('stat-rate', rate + '%');
            setText('live-present-count', present);
            setText('live-absent-count', absent);

            const fill = document.getElementById('stat-rate-fill');
            if (fill) fill.style.width = rate + '%';
        }

        function refreshAll() {
            rows.forEach(refreshRow);
            updateStats();
        }

        radios.forEach((radio) => {
            radio.addEventListener('change', () => {
                refreshRow(radio.closest('.att-row'));
                updateStats();
            });
        });

        const markAllPresentBtn = document.getElementById('mark-all-present');
        if (markAllPresentBtn) {
            markAllPresentBtn.addEventListener('click', () => {
                document.querySelectorAll('input.attendance-radio[value="present"]').forEach((input) => { input.checked = true; });
                refreshAll();
            });
        }

        const markAllAbsentBtn = document.getElementById('mark-all-absent');
        if (markAllAbsentBtn) {
            markAllAbsentBtn.addEventListener('click', () => {
                document.querySelectorAll('input.attendance-radio[value="absent"]').forEach((input) => { input.checked = true; });
                refreshAll();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const term = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;
                rows.forEach((row) => {
                    const matches = !term || row.dataset.name.includes(term);
                    row.classList.toggle('is-hidden', !matches);
                    if (matches) visibleCount++;
                });
                if (noResults) noResults.style.display = (term && visibleCount === 0) ? 'block' : 'none';
            });
        }

        refreshAll();
    })();
</script>
@endpush
@endsection
