@extends('layouts.admin')
@section('title', 'Attendance History')
@section('content')

<div class="max-w-7xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Attendance History</h1>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <form method="GET" action="{{ route('admin.attendance.history') }}" class="flex flex-col lg:flex-row gap-3 lg:items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                <select name="grade"
                    class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All grades</option>
                    @for($g = 1; $g <= 12; $g++)
                        <option value="{{ $g }}" {{ (string) $grade === (string) $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class (Section)</label>
                <select name="section"
                    class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All sections</option>
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $sec)
                        <option value="{{ $sec }}" {{ $section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}"
                    class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    Search
                </button>
                <a href="{{ route('admin.attendance.history') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-[1100px] w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="text-left px-5 py-3">Student Name</th>
                    <th class="text-left px-5 py-3">Index Number</th>
                    <th class="text-left px-5 py-3">Class</th>
                    <th class="text-left px-5 py-3">Date</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Marked By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $record)
                    @php
                        $indexNumber = $record->student?->profile?->index_number ?? $record->student?->admission_number;
                    @endphp
                    <tr class="{{ $record->status === 'absent' ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                        <td class="px-5 py-3 text-gray-700">{{ $record->student?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $indexNumber ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $record->student?->schoolClass?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $record->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $record->markedBy?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-400 text-sm">
                            No attendance records found for selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
