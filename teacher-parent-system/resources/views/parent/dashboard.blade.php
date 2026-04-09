@extends('layouts.parent')
@section('title', 'Dashboard')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-2">Hello, {{ $parent->name }}</h1>
<p class="text-gray-500 mb-8">Track your child's attendance and academic progress.</p>

<div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Today's attendance</p>
        <p class="text-lg font-semibold text-orange-600">No data yet</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Latest marks</p>
        <p class="text-lg font-semibold text-orange-600">No data yet</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Notifications</p>
        <p class="text-lg font-semibold text-orange-600">0 unread</p>
    </div>
</div>

@endsection