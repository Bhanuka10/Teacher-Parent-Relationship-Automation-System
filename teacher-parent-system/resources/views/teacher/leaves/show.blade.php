@extends('layouts.teacher')
@section('title', 'Leave Request')

@section('content')
@php
    $statusClass = match($leaveRequest->status) {
        'approved' => 'ui-status-graded',
        'rejected' => 'ui-status-expired',
        default => 'ui-status-pending',
    };
@endphp
<div class="mx-auto max-w-2xl">
    <a href="{{ route('teacher.leaves.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to leave requests
    </a>

    <section class="ui-card mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <span class="ui-status-pill {{ $statusClass }}">{{ ucfirst($leaveRequest->status) }}</span>
                <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $leaveRequest->student->name }}</h1>
                <p class="mt-1.5 text-sm text-gray-500">
                    {{ $leaveRequest->start_date->format('d M Y') }}
                    @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
                    · {{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}
                </p>
            </div>
        </div>

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

    @if($leaveRequest->isPending())
        <section class="ui-card mt-6 p-6">
            <h2 class="font-bold text-gray-800">Review this request</h2>
            <form method="POST" action="{{ route('teacher.leaves.review', $leaveRequest) }}" class="mt-4">
                @csrf
                @method('PUT')

                <label for="lr-note" class="ui-field-label">Note <span class="font-normal text-gray-400">(optional, shared with the parent)</span></label>
                <textarea id="lr-note" name="review_note" rows="3" class="ui-input resize-none">{{ old('review_note') }}</textarea>
                @error('review_note')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                @error('decision')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" name="decision" value="approved" class="ui-submit-btn" style="background:linear-gradient(135deg,#15803d,#22c55e)">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Approve
                    </button>
                    <button type="submit" name="decision" value="rejected" class="ui-submit-btn" style="background:linear-gradient(135deg,#b91c1c,#ef4444)">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        Reject
                    </button>
                </div>
            </form>
        </section>
    @endif
</div>
@endsection
