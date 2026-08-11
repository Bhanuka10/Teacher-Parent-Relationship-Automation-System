@extends('layouts.parent')
@section('title', 'Dashboard')

@push('styles')
<style>
    :root {
        --p-accent:       #c2410c;
        --p-accent-light: #ffedd5;
        --p-accent-mid:   #f97316;
    }

    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0; }
    .db-sub span { color: var(--p-accent); font-weight: 600; }

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
    .qa-btn.primary { background: var(--p-accent); color: #fff; box-shadow: 0 4px 14px rgba(194,65,12,.28); }
    .qa-btn.primary:hover { background: #9a3412; transform: translateY(-1px); }
    .qa-btn.ghost { background: #fff; color: var(--p-accent); border-color: #ffedd5; }
    .qa-btn.ghost:hover { background: var(--p-accent-light); border-color: var(--p-accent-mid); }

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
    .kpi-icon.orange { background: var(--p-accent-light); color: var(--p-accent); }
    .kpi-icon.green  { background: #d1fae5; color: #065f46; }
    .kpi-icon.red    { background: #fee2e2; color: #991b1b; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .kpi-note  { font-size: 10.5px; color: #9ca3af; margin-top: 3px; }

    /* ── chart grid ── */
    .chart-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
    @media(max-width: 900px) { .chart-grid, .content-grid { grid-template-columns: 1fr; } }

    .chart-card, .today-card {
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

    /* ── content grid: homework + results ── */
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
    .section-link { font-size: 12px; font-weight: 600; color: var(--p-accent); text-decoration: none; }
    .section-link:hover { text-decoration: underline; }

    .hw-row, .result-row {
        display: flex; align-items: flex-start; gap: 12px; padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background .12s;
    }
    .hw-row:last-child, .result-row:last-child { border-bottom: none; }
    .hw-row:hover, .result-row:hover { background: #fafafa; }
    .hw-title { font-size: 13px; font-weight: 700; color: #111827; }
    .hw-meta  { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }
    .result-row { align-items: center; }

    .due-banner {
        display: flex; align-items: center; gap: 10px;
        background: var(--p-accent-light); border: 1px solid #fed7aa;
        border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        font-size: 12.5px; color: var(--p-accent);
    }
    .due-banner strong { font-weight: 800; }
</style>
@endpush

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<div class="max-w-7xl">
    <div class="db-head">
        <div>
            <h1 class="db-title">{{ $greeting }}, {{ $parent->name }}</h1>
            <p class="db-sub">
                {{ now()->format('l, F j, Y') }} ·
                @if($student)
                    Tracking <span>{{ $student->name }}</span>{{ $schoolClass ? ' · '.$schoolClass->name : '' }}
                @else
                    <span>No student linked yet</span>
                @endif
            </p>
        </div>
        @if($student)
            <div class="quick-actions">
                <a href="{{ route('parent.homework.index') }}" class="qa-btn primary">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Homework
                </a>
                <a href="{{ route('parent.results.index') }}" class="qa-btn ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                    Results
                </a>
                <a href="{{ route('parent.messages.index') }}" class="qa-btn ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                    Messages
                </a>
            </div>
        @endif
    </div>

    @if(!$student)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5Z"/></svg>
                <p>No student record is linked to this account yet.</p>
                <p class="ui-empty-sub">Contact an admin to have your child linked — your dashboard will populate automatically.</p>
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
                <div class="kpi-icon {{ $todayAttendance ? ($todayAttendance->status === 'present' ? 'green' : 'red') : 'amber' }}">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $todayAttendance ? ucfirst($todayAttendance->status) : 'Not marked' }}</div>
                    <div class="kpi-label">Today's Attendance</div>
                </div>
            </div>

            <div class="kpi-card" style="cursor:default">
                <div class="kpi-icon orange">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $weekRate }}%</div>
                    <div class="kpi-label">7-Day Attendance Rate</div>
                    <div class="kpi-note">{{ $weekPresent }} present · {{ $weekAbsent }} absent</div>
                </div>
            </div>

            <a href="{{ route('parent.homework.index') }}" class="kpi-card">
                <div class="kpi-icon {{ $dueHomeworkCount ? 'amber' : 'orange' }}">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $dueHomeworkCount }}</div>
                    <div class="kpi-label">Homework Due</div>
                </div>
            </a>

            <a href="{{ route('parent.messages.index') }}" class="kpi-card">
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
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#c2410c" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    7-Day Attendance Trend
                </p>
                <p class="chart-sub">{{ $student->name }}'s attendance — last 7 days</p>
                <div class="chart-wrap"><canvas id="trendChart" height="110"></canvas></div>
            </div>

            <div class="today-card">
                <p class="chart-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#c2410c" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Last 7 Days
                </p>
                <p class="chart-sub">{{ now()->subDays(6)->format('M j') }} – {{ now()->format('M j') }}</p>
                <div class="doughnut-wrap"><canvas id="weekChart"></canvas></div>
                <div class="today-meta">
                    <div class="today-chip present"><div class="val">{{ $weekPresent }}</div><div class="lbl">Present</div></div>
                    <div class="today-chip absent"><div class="val">{{ $weekAbsent }}</div><div class="lbl">Absent</div></div>
                    <div class="today-chip pending"><div class="val">{{ $weekUnmarked }}</div><div class="lbl">Unmarked</div></div>
                </div>
            </div>
        </div>

        {{-- ── Recent homework + results ── --}}
        <div class="content-grid">
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Recent Homework
                    </div>
                    <a href="{{ route('parent.homework.index') }}" class="section-link">View all &rarr;</a>
                </div>

                @forelse($recentSubmissions as $submission)
                    @php
                        $homework = $submission->homework;
                        $status = $submission->status();
                        $statusLabel = ['pending' => 'Not submitted', 'submitted' => 'Submitted', 'graded' => 'Graded', 'expired' => 'Expired'][$status];
                    @endphp
                    <a href="{{ route('parent.homework.show', $homework) }}" class="hw-row">
                        <span class="ui-tag {{ $homework->type === 'quiz' ? 'ui-tag-rose' : 'ui-tag-orange' }}" style="margin-top:1px">{{ $homework->type === 'quiz' ? 'Quiz' : 'File' }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="hw-title block truncate">{{ $homework->title }}</span>
                            <span class="hw-meta block">{{ $homework->due_at ? 'Due '.$homework->due_at->format('d M, h:i A') : 'No due date' }}</span>
                        </span>
                        <span class="flex shrink-0 flex-col items-end gap-1">
                            <span class="ui-status-pill ui-status-{{ $status }}">{{ $statusLabel }}</span>
                            @if($submission->isGraded())
                                <span class="ui-tag ui-tag-orange">{{ $submission->marks }}/{{ $homework->max_marks }}</span>
                            @endif
                        </span>
                    </a>
                @empty
                    <div class="ui-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <p>No homework assigned yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                        Exam Results
                    </div>
                    <a href="{{ route('parent.results.index') }}" class="section-link">View all &rarr;</a>
                </div>

                @forelse($resultHistory as $entry)
                    @php
                        $exam = $entry['exam'];
                        $rank = $entry['class_rank'];
                        $rankClass = match(true) {
                            $rank === null => 'ui-rank-none',
                            $rank === 1 => 'ui-rank-1',
                            $rank === 2 => 'ui-rank-2',
                            $rank === 3 => 'ui-rank-3',
                            default => 'ui-rank-other',
                        };
                    @endphp
                    <a href="{{ route('parent.results.show', $exam) }}" class="result-row">
                        <span class="ui-rank-badge {{ $rankClass }}" style="width:30px;height:30px;font-size:12px">{{ $rank ?? '—' }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="hw-title block truncate">{{ $exam->name }}</span>
                            <span class="hw-meta block">Term {{ $exam->term }} · {{ $exam->academic_year }}</span>
                        </span>
                        @if($entry['average'] !== null)
                            <span class="flex shrink-0 flex-col items-end gap-1">
                                <span class="ui-tag ui-tag-orange">{{ $entry['average'] }} avg</span>
                                @if($entry['delta'] !== null)
                                    <span class="ui-trend-pill {{ $entry['delta'] > 0 ? 'ui-trend-up' : ($entry['delta'] < 0 ? 'ui-trend-down' : 'ui-trend-flat') }}">
                                        {{ $entry['delta'] > 0 ? '+' : '' }}{{ $entry['delta'] }}
                                    </span>
                                @endif
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="ui-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                        <p>No exam results yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>

@if($student)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    Chart.defaults.font.family = "'Instrument Sans', 'system-ui', sans-serif";
    Chart.defaults.color = '#6b7280';

    const trendLabels  = @json($trendLabels);
    const trendPresent = @json($trendPresent);
    const trendAbsent  = @json($trendAbsent);
    const weekPresent  = {{ $weekPresent }};
    const weekAbsent   = {{ $weekAbsent }};
    const weekUnmarked = {{ $weekUnmarked }};

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                { label: 'Present', data: trendPresent, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)', borderWidth: 2.5, pointBackgroundColor: '#10b981', pointRadius: 4, tension: 0.4, fill: true, stepped: false },
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
                y: { beginAtZero: true, max: 1, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } },
            },
        },
    });

    const hasWeek = weekPresent + weekAbsent + weekUnmarked > 0;
    new Chart(document.getElementById('weekChart'), {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Unmarked'],
            datasets: [{
                data: hasWeek ? [weekPresent, weekAbsent, weekUnmarked] : [1],
                backgroundColor: hasWeek ? ['#10b981', '#ef4444', '#e5e7eb'] : ['#e5e7eb'],
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
