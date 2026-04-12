@extends('layouts.teacher')
@section('title', 'Mark Attendance')
@section('content')

<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Daily Attendance</h1>
        <a href="{{ route('teacher.attendance.history') }}" class="text-sm text-teal-700 hover:underline">View history</a>
    </div>

    @if($errors->has('attendance'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first('attendance') }}
        </div>
    @endif

    @if(!$schoolClass)
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-gray-500">
            No class is assigned to your account yet.
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
            <p class="text-sm text-gray-600">Class: <span class="font-semibold text-gray-800">{{ $schoolClass->name }}</span></p>
            <p class="text-sm text-gray-600">Date: <span class="font-semibold text-gray-800">{{ \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') }}</span></p>
        </div>

        <form method="POST" action="{{ route('teacher.attendance.store') }}" id="attendance-form">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-col md:flex-row md:items-end gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Date</label>
                    <input type="date" name="date" value="{{ $date }}"
                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('date') border-red-400 @enderror">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="button" id="mark-all-present"
                    class="bg-teal-100 text-teal-800 hover:bg-teal-200 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    Mark All Present
                </button>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-5 py-3">Student Name</th>
                            <th class="text-left px-5 py-3">Index Number</th>
                            <th class="text-left px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $student)
                            @php
                                $record = $attendanceByStudent->get($student->id);
                                $status = old('status.'.$student->id, $record?->status ?? 'present');
                                $indexNumber = $student->profile?->index_number ?? $student->admission_number;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $student->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $indexNumber }}</td>
                                <td class="px-5 py-3">
                                    <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden">
                                        <label class="cursor-pointer">
                                            <input type="radio" class="sr-only attendance-radio" name="status[{{ $student->id }}]" value="present" {{ $status === 'present' ? 'checked' : '' }}>
                                            <span class="attendance-pill present-pill px-3 py-1.5 text-xs font-semibold {{ $status === 'present' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">Present</span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" class="sr-only attendance-radio" name="status[{{ $student->id }}]" value="absent" {{ $status === 'absent' ? 'checked' : '' }}>
                                            <span class="attendance-pill absent-pill px-3 py-1.5 text-xs font-semibold {{ $status === 'absent' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' }}">Absent</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-gray-400">No students in your class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->count())
                <div class="mt-4">
                    <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Save Attendance
                    </button>
                </div>
            @endif
        </form>
    @endif
</div>

<script>
    (function () {
        const markAllBtn = document.getElementById('mark-all-present');
        const radios = document.querySelectorAll('.attendance-radio');

        function refreshPills() {
            const rows = document.querySelectorAll('tr');
            rows.forEach((row) => {
                const presentInput = row.querySelector('input[value="present"]');
                const absentInput = row.querySelector('input[value="absent"]');
                const presentPill = row.querySelector('.present-pill');
                const absentPill = row.querySelector('.absent-pill');

                if (!presentInput || !absentInput || !presentPill || !absentPill) return;

                if (presentInput.checked) {
                    presentPill.className = 'attendance-pill present-pill px-3 py-1.5 text-xs font-semibold bg-green-100 text-green-700';
                    absentPill.className = 'attendance-pill absent-pill px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-500';
                } else {
                    presentPill.className = 'attendance-pill present-pill px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-500';
                    absentPill.className = 'attendance-pill absent-pill px-3 py-1.5 text-xs font-semibold bg-red-100 text-red-700';
                }
            });
        }

        radios.forEach((radio) => {
            radio.addEventListener('change', refreshPills);
        });

        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => {
                document.querySelectorAll('input.attendance-radio[value="present"]').forEach((input) => {
                    input.checked = true;
                });
                refreshPills();
            });
        }

        refreshPills();
    })();
</script>

@endsection
