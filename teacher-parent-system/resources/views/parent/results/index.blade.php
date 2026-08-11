@extends('layouts.parent')
@section('title', 'Results')

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
    .db-head  { margin-bottom: 26px; }

    /* ── KPI stat cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
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
    .kpi-icon.orange { background: var(--p-accent-light); color: var(--p-accent); }
    .kpi-icon.green  { background: #d1fae5; color: #065f46; }
    .kpi-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .kpi-icon.amber  { background: #fef3c7; color: #b45309; }
    .kpi-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1.15; }
    .kpi-label { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .kpi-note  { font-size: 10.5px; color: #9ca3af; margin-top: 3px; }

    /* ── section card / result rows ── */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .result-row {
        display: flex; align-items: center; gap: 12px; padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background .12s;
    }
    .result-row:last-child { border-bottom: none; }
    .result-row:hover { background: #fafafa; }
    .result-title { font-size: 13px; font-weight: 700; color: #111827; }
    .result-meta  { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }
</style>
@endpush

@section('content')
<div class="max-w-7xl">
    <div class="db-head">
        <h1 class="db-title">Results</h1>
        <p class="db-sub">
            @if($student)
                Term exam marks, grades, and rankings for <span>{{ $student->name }}</span>{{ $student->schoolClass ? ' · '.$student->schoolClass->name : '' }}
            @else
                <span>No student linked yet</span>
            @endif
        </p>
    </div>

    @if(!$student)
        <div class="section-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                <p>No student record is linked to this account yet.</p>
                <p class="ui-empty-sub">Contact an admin to have your child linked — results will appear here automatically.</p>
            </div>
        </div>
    @else
        @php
            $scored = $history->filter(fn ($entry) => $entry['average'] !== null);
            $examsTaken = $history->count();
            $bestAverage = $scored->max('average');
            $latest = $history->last();
            $latestRank = $latest['class_rank'] ?? null;
            $latestAverage = $latest['average'] ?? null;
        @endphp

        {{-- ── KPI cards ── --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon orange">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $examsTaken }}</div>
                    <div class="kpi-label">Exams Taken</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $bestAverage ?? '—' }}</div>
                    <div class="kpi-label">Best Average</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-3.5 1.5L7 21l5-2 5 2-1.5-4.5"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $latestRank ?? '—' }}</div>
                    <div class="kpi-label">Latest Rank</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M8 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                </div>
                <div>
                    <div class="kpi-val">{{ $latestAverage ?? '—' }}</div>
                    <div class="kpi-label">Latest Average</div>
                    @if($latest && $latest['delta'] !== null)
                        <div class="kpi-note">{{ $latest['delta'] > 0 ? '+' : '' }}{{ $latest['delta'] }} vs previous term</div>
                    @endif
                </div>
            </div>
        </div>

        <section class="section-card">
            @forelse($history as $index => $entry)
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
                <a href="{{ route('parent.results.show', $exam) }}"
                   class="result-row ui-animate-in"
                   style="animation-delay:{{ min($index, 4) * .05 }}s">
                    <span class="ui-tag ui-tag-orange">Term {{ $exam->term }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="result-title block truncate">{{ $exam->name }}</span>
                        <span class="result-meta block">{{ $exam->academic_year }} · {{ $exam->exam_date->format('d M Y') }}</span>
                    </span>
                    @if($entry['average'] !== null)
                        <span class="text-right">
                            <span class="block text-sm font-bold text-gray-800">{{ $entry['average'] }} avg</span>
                            @if($entry['delta'] !== null)
                                <span class="ui-trend-pill {{ $entry['delta'] > 0 ? 'ui-trend-up' : ($entry['delta'] < 0 ? 'ui-trend-down' : 'ui-trend-flat') }}">
                                    {{ $entry['delta'] > 0 ? '+' : '' }}{{ $entry['delta'] }}
                                </span>
                            @endif
                        </span>
                    @endif
                    <span class="ui-rank-badge {{ $rankClass }}">{{ $rank ?? '—' }}</span>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z"/></svg>
                    <p>No exam results yet.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
