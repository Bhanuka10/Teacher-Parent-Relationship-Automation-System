@extends('layouts.admin')
@section('title', 'Manage Student Records')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manage Student Records</h1>
    <a href="{{ route('admin.students.create') }}"
       class="bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        + Add Student
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="text-left px-5 py-3">Name</th>
                <th class="text-left px-5 py-3">Admission No.</th>
                <th class="text-left px-5 py-3">Class</th>
                <th class="text-left px-5 py-3">Account</th>
                <th class="text-left px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($students as $student)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $student->name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $student->admission_number }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $student->schoolClass?->name ?? 'Not assigned' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $student->parent?->name ?? 'None' }}</td>
                <td class="px-5 py-3 flex items-center gap-3">
                    <a href="{{ route('admin.students.edit', $student) }}" class="text-purple-600 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                          onsubmit="return confirm('Delete this student record?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">
                    No student records found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
