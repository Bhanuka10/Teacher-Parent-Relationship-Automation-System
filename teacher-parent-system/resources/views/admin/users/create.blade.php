@extends('layouts.admin')
@section('title', 'Create Account')
@section('content')

<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:underline">
            &larr; Back to users
        </a>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Account</h1>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500
                           @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500
                           @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500
                           @error('role') border-red-400 @enderror">
                    <option value="">Select a role...</option>
                    <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="parent"  {{ old('role') === 'parent'  ? 'selected' : '' }}>Parent</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Temporary password
                    <span class="font-normal text-gray-400">(user must change on first login)</span>
                </label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500
                           @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <button type="submit"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg font-medium text-sm transition">
                Create Account
            </button>
        </form>
    </div>
</div>

@endsection