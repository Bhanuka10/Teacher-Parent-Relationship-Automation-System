@extends('layouts.parent')
@section('title', 'Dashboard')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-2">Hello, {{ $parent->name }}</h1>
<p class="text-gray-500 mb-8">Track your child's attendance and academic progress.</p>

@if($student && $todayAttendance)
    <div class="mb-6 rounded-xl border px-4 py-3 text-sm {{ $todayAttendance->status === 'present' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">
        Notification: Your attendance for today is
        <span class="font-semibold">{{ ucfirst($todayAttendance->status) }}</span>.
    </div>
@elseif($student)
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        Notification: Attendance for today has not been marked yet.
    </div>
@else
    <div class="mb-6 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
        Notification: No student record linked to this account.
    </div>
@endif

<div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Today's attendance</p>
        @if($todayAttendance)
            <p class="text-lg font-semibold {{ $todayAttendance->status === 'present' ? 'text-green-600' : 'text-red-600' }}">
                {{ ucfirst($todayAttendance->status) }}
            </p>
        @else
            <p class="text-lg font-semibold text-orange-600">Not marked</p>
        @endif
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Latest attendance</p>
        @if($latestAttendance)
            <p class="text-lg font-semibold {{ $latestAttendance->status === 'present' ? 'text-green-600' : 'text-red-600' }}">
                {{ ucfirst($latestAttendance->status) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $latestAttendance->date->format('Y-m-d') }}</p>
        @else
            <p class="text-lg font-semibold text-orange-600">No data yet</p>
        @endif
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Notifications</p>
        <a href="{{ route('parent.messages.index') }}" class="text-lg font-semibold text-orange-600 hover:underline">{{ $unreadMessages }} message{{ $unreadMessages === 1 ? '' : 's' }} unread</a>
    </div>
</div>

@endsection
