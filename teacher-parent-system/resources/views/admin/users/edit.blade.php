@extends('layouts.admin')
@section('title', 'Edit User')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .ca-page { font-family: 'Inter', sans-serif; }

    .ca-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.8rem; font-weight: 500; color: #7c3aed;
        text-decoration: none; margin-bottom: 20px; transition: color 0.15s;
    }
    .ca-back:hover { color: #5b21b6; }
    .ca-back svg { width: 14px; height: 14px; }

    .ca-page-title { font-size: 1.6rem; font-weight: 700; color: #111827; margin-bottom: 24px; letter-spacing: -0.4px; }

    .ca-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 8px 32px rgba(109,40,217,0.06);
        overflow: hidden;
    }
    .ca-card-header {
        background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 60%, #8b5cf6 100%);
        padding: 24px 28px 20px; display: flex; align-items: center; gap: 14px;
    }
    .ca-card-header-icon {
        width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ca-card-header-icon svg { width: 22px; height: 22px; stroke: #fff; }
    .ca-card-header-title { font-size: 1.1rem; font-weight: 700; color: #fff; letter-spacing: -0.2px; }
    .ca-card-header-sub { font-size: 0.78rem; color: rgba(255,255,255,0.72); margin-top: 2px; }
    .ca-card-body { padding: 24px 28px 28px; }

    .ca-section-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        color: #9ca3af; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;
    }
    .ca-section-label:not(:first-child) { margin-top: 26px; }

    .ca-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .ca-field { margin-bottom: 18px; }
    .ca-label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .ca-label-hint { font-weight: 400; color: #9ca3af; font-size: 0.75rem; }

    .ca-input, .ca-select {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 14px;
        font-size: 0.875rem; font-family: 'Inter', sans-serif; color: #111827; background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none;
        box-sizing: border-box; -webkit-appearance: none; appearance: none;
    }
    .ca-input:focus, .ca-select:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.12); background: #fff; }
    .ca-input.ca-error, .ca-select.ca-error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.08); }

    .ca-select-wrap { position: relative; }
    .ca-select-wrap::after {
        content: ''; position: absolute; top: 50%; right: 13px; transform: translateY(-50%);
        width: 0; height: 0; border-left: 4px solid transparent; border-right: 4px solid transparent;
        border-top: 5px solid #9ca3af; pointer-events: none;
    }
    .ca-select { padding-right: 34px; }

    .ca-pw-wrap { position: relative; }
    .ca-pw-wrap .ca-input { padding-right: 44px; }
    .ca-pw-actions-sm { position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; padding-right: 10px; }
    .ca-pw-btn {
        background: none; border: none; cursor: pointer; color: #9ca3af;
        display: flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px; transition: color 0.15s, background 0.15s; padding: 0;
    }
    .ca-pw-btn:hover { color: #6d28d9; background: rgba(109,40,217,0.07); }
    .ca-pw-btn svg { width: 17px; height: 17px; stroke: currentColor; }

    .ca-field-error { font-size: 0.73rem; color: #ef4444; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

    .ca-current-class {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f5f3ff; color: #6d28d9; font-size: 0.72rem; font-weight: 700;
        padding: 4px 10px; border-radius: 999px; margin-left: 8px;
    }

    .ca-submit {
        width: 100%; background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 100%); color: #fff;
        border: none; border-radius: 11px; padding: 13px; font-size: 0.9rem; font-weight: 600;
        font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.2s; letter-spacing: 0.2px;
        box-shadow: 0 4px 14px rgba(109,40,217,0.3);
        display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 24px;
    }
    .ca-submit:hover { box-shadow: 0 6px 20px rgba(109,40,217,0.4); transform: translateY(-1px); background: linear-gradient(135deg, #5b21b6 0%, #6d28d9 100%); }
    .ca-submit:active { transform: translateY(0); }
    .ca-submit svg { width: 17px; height: 17px; stroke: #fff; }
</style>
@endpush

@section('content')
@php
    $roleLabel = $user->role === 'parent' ? 'Student' : ucfirst($user->role);
    $currentClassName = $selectedGrade && $selectedSection ? $selectedGrade.'-'.$selectedSection : null;
@endphp
<div class="ca-page max-w-2xl">
    <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" class="ca-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to users
    </a>

    <h1 class="ca-page-title">Edit {{ $roleLabel }} — {{ $user->name }}</h1>

    <div class="ca-card">
        <div class="ca-card-header">
            <div class="ca-card-header-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="ca-card-header-title">{{ $roleLabel }} Account
                    @if($currentClassName)<span class="ca-current-class">Class {{ $currentClassName }}</span>@endif
                </div>
                <div class="ca-card-header-sub">Update profile details, class assignment, or reset the password</div>
            </div>
        </div>

        <div class="ca-card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PUT')

                <div class="ca-section-label">Personal Information</div>

                <div class="ca-field">
                    <label class="ca-label" for="u-name">Full name</label>
                    <input id="u-name" type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="ca-input {{ $errors->has('name') ? 'ca-error' : '' }}">
                    @error('name')<p class="ca-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="ca-field">
                    <label class="ca-label" for="u-email">Email address</label>
                    <input id="u-email" type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="ca-input {{ $errors->has('email') ? 'ca-error' : '' }}">
                    @error('email')<p class="ca-field-error">{{ $message }}</p>@enderror
                </div>

                @if(in_array($user->role, ['teacher', 'parent'], true))
                    <div class="ca-section-label">
                        {{ $user->role === 'teacher' ? 'Class Assigned' : "Student's Class" }}
                    </div>

                    <div class="ca-grid-2" style="margin-bottom: 4px;">
                        <div class="ca-field">
                            <label class="ca-label" for="u-grade">Grade</label>
                            <div class="ca-select-wrap">
                                <select id="u-grade" name="grade" class="ca-select {{ $errors->has('grade') ? 'ca-error' : '' }}">
                                    <option value="">Select grade</option>
                                    @for($grade = 1; $grade <= 12; $grade++)
                                        <option value="{{ $grade }}" {{ old('grade', $selectedGrade) == $grade ? 'selected' : '' }}>Grade {{ $grade }}</option>
                                    @endfor
                                </select>
                            </div>
                            @error('grade')<p class="ca-field-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="ca-field">
                            <label class="ca-label" for="u-section">Section</label>
                            <div class="ca-select-wrap">
                                <select id="u-section" name="section" class="ca-select {{ $errors->has('section') ? 'ca-error' : '' }}">
                                    <option value="">Select section</option>
                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $section)
                                        <option value="{{ $section }}" {{ old('section', $selectedSection) === $section ? 'selected' : '' }}>Section {{ $section }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('section')<p class="ca-field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <p class="ca-label-hint" style="margin: -10px 0 18px;">
                        @if($user->role === 'teacher')
                            Moving this teacher to a different class unassigns them from their current one.
                        @else
                            Moving the student's class here updates their student record automatically.
                        @endif
                    </p>
                @endif

                <div class="ca-section-label">Security</div>

                <div class="ca-field">
                    <label class="ca-label" for="u-password">New password <span class="ca-label-hint">(leave blank to keep current)</span></label>
                    <div class="ca-pw-wrap">
                        <input id="u-password" type="password" name="password"
                            class="ca-input {{ $errors->has('password') ? 'ca-error' : '' }}">
                        <div class="ca-pw-actions-sm">
                            <button type="button" data-password-toggle data-password-target="password" class="ca-pw-btn" aria-label="Show password" aria-pressed="false">
                                <span data-password-icon-show>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                                </span>
                                <span data-password-icon-hide class="hidden">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    @error('password')<p class="ca-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="ca-field">
                    <label class="ca-label" for="u-password-confirm">Confirm new password</label>
                    <div class="ca-pw-wrap">
                        <input id="u-password-confirm" type="password" name="password_confirmation" class="ca-input">
                        <div class="ca-pw-actions-sm">
                            <button type="button" data-password-toggle data-password-target="password_confirmation" class="ca-pw-btn" aria-label="Show confirm password" aria-pressed="false">
                                <span data-password-icon-show>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                                </span>
                                <span data-password-icon-hide class="hidden">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="ca-submit">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const targetName = button.dataset.passwordTarget;
            const input = button.closest('form')?.querySelector(`[name="${targetName}"]`);
            if (!input) return;

            const showIcon = button.querySelector('[data-password-icon-show]');
            const hideIcon = button.querySelector('[data-password-icon-hide]');

            button.addEventListener('click', () => {
                const visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';
                button.setAttribute('aria-pressed', visible ? 'true' : 'false');
                button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
                showIcon?.classList.toggle('hidden', visible);
                hideIcon?.classList.toggle('hidden', !visible);
            });
        });
    })();
</script>
@endsection
