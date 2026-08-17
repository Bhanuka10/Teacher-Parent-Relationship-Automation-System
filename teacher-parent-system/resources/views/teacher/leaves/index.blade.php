@extends('layouts.teacher')
@section('title', 'Leave Requests')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-teal">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Leave Requests</h1>
            <p class="mt-0.5 text-sm text-gray-500">Review and decide leave requests from your class.</p>
        </div>
    </div>

    @if(!$schoolClass)
        <div class="ui-card">
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                <p>No class is assigned to your account yet.</p>
            </div>
        </div>
    @else
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach(['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                <a href="{{ route('teacher.leaves.index', $value ? ['status' => $value] : []) }}"
                   class="ui-tab-btn" style="{{ (string) $status === $value ? 'background:#fff;color:#0f766e;box-shadow:0 1px 3px rgba(0,0,0,.1)' : 'background:#f3f4f6;color:#6b7280' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <section class="ui-card overflow-hidden">
            @forelse($leaveRequests as $leaveRequest)
                @php
                    $statusClass = match($leaveRequest->status) {
                        'approved' => 'ui-status-graded',
                        'rejected' => 'ui-status-expired',
                        default => 'ui-status-pending',
                    };
                    $initials = collect(explode(' ', $leaveRequest->student->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                @endphp
                <a href="{{ route('teacher.leaves.show', $leaveRequest) }}" class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0" style="text-decoration:none">
                    <span class="ui-avatar ui-avatar-indigo">{{ strtoupper($initials) }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-800">{{ $leaveRequest->student->name }}</span>
                        <span class="mt-1 block text-xs text-gray-400">
                            {{ $leaveRequest->start_date->format('d M Y') }}
                            @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
                            · {{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}
                        </span>
                    </span>
                    <span class="ui-status-pill {{ $statusClass }}">{{ ucfirst($leaveRequest->status) }}</span>
                </a>
            @empty
                <div class="ui-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                    <p>No leave requests{{ $status ? ' with this status' : '' }}.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
