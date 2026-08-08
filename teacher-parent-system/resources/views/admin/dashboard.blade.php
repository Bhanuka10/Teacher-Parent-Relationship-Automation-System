@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
<style>
    :root {
        --accent:       #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid:   #4f46e5;
    }

    /* page header */
    .db-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
    .db-sub   { font-size: 13px; color: #6b7280; margin: 0 0 28px; }
    .db-sub span { color: var(--accent); font-weight: 600; }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 16px;
        transition: transform .15s, box-shadow .15s;
        text-decoration: none;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.09);
    }
    .kpi-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon.purple { background: var(--accent-light); color: var(--accent); }
    .kpi-icon.teal   { background: #ccfbf1; color: #0f766e; }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-val   { font-size: 26px; font-weight: 800; color: #111827; line-height: 1.1; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── chart grid ── */
    .chart-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .chart-grid-bottom {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 28px;
    }
    @media(max-width: 900px) {
        .chart-grid, .chart-grid-bottom { grid-template-columns: 1fr; }
    }

    .chart-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .chart-title {
        font-size: 14px; font-weight: 800; color: #111827;
        margin: 0 0 4px;
        display: flex; align-items: center; gap: 8px;
    }
    .chart-sub {
        font-size: 12px; color: #9ca3af; margin: 0 0 18px;
    }
    .chart-wrap { position: relative; }

    /* today doughnut card */
    .today-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        display: flex; flex-direction: column;
    }
    .today-meta {
        display: flex; gap: 12px;
        margin-top: 16px; flex-wrap: wrap;
    }
    .today-chip {
        flex: 1; min-width: 80px;
        border-radius: 10px; padding: 10px 14px;
        text-align: center;
    }
    .today-chip.present { background: #d1fae5; }
    .today-chip.absent  { background: #fee2e2; }
    .today-chip .val    { font-size: 20px; font-weight: 800; }
    .today-chip.present .val { color: #065f46; }
    .today-chip.absent  .val { color: #991b1b; }
    .today-chip .lbl    { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .doughnut-wrap { max-width: 180px; margin: 0 auto; }

    /* ── recent activity table ── */
    .recent-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .recent-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .recent-title { font-size: 14px; font-weight: 800; color: #111827; display: flex; align-items: center; gap: 8px; }
    .recent-link  { font-size: 12px; font-weight: 600; color: var(--accent); text-decoration: none; }
    .recent-link:hover { text-decoration: underline; }
    .recent-card table { width: 100%; border-collapse: collapse; }
    .recent-card thead th {
        padding: 10px 18px;
        font-size: 10px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #9ca3af; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
        text-align: left;
    }
    .recent-card tbody tr { border-bottom: 1px solid #f3f4f6; transition: background .12s; }
    .recent-card tbody tr:last-child { border-bottom: none; }
    .recent-card tbody tr:hover { background: #fafafa; }
    .recent-card tbody td {
        padding: 12px 18px; font-size: 12px; color: #374151; vertical-align: middle;
    }
    .student-cell { display: flex; align-items: center; gap: 8px; }
    .student-av {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .student-nm { font-weight: 600; color: #111827; }
    .class-chip {
        background: #f3f4f6; color: #374151;
        border-radius: 5px; padding: 2px 8px;
        font-size: 11px; font-weight: 600;
    }
    .status-dot {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
    }
    .status-dot.present { background: #d1fae5; color: #065f46; }
    .status-dot.absent  { background: #fee2e2; color: #991b1b; }
    .dot { width: 5px; height: 5px; border-radius: 50%; }
    .status-dot.present .dot { background: #10b981; }
    .status-dot.absent  .dot { background: #ef4444; }

    /* quick action btn */
    .btn-action {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 700;
        background: var(--accent-mid); color: #fff;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(79,70,229,.25);
        transition: background .15s;
    }
    .btn-action:hover { background: #4338ca; }
</style>
@endpush

@section('content')
@php
    $avatarColours = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
    $todayRate = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100) : 0;
@endphp

<div class="max-w-7xl">

    {{-- heading --}}
    <h1 class="db-title">Admin Dashboard</h1>
    <p class="db-sub">Welcome back! Here's an <span>overview</span> of the school system today.</p>

    {{-- ── KPI cards ── --}}
    <div class="kpi-grid">
        <a href="{{ route('admin.users.index', ['role'=>'teacher']) }}" class="kpi-card" id="kpi-teachers">
            <div class="kpi-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['teachers'] }}</div>
                <div class="kpi-label">Teachers</div>
            </div>
        </a>
        <a href="{{ route('admin.users.index', ['role'=>'parent']) }}" class="kpi-card" id="kpi-students">
            <div class="kpi-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['students'] }}</div>
                <div class="kpi-label">Students</div>
            </div>
        </a>
        <a href="{{ route('admin.classes.index') }}" class="kpi-card" id="kpi-classes">
            <div class="kpi-icon indigo">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['classes'] }}</div>
                <div class="kpi-label">Classes</div>
            </div>
        </a>
        <div class="kpi-card" id="kpi-today-rate" style="cursor:default">
            <div class="kpi-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="kpi-val">{{ $todayRate }}%</div>
                <div class="kpi-label">Today's Attendance</div>
            </div>
        </div>
    </div>

    {{-- ── Row 1: Line chart + Today doughnut ── --}}
    <div class="chart-grid">
        {{-- 7-day trend line chart --}}
        <div class="chart-card">
            <p class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                7-Day Attendance Trend
            </p>
            <p class="chart-sub">Present vs Absent — last 7 days</p>
            <div class="chart-wrap">
                <canvas id="trendChart" height="110"></canvas>
            </div>
        </div>

        {{-- Today doughnut --}}
        <div class="today-card">
            <p class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Today's Status
            </p>
            <p class="chart-sub">{{ now()->format('l, M j Y') }}</p>
            <div class="doughnut-wrap">
                <canvas id="todayChart"></canvas>
            </div>
            <div class="today-meta">
                <div class="today-chip present">
                    <div class="val" id="today-present-val">{{ $todayPresent }}</div>
                    <div class="lbl">Present</div>
                </div>
                <div class="today-chip absent">
                    <div class="val" id="today-absent-val">{{ $todayAbsent }}</div>
                    <div class="lbl">Absent</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Bar charts ── --}}
    <div class="chart-grid-bottom">
        {{-- Students per class bar --}}
        <div class="chart-card">
            <p class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Students per Class
            </p>
            <p class="chart-sub">Enrolment by class (top 10)</p>
            <div class="chart-wrap">
                <canvas id="classChart" height="170"></canvas>
            </div>
        </div>

        {{-- Attendance rate per class horizontal bar --}}
        <div class="chart-card">
            <p class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2">
                    <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="10" x2="14" y2="10"/>
                    <line x1="4" y1="14" x2="17" y2="14"/><line x1="4" y1="18" x2="11" y2="18"/>
                </svg>
                Attendance Rate by Class
            </p>
            <p class="chart-sub">All-time present % (top 8 classes)</p>
            <div class="chart-wrap">
                <canvas id="rateChart" height="170"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Recent activity + quick actions ── --}}
    <div class="recent-card" id="recent-activity">
        <div class="recent-header">
            <div class="recent-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Recent Attendance Activity
            </div>
            <a href="{{ route('admin.attendance.history') }}" class="recent-link">View all &rarr;</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Marked By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentRecords as $record)
                    @php
                        $student = $record->student;
                        $aColor  = $avatarColours[ord(strtolower($student?->name[0] ?? 'a')) % count($avatarColours)];
                    @endphp
                    <tr id="recent-row-{{ $record->id }}">
                        <td>
                            <div class="student-cell">
                                <div class="student-av" style="background:{{ $aColor }}">
                                    {{ strtoupper(substr($student?->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="student-nm">{{ $student?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td><span class="class-chip">{{ $student?->schoolClass?->name ?? '—' }}</span></td>
                        <td>{{ $record->date->format('M j, Y') }}</td>
                        <td>
                            <span class="status-dot {{ $record->status }}">
                                <span class="dot"></span>
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td>{{ $record->markedBy?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:#9ca3af;font-size:13px">
                            No attendance records yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ── Chart.js ── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    Chart.defaults.font.family = "'Inter', 'system-ui', sans-serif";
    Chart.defaults.color = '#6b7280';

    const trendLabels  = @json($trendLabels);
    const trendPresent = @json($trendPresent);
    const trendAbsent  = @json($trendAbsent);
    const classLabels  = @json($classLabels);
    const classStudents = @json($classStudents);
    const rateLabels   = @json($rateLabels);
    const rateValues   = @json($rateValues);
    const todayPresent = {{ $todayPresent }};
    const todayAbsent  = {{ $todayAbsent }};

    /* ── 1. 7-Day trend line chart ── */
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'Present',
                    data: trendPresent,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,.1)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Absent',
                    data: trendAbsent,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                },
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

    /* ── 2. Today doughnut ── */
    const hasToday = todayPresent + todayAbsent > 0;
    new Chart(document.getElementById('todayChart'), {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                data: hasToday ? [todayPresent, todayAbsent] : [1],
                backgroundColor: hasToday ? ['#10b981', '#ef4444'] : ['#e5e7eb'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: hasToday, cornerRadius: 8 },
            },
        },
    });

    /* ── 3. Students per class bar ── */
    const barColors = [
        '#6366f1','#8b5cf6','#0ea5e9','#10b981',
        '#f59e0b','#ec4899','#14b8a6','#ef4444','#a855f7','#f97316'
    ];
    new Chart(document.getElementById('classChart'), {
        type: 'bar',
        data: {
            labels: classLabels,
            datasets: [{
                label: 'Students',
                data: classStudents,
                backgroundColor: classLabels.map((_, i) => barColors[i % barColors.length] + 'cc'),
                borderColor:     classLabels.map((_, i) => barColors[i % barColors.length]),
                borderWidth: 1.5,
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { cornerRadius: 8 },
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } },
            },
        },
    });

    /* ── 4. Attendance rate horizontal bar ── */
    new Chart(document.getElementById('rateChart'), {
        type: 'bar',
        data: {
            labels: rateLabels,
            datasets: [{
                label: 'Attendance %',
                data: rateValues,
                backgroundColor: rateValues.map(v =>
                    v >= 90 ? '#10b981cc' : v >= 75 ? '#f59e0bcc' : '#ef4444cc'
                ),
                borderColor: rateValues.map(v =>
                    v >= 90 ? '#10b981' : v >= 75 ? '#f59e0b' : '#ef4444'
                ),
                borderWidth: 1.5,
                borderRadius: 6,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    cornerRadius: 8,
                    callbacks: { label: ctx => ' ' + ctx.parsed.x + '%' },
                },
            },
            scales: {
                x: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' },
                     ticks: { callback: v => v + '%' } },
                y: { grid: { display: false } },
            },
        },
    });
})();
</script>
@endsection
