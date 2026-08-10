@extends('layouts.parent')
@section('title', 'Results')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-orange">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Results</h1>
            <p class="mt-0.5 text-sm text-gray-500">Term exam marks, grades, and rankings.</p>
        </div>
    </div>

    @if(!$student)
        <div class="ui-card">
            <div class="ui-empty"><p>No student record is linked to this account yet.</p></div>
        </div>
    @else
        <section class="ui-card overflow-hidden">
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
                   class="ui-animate-in flex items-center gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-orange-50/40 last:border-0"
                   style="text-decoration:none;animation-delay:{{ min($index, 4) * .05 }}s">
                    <span class="ui-tag ui-tag-orange">Term {{ $exam->term }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-800">{{ $exam->name }}</span>
                        <span class="mt-1 block text-xs text-gray-400">{{ $exam->academic_year }} · {{ $exam->exam_date->format('d M Y') }}</span>
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
