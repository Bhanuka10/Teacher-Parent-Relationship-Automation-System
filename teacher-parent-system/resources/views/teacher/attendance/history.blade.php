@extends('layouts.teacher')
@section('title', 'Attendance History')
@section('content')

<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Attendance History</h1>
        <a href="{{ route('teacher.attendance.index') }}" class="text-sm text-teal-700 hover:underline">Back to marking</a>
    </div>

    @if(!$schoolClass)
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-gray-500">
            No class is assigned to your account yet.
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
            <p class="text-sm text-gray-600 mb-3">Class: <span class="font-semibold text-gray-800">{{ $schoolClass->name }}</span></p>
            <form method="GET" action="{{ route('teacher.attendance.history') }}" class="flex flex-col md:flex-row gap-3 md:items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Date</label>
                    <input type="date" name="date" value="{{ $filterDate }}"
                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                        Filter
                    </button>
                    <a href="{{ route('teacher.attendance.history') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @if($filterDate)
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
                <p class="text-sm text-gray-600">Need to edit this date? Open marking page with this date:</p>
                <a href="{{ route('teacher.attendance.index', ['date' => $filterDate]) }}" class="inline-block mt-2 text-sm text-teal-700 hover:underline">
                    Edit {{ $filterDate }} attendance
                </a>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-5 py-3">Student Name</th>
                        <th class="text-left px-5 py-3">Index Number</th>
                        <th class="text-left px-5 py-3">Date</th>
                        <th class="text-left px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                        @php
                            $indexNumber = $record->student->profile?->index_number ?? $record->student->admission_number;
                        @endphp
                        <tr class="{{ $record->status === 'absent' ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-5 py-3 text-gray-700">{{ $record->student->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $indexNumber }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $record->date->format('Y-m-d') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-400">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
