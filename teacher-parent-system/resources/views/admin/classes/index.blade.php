@extends('layouts.admin')
@section('title', 'Classes')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Classes</h1>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
    <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-col md:flex-row gap-3 md:items-center">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search by class name (e.g. 5-B, 12-E)"
               class="w-full md:max-w-md border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        <div class="flex gap-2">
            <button type="submit"
                class="bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Search
            </button>
            <a href="{{ route('admin.classes.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="space-y-4">
    @forelse($classes as $class)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Class {{ $class->name }}</h2>
                    <p class="text-sm text-gray-500">
                        Teacher:
                        <span class="font-medium text-gray-700">{{ $class->teacher?->name ?? 'Not assigned' }}</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Students:
                        <span class="font-medium text-gray-700">{{ $class->students_count }}</span>
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.classes.show', $class) }}"
                       class="bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-3 py-2 rounded-lg transition">
                        Open Class
                    </a>
                    <form method="POST" action="{{ route('admin.classes.remove-teacher', $class) }}"
                          onsubmit="return confirm('Remove teacher from this class?')">
                        @csrf
                        @method('PATCH')
                        <button class="bg-amber-100 text-amber-800 hover:bg-amber-200 text-sm font-medium px-3 py-2 rounded-lg transition">
                            Remove Teacher
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.classes.reset', $class) }}"
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
    @empty
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
            No classes found.
        </div>
    @endforelse
</div>

@endsection
