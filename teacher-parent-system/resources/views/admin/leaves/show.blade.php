@extends('layouts.admin')
@section('title', 'Leave Request')

@section('content')
@php
    $statusClass = match($leaveRequest->status) {
        'approved' => 'ui-status-graded',
        'rejected' => 'ui-status-expired',
        default => 'ui-status-pending',
    };
@endphp
<div class="max-w-2xl">
    <a href="{{ route('admin.leaves.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to leave requests
    </a>

    <section class="ui-card mt-4 p-6">
        <span class="ui-status-pill {{ $statusClass }}">{{ ucfirst($leaveRequest->status) }}</span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $leaveRequest->student->name }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">
            {{ $leaveRequest->schoolClass?->name ?? '—' }}
            · {{ $leaveRequest->start_date->format('d M Y') }}
            @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
            · {{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Requested by {{ $leaveRequest->requestedBy?->name ?? '—' }} on {{ $leaveRequest->created_at->format('d M Y, h:i A') }}</p>

        <p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm leading-6 text-gray-700">{{ $leaveRequest->reason }}</p>

        @if(!$leaveRequest->isPending())
            <div class="mt-4 border-t border-gray-100 pt-4 text-xs text-gray-500">
                {{ ucfirst($leaveRequest->status) }} by {{ $leaveRequest->reviewedBy?->name ?? '—' }} on {{ $leaveRequest->reviewed_at?->format('d M Y, h:i A') }}
                @if($leaveRequest->review_note)
                    <p class="mt-2 rounded-lg bg-gray-50 p-3 text-sm text-gray-700"><span class="font-semibold">Note:</span> {{ $leaveRequest->review_note }}</p>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
