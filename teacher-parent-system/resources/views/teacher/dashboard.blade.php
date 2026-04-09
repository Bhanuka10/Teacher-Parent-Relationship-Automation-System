@extends('layouts.teacher')
@section('title', 'Dashboard')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-2">Good morning, {{ $teacher->name }}</h1>
<p class="text-gray-500 mb-8">Here's what needs your attention today.</p>

<div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Attendance</p>
        <p class="text-lg font-semibold text-teal-700">Not marked yet</p>
        <p class="text-xs text-gray-400 mt-1">Today — {{ now()->format('d M Y') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Messages</p>
        <p class="text-lg font-semibold text-teal-700">0 unread</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Notices</p>
        <p class="text-lg font-semibold text-teal-700">0 active</p>
    </div>
</div>

@endsection