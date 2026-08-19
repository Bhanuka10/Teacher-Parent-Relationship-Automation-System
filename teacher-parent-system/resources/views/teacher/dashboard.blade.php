@extends('layouts.teacher')
@section('title', 'Dashboard')

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
        border: 1.5px solid transparent;
    }
    .qa-btn svg { width: 15px; height: 15px; }
    .qa-btn.primary { background: var(--t-accent); color: #fff; box-shadow: 0 4px 14px rgba(15,118,110,.28); }
    .qa-btn.primary:hover { background: #0d5f58; transform: translateY(-1px); }
    .qa-btn.ghost { background: #fff; color: var(--t-accent); border-color: #d1fae5; }
    .qa-btn.ghost:hover { background: var(--t-accent-light); border-color: var(--t-accent-mid); }

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
        transition: transform .15s, box-shadow .15s; text-decoration: none;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.09); }
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

    /* ── chart grid ── */
    .chart-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
    @media(max-width: 900px) { .chart-grid, .content-grid { grid-template-columns: 1fr; } }

    .chart-card, .today-card, .side-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .today-card { display: flex; flex-direction: column; }
    .chart-title {
        font-size: 14px; font-weight: 800; color: #111827; margin: 0 0 4px;
        display: flex; align-items: center; gap: 8px;
    }
    .chart-sub { font-size: 12px; color: #9ca3af; margin: 0 0 18px; }
    .chart-wrap { position: relative; }

    .today-meta { display: flex; gap: 12px; margin-top: 16px; flex-wrap: wrap; }
    .today-chip { flex: 1; min-width: 80px; border-radius: 10px; padding: 10px 14px; text-align: center; }
    .today-chip.present { background: #d1fae5; }
    .today-chip.absent  { background: #fee2e2; }
    .today-chip.pending { background: #f3f4f6; }
    .today-chip .val { font-size: 20px; font-weight: 800; }
    .today-chip.present .val { color: #065f46; }
    .today-chip.absent  .val { color: #991b1b; }
    .today-chip.pending .val { color: #4b5563; }
    .today-chip .lbl { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .doughnut-wrap { max-width: 170px; margin: 0 auto; }

    /* ── content grid: homework + roster ── */
    .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }

    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .section-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }
    .section-link { font-size: 12px; font-weight: 600; color: var(--t-accent); text-decoration: none; }
    .section-link:hover { text-decoration: underline; }

    .hw-row {
        display: flex; align-items: flex-start; gap: 12px; padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background .12s;
    }
    .hw-row:last-child { border-bottom: none; }
    .hw-row:hover { background: #fafafa; }
    .hw-title { font-size: 13px; font-weight: 700; color: #111827; }
    .hw-meta  { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }

    .roster-row {
        display: flex; align-items: center; gap: 10px; padding: 10px 20px;
        border-bottom: 1px solid #f3f4f6;
    }
    .roster-row:last-child { border-bottom: none; }
    .roster-list { max-height: 380px; overflow-y: auto; }
    .roster-av {
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #fff;
    }
    .roster-nm { font-size: 12.5px; font-weight: 600; color: #111827; }
    .roster-adm { font-size: 10.5px; color: #9ca3af; }

    .status-dot {
        display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0; margin-left: auto;
        border-radius: 20px; padding: 3px 10px; font-size: 10.5px; font-weight: 700;
    }
    .status-dot.present { background: #d1fae5; color: #065f46; }
    .status-dot.absent  { background: #fee2e2; color: #991b1b; }
    .status-dot.unmarked{ background: #f3f4f6; color: #6b7280; }
    .dot { width: 5px; height: 5px; border-radius: 50%; }
    .status-dot.present .dot { background: #10b981; }
    .status-dot.absent  .dot { background: #ef4444; }
    .status-dot.unmarked .dot{ background: #9ca3af; }

    .due-banner {
        display: flex; align-items: center; gap: 10px;
        background: var(--t-accent-light); border: 1px solid #99f6e4;
        border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        font-size: 12.5px; color: #0f766e;
    }
    .due-banner strong { font-weight: 800; }
</style>
@endpush

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $avatarColours = ['#0f766e','#6366f1','#8b5cf6','#ec4899','#f59e0b','#0ea5e9','#ef4444','#14b8a6'];
@endphp

<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">{{ $greeting }}, {{ $teacher->name }}</h1>
            <p class="db-sub">
                {{ now()->format('l, F j, Y') }} ·
                @if($schoolClass)
                    Class <span>{{ $schoolClass->name }}</span>
                @else
                    <span>No class assigned yet</span>
                @endif
            </p>
        </div>
        @if($schoolClass)
            <div class="quick-actions">
                <a href="{{ route('teacher.attendance.index') }}" class="qa-btn primary">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                    Mark Attendance
                </a>
                <a href="{{ route('teacher.homework.create') }}" class="qa-btn ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Homework
                </a>
                <a href="{{ route('teacher.messages.index') }}" class="qa-btn ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                    Messages
                </a>
                <a href="{{ route('teacher.students.create') }}" class="qa-btn ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Add Student
                </a>
            </div>
        @endif
    </div>

    @if(!$schoolClass)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V8.5L12 3l8 5.5V21M9 21v-6h6v6M8 10h.01M16 10h.01"/></svg>
                <p>No class is assigned to your account yet.</p>
                <p class="ui-empty-sub">Contact an admin to get a class assigned — your dashboard will populate automatically.</p>
            </div>
        </div>
    @else
        @if($nextDue)
            <div class="due-banner">
                <svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Next deadline: <strong>{{ $nextDue->title }}</strong> is due {{ $nextDue->due_at->format('D, M j \a\t g:i A') }} ({{ $nextDue->due_at->diffForHumans() }}).
            </div>
        @endif

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card" style="cursor:default">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $studentCount }}</div>
                    <div class="kpi-label">Students in {{ $schoolClass->name }}</div>
                </div>
            </div>

            <a href="{{ route('teacher.attendance.index') }}" class="kpi-card">
                <div class="kpi-icon {{ $todayMarked ? 'teal' : 'amber' }}">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $todayMarked ? $todayRate.'%' : 'Pending' }}</div>
                    <div class="kpi-label">Today's Attendance</div>
                    <div class="kpi-note">{{ $todayMarked ? "{$todayPresent} present · {$todayAbsent} absent" : 'Not marked yet — tap to mark' }}</div>
                </div>
            </a>

            <a href="{{ route('teacher.homework.index') }}" class="kpi-card">
                <div class="kpi-icon {{ $toGradeCount ? 'rose' : 'teal' }}">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $toGradeCount }}</div>
                    <div class="kpi-label">Submissions to Grade</div>
                    <div class="kpi-note">{{ $homeworkCount }} homework assigned total</div>
                </div>
            </a>

            <a href="{{ route('teacher.messages.index') }}" class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $unreadMessages }}</div>
                    <div class="kpi-label">Unread Messages</div>
                </div>
            </a>
        </div>

        {{-- ── Charts ── --}}
        <div class="chart-grid">
            <div class="chart-card">
                <p class="chart-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#0f766e" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    7-Day Attendance Trend
                </p>
                <p class="chart-sub">Present vs Absent in {{ $schoolClass->name }} — last 7 days</p>
                <div class="chart-wrap"><canvas id="trendChart" height="110"></canvas></div>
            </div>

            <div class="today-card">
                <p class="chart-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#0f766e" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Today's Status
                </p>
                <p class="chart-sub">{{ now()->format('l, M j') }}</p>
                <div class="doughnut-wrap"><canvas id="todayChart"></canvas></div>
                <div class="today-meta">
                    <div class="today-chip present"><div class="val">{{ $todayPresent }}</div><div class="lbl">Present</div></div>
                    <div class="today-chip absent"><div class="val">{{ $todayAbsent }}</div><div class="lbl">Absent</div></div>
                    <div class="today-chip pending"><div class="val">{{ max($studentCount - $todayPresent - $todayAbsent, 0) }}</div><div class="lbl">Unmarked</div></div>
                </div>
            </div>
        </div>

        {{-- ── Recent homework + roster ── --}}
        <div class="content-grid">
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Recent Homework
                    </div>
                    <a href="{{ route('teacher.homework.index') }}" class="section-link">View all &rarr;</a>
                </div>

                @forelse($recentHomeworks as $homework)
                    @php $pct = $homework->total_count > 0 ? round(($homework->submitted_count / $homework->total_count) * 100) : 0; @endphp
                    <a href="{{ route('teacher.homework.show', $homework) }}" class="hw-row">
                        <span class="ui-tag {{ $homework->type === 'quiz' ? 'ui-tag-rose' : 'ui-tag-indigo' }}" style="margin-top:1px">{{ $homework->type === 'quiz' ? 'Quiz' : 'File' }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="hw-title block truncate">{{ $homework->title }}</span>
                            <span class="hw-meta block">{{ $homework->due_at ? 'Due '.$homework->due_at->format('d M, h:i A') : 'No due date' }}</span>
                            <span class="ui-progress mt-2" style="max-width:220px"><span class="ui-progress-fill" style="width:{{ $pct }}%"></span></span>
                        </span>
                        <span class="flex shrink-0 flex-col items-end gap-1">
                            <span class="ui-tag ui-tag-gray">{{ $homework->submitted_count }}/{{ $homework->total_count }} in</span>
                            @if($homework->submitted_count > $homework->graded_count)
                                <span class="ui-tag ui-tag-rose">{{ $homework->submitted_count - $homework->graded_count }} to grade</span>
                            @else
                                <span class="ui-tag ui-tag-teal">All graded</span>
                            @endif
                        </span>
                    </a>
                @empty
                    <div class="ui-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <p>No homework assigned yet.</p>
                        <p class="ui-empty-sub">Create one to get started.</p>
                    </div>
                @endforelse
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Today's Roster
                    </div>
                    <a href="{{ route('teacher.attendance.history') }}" class="section-link">History &rarr;</a>
                </div>
                <div class="roster-list">
                    @forelse($roster as $entry)
                        @php
                            $student = $entry['student'];
                            $status = $entry['status'];
                            $aColor = $avatarColours[ord(strtolower($student->name[0] ?? 'a')) % count($avatarColours)];
                        @endphp
                        <div class="roster-row">
                            <div class="roster-av" style="background:{{ $aColor }}">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <div class="roster-nm truncate">{{ $student->name }}</div>
                                <div class="roster-adm">{{ $student->admission_number }}</div>
                            </div>
                            <span class="status-dot {{ $status === 'present' ? 'present' : ($status === 'absent' ? 'absent' : 'unmarked') }}">
                                <span class="dot"></span>
                                {{ $status ? ucfirst($status) : 'Unmarked' }}
                            </span>
                        </div>
                    @empty
                        <div class="ui-empty">
                            <p>No students in this class yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>

@if($schoolClass)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    Chart.defaults.font.family = "'Poppins', 'system-ui', sans-serif";
    Chart.defaults.color = '#6b7280';

    const trendLabels  = @json($trendLabels);
    const trendPresent = @json($trendPresent);
    const trendAbsent  = @json($trendAbsent);
    const todayPresent = {{ $todayPresent }};
    const todayAbsent  = {{ $todayAbsent }};
    const todayUnmarked = {{ max($studentCount - $todayPresent - $todayAbsent, 0) }};

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                { label: 'Present', data: trendPresent, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)', borderWidth: 2.5, pointBackgroundColor: '#10b981', pointRadius: 4, tension: 0.4, fill: true },
                { label: 'Absent',  data: trendAbsent,  borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.08)', borderWidth: 2.5, pointBackgroundColor: '#ef4444', pointRadius: 4, tension: 0.4, fill: true },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: { cornerRadius: 8 },
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } },
            },
        },
    });

    const hasToday = todayPresent + todayAbsent + todayUnmarked > 0;
    new Chart(document.getElementById('todayChart'), {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Unmarked'],
            datasets: [{
                data: hasToday ? [todayPresent, todayAbsent, todayUnmarked] : [1],
                backgroundColor: hasToday ? ['#10b981', '#ef4444', '#e5e7eb'] : ['#e5e7eb'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { display: false }, tooltip: { cornerRadius: 8 } },
        },
    });
})();
</script>
@endif
@endsection
