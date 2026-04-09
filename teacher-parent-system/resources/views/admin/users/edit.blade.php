@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')

<div class="max-w-lg">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:underline mb-6 inline-block">
        &larr; Back to users
    </a>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit — {{ $user->name }}</h1>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    New password
                    <span class="font-normal text-gray-400">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm new password</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <button type="submit"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg font-medium text-sm transition">
                Save Changes
            </button>
        </form>
    </div>
</div>

@endsection