@extends('layouts.admin')
@section('title', 'Create Account')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ── Page Shell ── */
    .ca-page {
        font-family: 'Inter', sans-serif;
    }

    /* ── Two-column form layout ── */
    .ca-form-cols {
        display: grid;
        grid-template-columns: 1fr 1px 1fr;
        gap: 0 32px;
        align-items: start;
    }

    .ca-col-divider {
        background: #f0f0f5;
        align-self: stretch;
        margin: 4px 0;
        border-radius: 2px;
    }

    /* Tighten field spacing in 2-col view */
    .ca-form-cols .ca-field {
        margin-bottom: 14px;
    }

    .ca-form-cols .ca-section-label {
        margin-top: 0;
    }

    /* ── Breadcrumb ── */
    .ca-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #7c3aed;
        text-decoration: none;
        margin-bottom: 20px;
        transition: color 0.15s;
    }
    .ca-back:hover { color: #5b21b6; }
    .ca-back svg { width: 14px; height: 14px; }

    /* ── Page header ── */
    .ca-page-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 24px;
        letter-spacing: -0.4px;
    }

    /* ── Main card ── */
    .ca-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 8px 32px rgba(109,40,217,0.06);
        overflow: hidden;
    }

    /* ── Card gradient header band ── */
    .ca-card-header {
        background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 60%, #8b5cf6 100%);
        padding: 24px 28px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ca-card-header-icon {
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ca-card-header-icon svg {
        width: 22px;
        height: 22px;
        stroke: #fff;
    }

    .ca-card-header-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.2px;
    }

    .ca-card-header-sub {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.72);
        margin-top: 2px;
    }

    /* ── Card body ── */
    .ca-card-body {
        padding: 24px 28px 28px;
    }

    /* ── Tab switcher ── */
    .ca-tab-bar {
        display: inline-flex;
        background: #f3f4f6;
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 28px;
        gap: 4px;
    }

    .ca-tab-btn {
        padding: 9px 22px;
        border: none;
        border-radius: 9px;
        font-size: 0.85rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .ca-tab-btn svg {
        width: 15px;
        height: 15px;
        stroke: currentColor;
    }

    .ca-tab-btn[aria-pressed="true"] {
        background: #fff;
        color: #6d28d9;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1), 0 2px 12px rgba(109,40,217,0.12);
    }

    .ca-tab-btn[aria-pressed="false"]:hover {
        color: #374151;
        background: rgba(255,255,255,0.5);
    }

    /* ── Section divider ── */
    .ca-section-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9ca3af;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f3f4f6;
    }

    /* ── Form grid ── */
    .ca-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* ── Field group ── */
    .ca-field {
        margin-bottom: 18px;
    }

    .ca-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .ca-label-hint {
        font-weight: 400;
        color: #9ca3af;
        font-size: 0.75rem;
    }

    /* ── Inputs ── */
    .ca-input,
    .ca-select {
        width: 100%;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
        color: #111827;
        background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none;
        box-sizing: border-box;
        -webkit-appearance: none;
        appearance: none;
    }

    .ca-input:focus,
    .ca-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
        background: #fff;
    }

    .ca-input.ca-error,
    .ca-select.ca-error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
    }

    /* Select arrow */
    .ca-select-wrap {
        position: relative;
    }

    .ca-select-wrap::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 13px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 5px solid #9ca3af;
        pointer-events: none;
    }

    .ca-select { padding-right: 34px; }

    /* ── Password field ── */
    .ca-pw-wrap {
        position: relative;
    }

    .ca-pw-wrap .ca-input {
        padding-right: 72px;
    }

    .ca-pw-actions {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        gap: 2px;
        padding-right: 6px;
    }

    .ca-pw-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 7px;
        transition: color 0.15s, background 0.15s;
        padding: 0;
    }

    .ca-pw-btn:hover {
        color: #6d28d9;
        background: rgba(109,40,217,0.07);
    }

    .ca-pw-btn svg {
        width: 17px;
        height: 17px;
        stroke: currentColor;
    }

    /* ── Confirm password (only eye icon) ── */
    .ca-pw-wrap-sm .ca-input {
        padding-right: 44px;
    }

    .ca-pw-actions-sm {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        padding-right: 10px;
    }

    /* ── Field error ── */
    .ca-field-error {
        font-size: 0.73rem;
        color: #ef4444;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Divider ── */
    .ca-divider {
        height: 1px;
        background: #f3f4f6;
        margin: 22px 0;
    }

    /* ── Submit button ── */
    .ca-submit {
        width: 100%;
        background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 100%);
        color: #fff;
        border: none;
        border-radius: 11px;
        padding: 13px;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        transition: all 0.2s;
        letter-spacing: 0.2px;
        box-shadow: 0 4px 14px rgba(109,40,217,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 24px;
    }

    .ca-submit:hover {
        box-shadow: 0 6px 20px rgba(109,40,217,0.4);
        transform: translateY(-1px);
        background: linear-gradient(135deg, #5b21b6 0%, #6d28d9 100%);
    }

    .ca-submit:active { transform: translateY(0); }

    .ca-submit svg {
        width: 17px;
        height: 17px;
        stroke: #fff;
    }

    /* ── CSV import row ── */
    .ca-import-row {
        display: flex;
        gap: 14px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .ca-import-row input[type="file"] {
        padding: 8px 12px;
    }

    .ca-import-hint {
        font-size: 0.73rem;
        color: #9ca3af;
        margin-top: 6px;
        line-height: 1.4;
    }

    .ca-import-hint code {
        background: #f3f4f6;
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 0.71rem;
    }

    .ca-import-btn {
        width: auto;
        margin-top: 0;
        padding: 10px 22px;
        flex-shrink: 0;
    }

    /* ── Info badge on password label ── */
    .ca-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #fef3c7;
        color: #92400e;
        font-size: 0.67rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 20px;
        vertical-align: middle;
        margin-left: 6px;
    }
</style>
@endpush

@section('content')

@php
    $selectedRole = old('role', $role ?? request('role'));
    $activeTab = $selectedRole === 'parent' ? 'parent' : 'teacher';
@endphp

<div class="ca-page">

    {{-- Back link --}}
    <a href="{{ route('admin.users.index', ['role' => $activeTab]) }}" class="ca-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to users
    </a>

    <h1 class="ca-page-title">Create New Account</h1>

    <div class="ca-card">

        {{-- Card header --}}
        <div class="ca-card-header">
            <div class="ca-card-header-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="ca-card-header-title">New Account Registration</div>
                <div class="ca-card-header-sub">Fill in the details to create a teacher or student account</div>
            </div>
        </div>

        {{-- Card body --}}
        <div class="ca-card-body">

            {{-- Tab switcher --}}
            <div class="ca-tab-bar">
                <button type="button" data-tab-target="teacher"
                    class="ca-tab-btn"
                    aria-pressed="{{ $activeTab === 'teacher' ? 'true' : 'false' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    Teacher
                </button>
                <button type="button" data-tab-target="parent"
                    class="ca-tab-btn"
                    aria-pressed="{{ $activeTab === 'parent' ? 'true' : 'false' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    Student
                </button>
            </div>

            {{-- ════════ TEACHER FORM ════════ --}}
            <form method="POST" action="{{ route('admin.users.store') }}"
                  data-tab-panel="teacher" class="tab-panel" data-auto-password-form>
                @csrf
                <input type="hidden" name="role" value="teacher">

                <div class="ca-form-cols">

                    {{-- LEFT: Personal Info --}}
                    <div>
                        <div class="ca-section-label">Personal Information</div>

                        <div class="ca-field">
                            <label class="ca-label" for="t-name">Full name</label>
                            <input id="t-name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Sarah Johnson"
                                class="ca-input {{ $errors->has('name') ? 'ca-error' : '' }}">
                            @error('name')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-field">
                            <label class="ca-label" for="t-email">Email address</label>
                            <input id="t-email" type="email" name="email" value="{{ old('email') }}"
                                data-auto-password-email
                                placeholder="teacher@school.com"
                                class="ca-input {{ $errors->has('email') ? 'ca-error' : '' }}">
                            @error('email')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-grid-2" style="margin-bottom:0;">
                            <div>
                                <label class="ca-label" for="t-grade">Grade</label>
                                <div class="ca-select-wrap">
                                    <select id="t-grade" name="grade"
                                        class="ca-select {{ $errors->has('grade') ? 'ca-error' : '' }}">
                                        <option value="">Select grade</option>
                                        @for($grade = 1; $grade <= 12; $grade++)
                                            <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>Grade {{ $grade }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @error('grade')
                                    <p class="ca-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="ca-label" for="t-section">Section</label>
                                <div class="ca-select-wrap">
                                    <select id="t-section" name="section"
                                        class="ca-select {{ $errors->has('section') ? 'ca-error' : '' }}">
                                        <option value="">Select section</option>
                                        @foreach(['A', 'B', 'C', 'D', 'E'] as $section)
                                            <option value="{{ $section }}" {{ old('section') === $section ? 'selected' : '' }}>Section {{ $section }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('section')
                                    <p class="ca-field-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Column divider --}}
                    <div class="ca-col-divider"></div>

                    {{-- RIGHT: Security --}}
                    <div style="display:flex;flex-direction:column;">
                        <div class="ca-section-label">Security</div>

                        <div class="ca-field">
                            <label class="ca-label" for="t-password">
                                Temporary password
                                <span class="ca-badge">⚠ Must change on first login</span>
                            </label>
                            <div class="ca-pw-wrap">
                                <input id="t-password" type="password" name="password"
                                    value="{{ old('password') }}" data-auto-password
                                    class="ca-input {{ $errors->has('password') ? 'ca-error' : '' }}">
                                <div class="ca-pw-actions">
                                    <button type="button" data-password-generate
                                        class="ca-pw-btn" title="Auto-generate from email">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.24 6.24A9 9 0 1 0 21 12"/>
                                        </svg>
                                    </button>
                                    <button type="button" data-password-toggle data-password-target="password"
                                        class="ca-pw-btn" aria-label="Show password" aria-pressed="false">
                                        <span data-password-icon-show>
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                                                <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                            </svg>
                                        </span>
                                        <span data-password-icon-hide class="hidden">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-field">
                            <label class="ca-label" for="t-password-confirm">Confirm password</label>
                            <div class="ca-pw-wrap ca-pw-wrap-sm">
                                <input id="t-password-confirm" type="password" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" data-auto-password-confirm
                                    class="ca-input">
                                <div class="ca-pw-actions-sm">
                                    <button type="button" data-password-toggle data-password-target="password_confirmation"
                                        class="ca-pw-btn" aria-label="Show confirm password" aria-pressed="false">
                                        <span data-password-icon-show>
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                                                <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                            </svg>
                                        </span>
                                        <span data-password-icon-hide class="hidden">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="ca-submit" style="margin-top:auto;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Create Teacher Account
                        </button>
                    </div>

                </div>{{-- /ca-form-cols --}}
            </form>

            {{-- ════════ TEACHER CSV IMPORT ════════ --}}
            <div class="tab-panel" data-tab-panel="teacher">
                <div class="ca-divider"></div>
                <div class="ca-section-label">Bulk Import via CSV</div>
                <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data"
                      class="ca-import-row">
                    @csrf
                    <input type="hidden" name="role" value="teacher">
                    <div class="ca-field" style="flex:1; min-width:220px; margin-bottom:0;">
                        <label class="ca-label" for="t-csv">CSV file</label>
                        <input id="t-csv" type="file" name="csv_file" accept=".csv,text/csv" class="ca-input">
                        <p class="ca-import-hint">
                            Columns: <strong>Full name, Email address, Grade, Section</strong>. Passwords are generated automatically (<code>te-</code> + the part of the email before @).
                        </p>
                    </div>
                    <button type="submit" class="ca-submit ca-import-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Import CSV
                    </button>
                </form>
            </div>

            {{-- ════════ STUDENT FORM ════════ --}}
            <form method="POST" action="{{ route('admin.users.store') }}"
                  data-tab-panel="parent" class="tab-panel" data-auto-password-form>
                @csrf
                <input type="hidden" name="role" value="parent">

                <div class="ca-form-cols">

                    {{-- LEFT: Personal Info --}}
                    <div>
                        <div class="ca-section-label">Personal Information</div>

                        <div class="ca-field">
                            <label class="ca-label" for="p-name">Full name</label>
                            <input id="p-name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Alex Johnson"
                                class="ca-input {{ $errors->has('name') ? 'ca-error' : '' }}">
                            @error('name')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-field">
                            <label class="ca-label" for="p-email">Email address</label>
                            <input id="p-email" type="email" name="email" value="{{ old('email') }}"
                                data-auto-password-email
                                placeholder="student@school.com"
                                class="ca-input {{ $errors->has('email') ? 'ca-error' : '' }}">
                            @error('email')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-grid-2" style="margin-bottom:0;">
                            <div>
                                <label class="ca-label" for="p-grade">Grade</label>
                                <div class="ca-select-wrap">
                                    <select id="p-grade" name="grade"
                                        class="ca-select {{ $errors->has('grade') ? 'ca-error' : '' }}">
                                        <option value="">Select grade</option>
                                        @for($grade = 1; $grade <= 12; $grade++)
                                            <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>Grade {{ $grade }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @error('grade')
                                    <p class="ca-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="ca-label" for="p-section">Section</label>
                                <div class="ca-select-wrap">
                                    <select id="p-section" name="section"
                                        class="ca-select {{ $errors->has('section') ? 'ca-error' : '' }}">
                                        <option value="">Select section</option>
                                        @foreach(['A', 'B', 'C', 'D', 'E'] as $section)
                                            <option value="{{ $section }}" {{ old('section') === $section ? 'selected' : '' }}>Section {{ $section }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('section')
                                    <p class="ca-field-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Column divider --}}
                    <div class="ca-col-divider"></div>

                    {{-- RIGHT: Security --}}
                    <div style="display:flex;flex-direction:column;">
                        <div class="ca-section-label">Security</div>

                        <div class="ca-field">
                            <label class="ca-label" for="p-password">
                                Temporary password
                                <span class="ca-badge">⚠ Must change on first login</span>
                            </label>
                            <div class="ca-pw-wrap">
                                <input id="p-password" type="password" name="password"
                                    value="{{ old('password') }}" data-auto-password
                                    class="ca-input {{ $errors->has('password') ? 'ca-error' : '' }}">
                                <div class="ca-pw-actions">
                                    <button type="button" data-password-generate
                                        class="ca-pw-btn" title="Auto-generate from email">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.24 6.24A9 9 0 1 0 21 12"/>
                                        </svg>
                                    </button>
                                    <button type="button" data-password-toggle data-password-target="password"
                                        class="ca-pw-btn" aria-label="Show password" aria-pressed="false">
                                        <span data-password-icon-show>
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                                                <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                            </svg>
                                        </span>
                                        <span data-password-icon-hide class="hidden">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-field">
                            <label class="ca-label" for="p-password-confirm">Confirm password</label>
                            <div class="ca-pw-wrap ca-pw-wrap-sm">
                                <input id="p-password-confirm" type="password" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" data-auto-password-confirm
                                    class="ca-input">
                                <div class="ca-pw-actions-sm">
                                    <button type="button" data-password-toggle data-password-target="password_confirmation"
                                        class="ca-pw-btn" aria-label="Show confirm password" aria-pressed="false">
                                        <span data-password-icon-show>
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                                                <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                            </svg>
                                        </span>
                                        <span data-password-icon-hide class="hidden">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @error('role')
                            <p class="ca-field-error" style="margin-bottom:8px;">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="ca-submit" style="margin-top:auto;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Create Student Account
                        </button>
                    </div>

                </div>{{-- /ca-form-cols --}}
            </form>

            {{-- ════════ STUDENT CSV IMPORT ════════ --}}
            <div class="tab-panel" data-tab-panel="parent">
                <div class="ca-divider"></div>
                <div class="ca-section-label">Bulk Import via CSV</div>
                <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data"
                      class="ca-import-row">
                    @csrf
                    <input type="hidden" name="role" value="parent">
                    <div class="ca-field" style="flex:1; min-width:220px; margin-bottom:0;">
                        <label class="ca-label" for="p-csv">CSV file</label>
                        <input id="p-csv" type="file" name="csv_file" accept=".csv,text/csv" class="ca-input">
                        <p class="ca-import-hint">
                            Columns: <strong>Full name, Email address, Grade, Section</strong>. Passwords are generated automatically (<code>st-</code> + the part of the email before @).
                        </p>
                    </div>
                    <button type="submit" class="ca-submit ca-import-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Import CSV
                    </button>
                </form>
            </div>

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}
</div>{{-- /page --}}

<script>
    (function () {
        const initialTab = @json($activeTab);
        const buttons = document.querySelectorAll('.ca-tab-btn');
        const panels = document.querySelectorAll('.tab-panel');

        function getTemporaryPassword(email, role) {
            if (!email || !email.includes('@')) return '';
            const local = email.split('@')[0].trim();
            if (!local) return '';
            let prefix = '';
            if (role === 'parent') prefix = 'st-';
            else if (role === 'teacher') prefix = 'te-';
            return prefix + local;
        }

        function syncTemporaryPassword(form) {
            const emailInput = form.querySelector('[data-auto-password-email]');
            const passwordInput = form.querySelector('[data-auto-password]');
            const confirmInput = form.querySelector('[data-auto-password-confirm]');
            const roleInput = form.querySelector('[name="role"]');
            const genBtn = form.querySelector('[data-password-generate]');

            if (!emailInput || !passwordInput || !confirmInput) return;
            const role = roleInput ? roleInput.value : '';

            const applyPassword = () => {
                const pw = getTemporaryPassword(emailInput.value, role);
                if (!pw) return;
                passwordInput.value = pw;
                confirmInput.value = pw;
            };

            emailInput.addEventListener('input', applyPassword);
            emailInput.addEventListener('blur', applyPassword);

            if (genBtn) {
                genBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const pw = getTemporaryPassword(emailInput.value, role);
                    if (!pw) return;
                    passwordInput.value = pw;
                    confirmInput.value = pw;
                    passwordInput.focus();
                });
            }

            applyPassword();
        }

        function syncPasswordToggle(button) {
            const targetName = button.dataset.passwordTarget;
            const input = button.closest('form')?.querySelector(`[name="${targetName}"]`);
            if (!input) return;

            const showIcon = button.querySelector('[data-password-icon-show]');
            const hideIcon = button.querySelector('[data-password-icon-hide]');

            const setVisible = (visible) => {
                input.type = visible ? 'text' : 'password';
                button.setAttribute('aria-pressed', visible ? 'true' : 'false');
                button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
                showIcon?.classList.toggle('hidden', visible);
                hideIcon?.classList.toggle('hidden', !visible);
            };

            button.addEventListener('click', () => setVisible(input.type === 'password'));
        }

        function setActiveTab(tab) {
            buttons.forEach((btn) => {
                const isActive = btn.dataset.tabTarget === tab;
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.tabPanel !== tab);
            });
        }

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => setActiveTab(btn.dataset.tabTarget));
        });

        document.querySelectorAll('[data-auto-password-form]').forEach(syncTemporaryPassword);
        document.querySelectorAll('[data-password-toggle]').forEach(syncPasswordToggle);

        setActiveTab(initialTab);
    })();
</script>

@endsection