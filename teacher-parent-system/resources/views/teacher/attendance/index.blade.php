@extends('layouts.teacher')
@section('title', 'Mark Attendance')

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
    .qa-btn.ghost-rose { background: #fff; color: #be123c; border-color: #fecdd3; }
    .qa-btn.ghost-rose:hover { background: #ffe4e6; border-color: #fda4af; }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 16px;
    }
    .kpi-icon {
        width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .kpi-icon.teal   { background: var(--t-accent-light); color: var(--t-accent); }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-icon.rose   { background: #ffe4e6; color: #be123c; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .kpi-note  { font-size: 10.5px; color: #9ca3af; margin-top: 3px; }

    .due-banner {
        display: flex; align-items: center; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca;
        border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        font-size: 12.5px; color: #991b1b;
    }
    .due-banner strong { font-weight: 800; }

    /* ── toolbar ── */
    .toolbar-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 18px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
        margin-bottom: 20px;
    }
    .toolbar-field label {
        display: block; font-size: 10.5px; font-weight: 700; color: #9ca3af;
        text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px;
    }
    .att-input {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 9px 13px;
        font-size: 13px; color: #111827; background: #fafafa; outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .att-input:focus { border-color: var(--t-accent-mid); box-shadow: 0 0 0 3px rgba(20,184,166,.15); background: #fff; }
    .att-search-wrap { position: relative; flex: 1; min-width: 200px; }
    .att-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #9ca3af; }
    .att-search-wrap input { width: 100%; padding-left: 32px; box-sizing: border-box; }

    /* ── section card / roster-style rows ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }

    .att-row {
        display: flex; align-items: center; gap: 12px; padding: 12px 20px;
        border-bottom: 1px solid #f3f4f6; transition: background .12s;
    }
    .att-row:last-child { border-bottom: none; }
    .att-row:hover { background: #fafafa; }
    .att-row.is-hidden { display: none; }
    .roster-av {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff;
    }
    .roster-nm { font-size: 13px; font-weight: 600; color: #111827; }
    .roster-adm { font-size: 11px; color: #9ca3af; }

    /* ── clickable status-dot toggle ── */
    .att-toggle { display: inline-flex; gap: 8px; margin-left: auto; flex-shrink: 0; }
    .att-toggle input { position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none; }
    .status-dot {
        display: inline-flex; align-items: center; gap: 5px; cursor: pointer; user-select: none;
        border-radius: 20px; padding: 5px 12px; font-size: 10.5px; font-weight: 700;
        background: #f3f4f6; color: #9ca3af; transition: all .15s; border: 1.5px solid transparent;
    }
    .status-dot .dot { width: 5px; height: 5px; border-radius: 50%; background: #9ca3af; }
    .status-dot.present.is-active { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
    .status-dot.present.is-active .dot { background: #10b981; }
    .status-dot.absent.is-active { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
    .status-dot.absent.is-active .dot { background: #ef4444; }

    .save-bar {
        position: sticky; bottom: 0; left: 0; right: 0; margin-top: 20px; z-index: 10;
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        background: rgba(255,255,255,.94); backdrop-filter: blur(6px);
        border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 20px;
        box-shadow: 0 -6px 20px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.06);
    }
    .save-summary { font-size: 12.5px; color: #6b7280; }
    .save-summary strong { color: #111827; }

    @media (max-width: 640px) {
        .save-bar { flex-direction: column; align-items: stretch; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">Mark Attendance</h1>
            <p class="db-sub">
                {{ \Illuminate\Support\Carbon::parse($date)->format('l, F j, Y') }} ·
                @if($schoolClass)
                    Class <span>{{ $schoolClass->name }}</span>
                @else
                    <span>No class assigned yet</span>
                @endif
            </p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('teacher.attendance.history') }}" class="qa-btn ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                View History
            </a>
        </div>
    </div>

    @if($errors->has('attendance'))
        <div class="due-banner">
            <svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            {{ $errors->first('attendance') }}
        </div>
    @endif

    @if(!$schoolClass)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned before marking attendance.</p>
            </div>
        </div>
    @else
        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $students->count() }}</div>
                    <div class="kpi-label">Students in {{ $schoolClass->name }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="kpi-val" id="stat-present">0</div>
                    <div class="kpi-label">Present</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon rose">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div class="kpi-val" id="stat-absent">0</div>
                    <div class="kpi-label">Absent</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg>
                </div>
                <div>
                    <div class="kpi-val" id="stat-rate">0%</div>
                    <div class="kpi-label">Attendance Rate</div>
                    <div class="kpi-note" id="stat-note">0/{{ $students->count() }} marked present</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('teacher.attendance.store') }}" id="attendance-form">
            @csrf

            <div class="toolbar-card">
                <div class="toolbar-field">
                    <label for="att-date">Attendance Date</label>
                    <input type="date" id="att-date" name="date" value="{{ $date }}"
                        data-attendance-index-url="{{ route('teacher.attendance.index') }}"
                        class="att-input @error('date') border-red-400 @enderror">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="att-search-wrap">
                    <label for="student-search" class="sr-only">Search students</label>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="text" id="student-search" class="att-input" placeholder="Search students by name…" autocomplete="off">
                </div>

                <button type="button" id="mark-all-present" class="qa-btn ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    All Present
                </button>
                <button type="button" id="mark-all-absent" class="qa-btn ghost-rose">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    All Absent
                </button>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Students
                    </div>
                </div>

                @forelse($students as $student)
                    @php
                        $record = $attendanceByStudent->get($student->id);
                        $status = old('status.'.$student->id, $record?->status ?? 'absent');
                        $indexNumber = $student->profile?->index_number ?? $student->admission_number;
                        $avatarColours = ['#0f766e','#6366f1','#8b5cf6','#ec4899','#f59e0b','#0ea5e9','#ef4444','#14b8a6'];
                        $aColor = $avatarColours[ord(strtolower($student->name[0] ?? 'a')) % count($avatarColours)];
                    @endphp
                    <div class="att-row" data-name="{{ strtolower($student->name) }}">
                        <div class="roster-av" style="background:{{ $aColor }}">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                        <div class="min-w-0">
                            <div class="roster-nm truncate">{{ $student->name }}</div>
                            <div class="roster-adm">Index {{ $indexNumber }}</div>
                        </div>
                        <div class="att-toggle">
                            <label>
                                <input type="radio" class="attendance-radio" name="status[{{ $student->id }}]" value="present" {{ $status === 'present' ? 'checked' : '' }}>
                                <span class="status-dot present {{ $status === 'present' ? 'is-active' : '' }}">
                                    <span class="dot"></span>Present
                                </span>
                            </label>
                            <label>
                                <input type="radio" class="attendance-radio" name="status[{{ $student->id }}]" value="absent" {{ $status === 'absent' ? 'checked' : '' }}>
                                <span class="status-dot absent {{ $status === 'absent' ? 'is-active' : '' }}">
                                    <span class="dot"></span>Absent
                                </span>
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="ui-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <p>No students in your class.</p>
                    </div>
                @endforelse
                <div class="ui-empty" id="att-no-results" style="display:none">
                    <p>No students match your search.</p>
                </div>
            </div>

            @if($students->count())
                <div class="save-bar">
                    <p class="save-summary">
                        <strong id="live-present-count">0</strong> present ·
                        <strong id="live-absent-count">0</strong> absent
                        of <strong>{{ $students->count() }}</strong> students
                    </p>
                    <button type="submit" class="qa-btn primary">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
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
        const dateInput = document.getElementById('att-date');

        // Switching the date here must reload the roster for that date —
        // otherwise the on-screen radios (from whatever date was originally
        // loaded) get silently saved against the newly-picked date instead,
        // overwriting that day's real attendance with stale/default values.
        dateInput?.addEventListener('change', () => {
            if (!dateInput.value) return;
            window.location.href = `${dateInput.dataset.attendanceIndexUrl}?date=${dateInput.value}`;
        });

        function refreshRow(row) {
            const presentInput = row.querySelector('input[value="present"]');
            const absentInput = row.querySelector('input[value="absent"]');
            const presentPill = row.querySelector('.status-dot.present');
            const absentPill = row.querySelector('.status-dot.absent');
            if (!presentInput || !absentInput || !presentPill || !absentPill) return;

            const isPresent = presentInput.checked;
            presentPill.classList.toggle('is-active', isPresent);
            absentPill.classList.toggle('is-active', !isPresent);
        }

        function updateStats() {
            const total = radios.length ? new Set(Array.from(radios).map(r => r.name)).size : 0;
            const present = document.querySelectorAll('.attendance-radio[value="present"]:checked').length;
            const absent = Math.max(total - present, 0);
            const rate = total ? Math.round((present / total) * 100) : 0;

            const setText = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
            setText('stat-present', present);
            setText('stat-absent', absent);
            setText('stat-rate', rate + '%');
            setText('stat-note', present + '/' + total + ' marked present');
            setText('live-present-count', present);
            setText('live-absent-count', absent);
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

        document.getElementById('mark-all-present')?.addEventListener('click', () => {
            document.querySelectorAll('.attendance-radio[value="present"]').forEach((input) => { input.checked = true; });
            refreshAll();
        });

        document.getElementById('mark-all-absent')?.addEventListener('click', () => {
            document.querySelectorAll('.attendance-radio[value="absent"]').forEach((input) => { input.checked = true; });
            refreshAll();
        });

        searchInput?.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;
            rows.forEach((row) => {
                const matches = !term || row.dataset.name.includes(term);
                row.classList.toggle('is-hidden', !matches);
                if (matches) visibleCount++;
            });
            if (noResults) noResults.style.display = (term && visibleCount === 0) ? 'block' : 'none';
        });

        refreshAll();
    })();
</script>
@endpush
@endsection
