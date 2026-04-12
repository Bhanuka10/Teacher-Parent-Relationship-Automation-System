@extends('layouts.admin')
@section('title', $role === 'parent' ? 'Manage Students' : 'Manage Teachers')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        {{ $role === 'parent' ? 'Manage Students' : 'Manage Teachers' }}
    </h1>
    <a href="{{ route('admin.users.create', ['role' => $role === 'parent' ? 'parent' : 'teacher']) }}"
       class="bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        + New {{ $role === 'parent' ? 'Student' : 'Teacher' }}
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-3 md:items-center">
        <input type="hidden" name="role" value="{{ $role === 'parent' ? 'parent' : 'teacher' }}">
        <input type="text" name="search" value="{{ $search }}" list="user-search-options"
            placeholder="Search by name, class, or admission no."
            class="w-full md:max-w-md border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        <datalist id="user-search-options">
            @foreach($searchOptions as $option)
                <option value="{{ $option }}"></option>
            @endforeach
        </datalist>

        <div class="flex gap-2">
            <button type="submit"
                class="bg-purple-700 hover:bg-purple-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Search
            </button>
            <a href="{{ route('admin.users.index', ['role' => $role === 'parent' ? 'parent' : 'teacher']) }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="{{ $role === 'teacher' ? 'min-w-[1400px]' : 'min-w-[1700px]' }} w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="text-left px-5 py-3">Name</th>
                <th class="text-left px-5 py-3">Email</th>
                @if($role === 'teacher')
                    <th class="text-left px-5 py-3">Full Name</th>
                    <th class="text-left px-5 py-3">Address</th>
                    <th class="text-left px-5 py-3">Phone Number</th>
                    <th class="text-left px-5 py-3">Email Address</th>
                @else
                    <th class="text-left px-5 py-3">Admission No.</th>
                    <th class="text-left px-5 py-3">Full Name</th>
                    <th class="text-left px-5 py-3">Index No.</th>
                    <th class="text-left px-5 py-3">Date of Birth</th>
                    <th class="text-left px-5 py-3">Gender</th>
                    <th class="text-left px-5 py-3">Parent Phone</th>
                    <th class="text-left px-5 py-3">Parent Email</th>
                    <th class="text-left px-5 py-3">Address</th>
                @endif
                <th class="text-left px-5 py-3">Class</th>
                <th class="text-left px-5 py-3">Role</th>
                <th class="text-left px-5 py-3">Status</th>
                <th class="text-left px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            @php
                $teacherProfile = $user->role === 'teacher' ? $user->teacherProfile : null;
                $studentRecord = $user->role === 'parent' ? $user->students->first() : null;
                $studentProfile = $studentRecord?->profile;
                $admissionNumber = $studentRecord?->admission_number ?? '-';
                $className = $user->role === 'teacher'
                    ? ($user->schoolClass?->name ?? '-')
                    : ($studentRecord?->schoolClass?->name ?? '-');
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                @if($role === 'teacher')
                    <td class="px-5 py-3 text-gray-600">{{ $teacherProfile?->full_name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate" title="{{ $teacherProfile?->address }}">{{ $teacherProfile?->address ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $teacherProfile?->phone_number ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $teacherProfile?->email_address ?? '-' }}</td>
                @else
                    <td class="px-5 py-3 text-gray-600">{{ $admissionNumber }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $studentProfile?->full_name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $studentProfile?->index_number ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ optional($studentProfile?->date_of_birth)->format('Y-m-d') ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $studentProfile?->gender ? ucfirst($studentProfile->gender) : '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $studentProfile?->parent_phone_number ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $studentProfile?->parent_email ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate" title="{{ $studentProfile?->address }}">{{ $studentProfile?->address ?? '-' }}</td>
                @endif
                <td class="px-5 py-3 text-gray-600">{{ $className }}</td>
                <td class="px-5 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $user->role === 'teacher'
                            ? 'bg-teal-100 text-teal-700'
                            : 'bg-orange-100 text-orange-700' }}">
                        {{ $user->role === 'parent' ? 'Student' : 'Teacher' }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    @if($user->must_change_password)
                        <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                            Pending password change
                        </span>
                    @else
                        <span class="text-xs text-green-600">Active</span>
                    @endif
                </td>
                <td class="px-5 py-3 flex items-center gap-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="text-purple-600 hover:underline text-xs">Edit</a>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Delete this user?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $role === 'teacher' ? '10' : '14' }}" class="px-5 py-8 text-center text-gray-400 text-sm">
                    No users found. Create one to get started.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection