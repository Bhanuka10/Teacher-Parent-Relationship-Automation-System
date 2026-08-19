@extends('layouts.teacher')
@section('title', 'My Leave')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-7 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="ui-hero-icon ui-hero-teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">My Leave</h1>
                <p class="mt-0.5 text-sm text-gray-500">Request leave and track admin approval.</p>
            </div>
        </div>
        <a href="{{ route('teacher.my-leave.create') }}" class="ui-submit-btn">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New request
        </a>
    </div>

    <section class="ui-card overflow-hidden">
        @forelse($leaveRequests as $leaveRequest)
            @php
                $statusClass = match($leaveRequest->status) {
                    'approved' => 'ui-status-graded',
                    'rejected' => 'ui-status-expired',
                    default => 'ui-status-pending',
                };
            @endphp
            <div class="flex flex-col gap-3 border-b border-gray-100 px-6 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="ui-status-pill {{ $statusClass }}">{{ ucfirst($leaveRequest->status) }}</span>
                        <span class="text-sm font-semibold text-gray-800">
                            {{ $leaveRequest->start_date->format('d M Y') }}
                            @if(!$leaveRequest->start_date->eq($leaveRequest->end_date))
                                – {{ $leaveRequest->end_date->format('d M Y') }}
                            @endif
                        </span>
                        <span class="ui-tag ui-tag-gray">{{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}</span>
                    </div>
                    <p class="mt-1.5 truncate text-xs text-gray-500">{{ $leaveRequest->reason }}</p>
                    @if($leaveRequest->status !== 'pending' && $leaveRequest->review_note)
                        <p class="mt-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs text-gray-600">
                            <span class="font-semibold">Admin's note:</span> {{ $leaveRequest->review_note }}
                        </p>
                    @endif
                </div>

                @if($leaveRequest->isPending())
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('teacher.my-leave.edit', $leaveRequest) }}" class="ui-tab-btn" style="background:#fff;border:1px solid #e5e7eb;color:#374151">Edit</a>
                        <form method="POST" action="{{ route('teacher.my-leave.destroy', $leaveRequest) }}" onsubmit="return confirm('Cancel this leave request?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="ui-tab-btn" style="background:#fef2f2;color:#b91c1c">Cancel</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="ui-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                <p>No leave requests yet.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
