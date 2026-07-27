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
                    Student
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" data-tab-panel="teacher" class="tab-panel" data-auto-password-form>
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
                <input type="email" name="email" value="{{ old('email') }}" data-auto-password-email
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500
                           @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                    <select name="grade"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500
                               @error('grade') border-red-400 @enderror">
                        <option value="">Select grade</option>
                        @for($grade = 1; $grade <= 12; $grade++)
                            <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endfor
                    </select>
                    @error('grade')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500
                               @error('section') border-red-400 @enderror">
                        <option value="">Select section</option>
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $section)
                            <option value="{{ $section }}" {{ old('section') === $section ? 'selected' : '' }}>{{ $section }}</option>
                        @endforeach
                    </select>
                    @error('section')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Temporary password
                    <span class="font-normal text-gray-400">(user must change on first login)</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" value="{{ old('password') }}" data-auto-password
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-12 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500
                               @error('password') border-red-400 @enderror">
                    <button type="button" data-password-toggle data-password-target="password"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                        aria-label="Show password" aria-pressed="false">
                        <span data-password-icon-show>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                            </svg>
                        </span>
                        <span data-password-icon-hide class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                            </svg>
                        </span>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" data-auto-password-confirm
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-12 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <button type="button" data-password-toggle data-password-target="password_confirmation"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                        aria-label="Show confirm password" aria-pressed="false">
                        <span data-password-icon-show>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                            </svg>
                        </span>
                        <span data-password-icon-hide class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg font-medium text-sm transition">
                Create Teacher Account
            </button>
        </form>

        <form method="POST" action="{{ route('admin.users.store') }}" data-tab-panel="parent" class="tab-panel" data-auto-password-form>
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
                <input type="email" name="email" value="{{ old('email') }}" data-auto-password-email
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-500
                           @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                    <select name="grade"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500
                               @error('grade') border-red-400 @enderror">
                        <option value="">Select grade</option>
                        @for($grade = 1; $grade <= 12; $grade++)
                            <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endfor
                    </select>
                    @error('grade')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500
                               @error('section') border-red-400 @enderror">
                        <option value="">Select section</option>
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $section)
                            <option value="{{ $section }}" {{ old('section') === $section ? 'selected' : '' }}>{{ $section }}</option>
                        @endforeach
                    </select>
                    @error('section')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Temporary password
                    <span class="font-normal text-gray-400">(user must change on first login)</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" value="{{ old('password') }}" data-auto-password
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-12 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500
                               @error('password') border-red-400 @enderror">
                    <button type="button" data-password-toggle data-password-target="password"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                        aria-label="Show password" aria-pressed="false">
                        <span data-password-icon-show>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                            </svg>
                        </span>
                        <span data-password-icon-hide class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                            </svg>
                        </span>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" data-auto-password-confirm
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-12 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <button type="button" data-password-toggle data-password-target="password_confirmation"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                        aria-label="Show confirm password" aria-pressed="false">
                        <span data-password-icon-show>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                            </svg>
                        </span>
                        <span data-password-icon-hide class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            @error('role')
                <p class="text-red-500 text-xs mt-1 mb-3">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg font-medium text-sm transition">
                Create Student Account
            </button>
        </form>
    </div>
</div>

<script>
    (function () {
        const initialTab = @json($activeTab);
        const buttons = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.tab-panel');

        function getTemporaryPassword(email) {
            if (!email || !email.includes('@')) {
                return '';
            }

            return email.split('@')[0].trim();
        }

        function syncTemporaryPassword(form) {
            const emailInput = form.querySelector('[data-auto-password-email]');
            const passwordInput = form.querySelector('[data-auto-password]');
            const confirmInput = form.querySelector('[data-auto-password-confirm]');

            if (!emailInput || !passwordInput || !confirmInput) {
                return;
            }

            const applyPassword = () => {
                const temporaryPassword = getTemporaryPassword(emailInput.value);

                if (!temporaryPassword) {
                    return;
                }

                passwordInput.value = temporaryPassword;
                confirmInput.value = temporaryPassword;
            };

            emailInput.addEventListener('input', applyPassword);
            emailInput.addEventListener('blur', applyPassword);

            applyPassword();
        }

        function syncPasswordToggle(button) {
            const targetName = button.dataset.passwordTarget;
            const input = button.closest('form')?.querySelector(`[name="${targetName}"]`);

            if (!input) {
                return;
            }

            const showIcon = button.querySelector('[data-password-icon-show]');
            const hideIcon = button.querySelector('[data-password-icon-hide]');

            const setVisible = (visible) => {
                input.type = visible ? 'text' : 'password';
                button.setAttribute('aria-pressed', visible ? 'true' : 'false');
                button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
                showIcon?.classList.toggle('hidden', visible);
                hideIcon?.classList.toggle('hidden', !visible);
            };

            button.addEventListener('click', () => {
                setVisible(input.type === 'password');
            });
        }

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

        document.querySelectorAll('[data-auto-password-form]').forEach(syncTemporaryPassword);
        document.querySelectorAll('[data-password-toggle]').forEach(syncPasswordToggle);

        setActiveTab(initialTab);
    })();
</script>

@endsection