@extends('layouts.parent')
@section('title', 'My Profile')
@section('content')

<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">My Profile</h1>

    {{-- Profile info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Account Info</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Name</span>
                <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Email</span>
                <span class="font-medium text-gray-800">{{ auth()->user()->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Role</span>
                <span class="inline-block bg-orange-100 text-orange-700 text-xs px-2.5 py-0.5 rounded-full font-medium">
                    Parent
                </span>
            </div>
        </div>
    </div>

    {{-- Change password --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Change Password</h2>

        <form method="POST" action="{{ route('parent.profile.password') }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current password</label>
                <input type="password" name="current_password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-orange-400
                           @error('current_password') border-red-400 @enderror">
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-orange-400
                           @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm new password</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <button type="submit"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2.5 rounded-lg font-medium text-sm transition">
                Update Password
            </button>
        </form>
    </div>
</div>

@endsection