@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

<div class="grid grid-cols-2 gap-4 max-w-sm mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500 mb-1">Teachers</p>
        <p class="text-3xl font-bold text-purple-700">{{ $stats['teachers'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500 mb-1">Parents</p>
        <p class="text-3xl font-bold text-purple-700">{{ $stats['parents'] }}</p>
    </div>
</div>

<a href="{{ route('admin.users.create') }}"
   class="inline-block bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
    + Create New Account
</a>

@endsection