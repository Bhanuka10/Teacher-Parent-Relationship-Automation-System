@extends('layouts.admin')
@section('title', 'Create Account')
@section('content')

@php
    $selectedRole = old('role', $role ?? request('role'));
    $activeTab = $selectedRole === 'parent' ? 'parent' : 'teacher';
@endphp

<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index', ['role' => $activeTab]) }}" class="text-sm text-purple-600 hover:underline">
            &larr; Back to users
        </a>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Account</h1>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="mb-6">
            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1">
                <button type="button" data-tab-target="teacher"
                    class="tab-btn px-4 py-2 text-sm font-medium rounded-md transition"
                    aria-pressed="{{ $activeTab === 'teacher' ? 'true' : 'false' }}">
                    Teacher
                </button>
                <button type="button" data-tab-target="parent"
                    class="tab-btn px-4 py-2 text-sm font-medium rounded-md transition"
                    aria-pressed="{{ $activeTab === 'parent' ? 'true' : 'false' }}">
                    Parent
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" data-tab-panel="teacher" class="tab-panel">
            @csrf
            <input type="hidden" name="role" value="teacher">

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
                Create Teacher Account
            </button>
        </form>

        <form method="POST" action="{{ route('admin.users.store') }}" data-tab-panel="parent" class="tab-panel">
            @csrf
            <input type="hidden" name="role" value="parent">

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

            @error('role')
                <p class="text-red-500 text-xs mt-1 mb-3">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg font-medium text-sm transition">
                Create Parent Account
            </button>
        </form>
    </div>
</div>

<script>
    (function () {
        const initialTab = @json($activeTab);
        const buttons = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.tab-panel');

        function setActiveTab(tab) {
            buttons.forEach((button) => {
                const isActive = button.dataset.tabTarget === tab;
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                button.classList.toggle('bg-white', isActive);
                button.classList.toggle('text-purple-700', isActive);
                button.classList.toggle('shadow-sm', isActive);
                button.classList.toggle('text-gray-600', !isActive);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.tabPanel !== tab);
            });
        }

        buttons.forEach((button) => {
            button.addEventListener('click', () => setActiveTab(button.dataset.tabTarget));
        });

        setActiveTab(initialTab);
    })();
</script>

@endsection