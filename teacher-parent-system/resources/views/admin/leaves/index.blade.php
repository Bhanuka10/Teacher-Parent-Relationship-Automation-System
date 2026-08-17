@extends('layouts.admin')
@section('title', 'Leave Requests')

@section('content')
<div class="max-w-6xl">
    <div class="mb-7 flex items-center gap-4">
        <div class="ui-hero-icon ui-hero-indigo">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Leave Requests</h1>
            <p class="mt-0.5 text-sm text-gray-500">Read-only view of leave requests and their status across all classes.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.leaves.index') }}" class="ui-card mb-6 flex flex-wrap items-end gap-3 p-5">
        <div style="min-width:180px">
            <label class="ui-field-label">Class</label>
            <select name="class_id" class="ui-input">
                <option value="">All classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px">
            <label class="ui-field-label">Status</label>
            <select name="status" class="ui-input">
                <option value="">All statuses</option>
                <option value="pending" @selected($status === 'pending')>Pending</option>
                <option value="approved" @selected($status === 'approved')>Approved</option>
                <option value="rejected" @selected($status === 'rejected')>Rejected</option>
            </select>
        </div>
        <button type="submit" class="ui-submit-btn" style="padding:10px 20px">Filter</button>
        <a href="{{ route('admin.leaves.index') }}" class="ui-tab-btn" style="background:#fff;border:1px solid #e5e7eb;color:#374151;padding:10px 16px">Reset</a>
    </form>

    <section class="ui-card overflow-hidden">
        @forelse($leaveRequests as $leaveRequest)
            @php
                $statusClass = match($leaveRequest->status) {
                    'approved' => 'ui-status-graded',
                    'rejected' => 'ui-status-expired',
                    default => 'ui-status-pending',
                };
            @endphp
            <a href="{{ route('admin.leaves.show', $leaveRequest) }}" class="flex items-start gap-3 border-b border-gray-100 px-6 py-4 transition hover:bg-gray-50 last:border-0" style="text-decoration:none">
                <span class="ui-status-pill {{ $statusClass }}" style="margin-top:2px">{{ ucfirst($leaveRequest->status) }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-gray-800">{{ $leaveRequest->student->name }}</span>
                    <span class="mt-1 block text-xs text-gray-400">
                        {{ $leaveRequest->schoolClass?->name ?? '—' }}
                        · {{ $leaveRequest->start_date->format('d M Y') }}
                        @if(!$leaveRequest->start_date->eq($leaveRequest->end_date)) – {{ $leaveRequest->end_date->format('d M Y') }} @endif
                        · {{ $leaveRequest->dayCount() }} day{{ $leaveRequest->dayCount() === 1 ? '' : 's' }}
                    </span>
                </span>
            </a>
        @empty
            <div class="ui-empty"><p>No leave requests match these filters.</p></div>
        @endforelse

        @if($leaveRequests->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $leaveRequests->links() }}</div>@endif
    </section>
</div>
@endsection
