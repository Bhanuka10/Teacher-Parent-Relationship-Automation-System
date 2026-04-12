@extends('layouts.admin')
@section('title', 'Class Details')
@section('content')

<div class="max-w-5xl">
    <a href="{{ route('admin.classes.index') }}" class="text-sm text-purple-600 hover:underline mb-6 inline-block">
        &larr; Back to classes
    </a>

    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Class {{ $school_class->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Teacher:
                    <span class="font-medium text-gray-700">{{ $school_class->teacher?->name ?? 'Not assigned' }}</span>
                </p>
                <p class="text-sm text-gray-500">
                    Total Students:
                    <span class="font-medium text-gray-700">{{ $school_class->students->count() }}</span>
                </p>
            </div>

            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.classes.remove-teacher', $school_class) }}"
                      onsubmit="return confirm('Remove teacher from this class?')">
                    @csrf
                    @method('PATCH')
                    <button class="bg-amber-100 text-amber-800 hover:bg-amber-200 text-sm font-medium px-3 py-2 rounded-lg transition">
                        Remove Teacher
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.classes.reset', $school_class) }}"
                      onsubmit="return confirm('Reset this class? This will unlink teacher and all students from the class.')">
                    @csrf
                    @method('PATCH')
                    <button class="bg-red-100 text-red-700 hover:bg-red-200 text-sm font-medium px-3 py-2 rounded-lg transition">
                        Reset
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="text-left px-5 py-3">Student Name</th>
                    <th class="text-left px-5 py-3">Index Number</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($school_class->students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-700">{{ $student->name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $student->admission_number }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-5 py-8 text-center text-gray-400 text-sm">
                            No students assigned to this class.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
