@extends('layouts.teacher')
@section('title', 'Create Student Account')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --t-accent:       #0f766e;
        --t-accent-light: #ccfbf1;
        --t-accent-mid:   #14b8a6;
    }

    /* ── Page Shell ── */
    .ca-page { font-family: 'Inter', sans-serif; }

    /* ── Two-column form layout ── */
    .ca-form-cols {
        display: grid;
        grid-template-columns: 1fr 1px 1fr;
        gap: 0 32px;
        align-items: start;
    }
    .ca-col-divider { background: #f0f0f5; align-self: stretch; margin: 4px 0; border-radius: 2px; }
    .ca-form-cols .ca-field { margin-bottom: 14px; }
    .ca-form-cols .ca-section-label { margin-top: 0; }

    /* ── Breadcrumb ── */
    .ca-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.8rem; font-weight: 500; color: var(--t-accent);
        text-decoration: none; margin-bottom: 20px; transition: color 0.15s;
    }
    .ca-back:hover { color: #0d5f58; }
    .ca-back svg { width: 14px; height: 14px; }

    /* ── Page header ── */
    .ca-page-title {
        font-size: 1.6rem; font-weight: 700; color: #111827;
        margin-bottom: 24px; letter-spacing: -0.4px;
    }

    /* ── Main card ── */
    .ca-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 8px 32px rgba(15,118,110,0.08);
        overflow: hidden;
    }

    /* ── Card gradient header band ── */
    .ca-card-header {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 60%, #14b8a6 100%);
        padding: 24px 28px 20px;
        display: flex; align-items: center; gap: 14px;
    }
    .ca-card-header-icon {
        width: 44px; height: 44px; background: rgba(255,255,255,0.18);
        border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ca-card-header-icon svg { width: 22px; height: 22px; stroke: #fff; }
    .ca-card-header-title { font-size: 1.1rem; font-weight: 700; color: #fff; letter-spacing: -0.2px; }
    .ca-card-header-sub { font-size: 0.78rem; color: rgba(255,255,255,0.8); margin-top: 2px; }

    /* ── Card body ── */
    .ca-card-body { padding: 24px 28px 28px; }

    /* ── Class badge (fixed, read-only) ── */
    .ca-class-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--t-accent-light); color: var(--t-accent);
        border-radius: 12px; padding: 10px 16px;
        font-size: 0.85rem; font-weight: 700;
        margin-bottom: 28px;
    }
    .ca-class-badge svg { width: 16px; height: 16px; }
    .ca-class-badge span.hint { font-weight: 400; color: #0f766e; opacity: .75; }

    /* ── Section divider ── */
    .ca-section-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        color: #9ca3af; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;
    }

    /* ── Field group ── */
    .ca-field { margin-bottom: 18px; }
    .ca-label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px; }

    /* ── Inputs ── */
    .ca-input {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 10px 14px; font-size: 0.875rem; font-family: 'Inter', sans-serif;
        color: #111827; background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none; box-sizing: border-box;
    }
    .ca-input:focus { border-color: var(--t-accent-mid); box-shadow: 0 0 0 3px rgba(20,184,166,0.14); background: #fff; }
    .ca-input.ca-error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.08); }

    /* ── Password field ── */
    .ca-pw-wrap { position: relative; }
    .ca-pw-wrap .ca-input { padding-right: 72px; }
    .ca-pw-actions { position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; gap: 2px; padding-right: 6px; }
    .ca-pw-btn {
        background: none; border: none; cursor: pointer; color: #9ca3af;
        display: flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px;
        transition: color 0.15s, background 0.15s; padding: 0;
    }
    .ca-pw-btn:hover { color: var(--t-accent); background: rgba(15,118,110,0.08); }
    .ca-pw-btn svg { width: 17px; height: 17px; stroke: currentColor; }
    .ca-pw-wrap-sm .ca-input { padding-right: 44px; }
    .ca-pw-actions-sm { position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; padding-right: 10px; }

    /* ── Field error ── */
    .ca-field-error { font-size: 0.73rem; color: #ef4444; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

    /* ── Divider ── */
    .ca-divider { height: 1px; background: #f3f4f6; margin: 22px 0; }

    /* ── Submit button ── */
    .ca-submit {
        width: 100%; background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
        color: #fff; border: none; border-radius: 11px; padding: 13px;
        font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;
        cursor: pointer; transition: all 0.2s; letter-spacing: 0.2px;
        box-shadow: 0 4px 14px rgba(15,118,110,0.3);
        display: flex; align-items: center; justify-content: center; gap: 8px;
        margin-top: 24px;
    }
    .ca-submit:hover { box-shadow: 0 6px 20px rgba(15,118,110,0.4); transform: translateY(-1px); background: linear-gradient(135deg, #0d5f58 0%, #0f766e 100%); }
    .ca-submit:active { transform: translateY(0); }
    .ca-submit svg { width: 17px; height: 17px; stroke: #fff; }

    /* ── CSV import row ── */
    .ca-import-row { display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap; }
    .ca-import-row input[type="file"] { padding: 8px 12px; }
    .ca-import-hint { font-size: 0.73rem; color: #9ca3af; margin-top: 6px; line-height: 1.4; }
    .ca-import-hint code { background: #f3f4f6; border-radius: 4px; padding: 1px 5px; font-size: 0.71rem; }
    .ca-import-btn { width: auto; margin-top: 0; padding: 10px 22px; flex-shrink: 0; }

    /* ── Info badge on password label ── */
    .ca-badge {
        display: inline-flex; align-items: center; gap: 3px;
        background: #fef3c7; color: #92400e; font-size: 0.67rem; font-weight: 600;
        padding: 2px 7px; border-radius: 20px; vertical-align: middle; margin-left: 6px;
    }
</style>
@endpush

@section('content')

<div class="ca-page max-w-4xl">

    {{-- Back link --}}
    <a href="{{ route('teacher.dashboard') }}" class="ca-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to dashboard
    </a>

    <h1 class="ca-page-title">Create Student Account</h1>

    <div class="ca-card">

        {{-- Card header --}}
        <div class="ca-card-header">
            <div class="ca-card-header-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="ca-card-header-title">New Student Registration</div>
                <div class="ca-card-header-sub">Fill in the details to create a login account for a student in your class</div>
            </div>
        </div>

        {{-- Card body --}}
        <div class="ca-card-body">

            <div class="ca-class-badge">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Class {{ $schoolClass->name }}
                <span class="hint">— new students are added to your class automatically</span>
            </div>

            {{-- ════════ STUDENT FORM ════════ --}}
            <form method="POST" action="{{ route('teacher.students.store') }}" data-auto-password-form>
                @csrf

                <div class="ca-form-cols">

                    {{-- LEFT: Personal Info --}}
                    <div>
                        <div class="ca-section-label">Personal Information</div>

                        <div class="ca-field">
                            <label class="ca-label" for="s-name">Full name</label>
                            <input id="s-name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Alex Johnson"
                                class="ca-input {{ $errors->has('name') ? 'ca-error' : '' }}">
                            @error('name')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ca-field">
                            <label class="ca-label" for="s-email">Email address</label>
                            <input id="s-email" type="email" name="email" value="{{ old('email') }}"
                                data-auto-password-email
                                placeholder="student@school.com"
                                class="ca-input {{ $errors->has('email') ? 'ca-error' : '' }}">
                            @error('email')
                                <p class="ca-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Column divider --}}
                    <div class="ca-col-divider"></div>

                    {{-- RIGHT: Security --}}
                    <div style="display:flex;flex-direction:column;">
                        <div class="ca-section-label">Security</div>

                        <div class="ca-field">
                            <label class="ca-label" for="s-password">
                                Temporary password
                                <span class="ca-badge">⚠ Must change on first login</span>
                            </label>
                            <div class="ca-pw-wrap">
                                <input id="s-password" type="password" name="password"
                                    value="{{ old('password') }}" data-auto-password
                                    class="ca-input {{ $errors->has('password') ? 'ca-error' : '' }}">
                                <div class="ca-pw-actions">
                                    <button type="button" data-password-generate class="ca-pw-btn" title="Auto-generate from email">
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
                            <label class="ca-label" for="s-password-confirm">Confirm password</label>
                            <div class="ca-pw-wrap ca-pw-wrap-sm">
                                <input id="s-password-confirm" type="password" name="password_confirmation"
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
                            Create Student Account
                        </button>
                    </div>

                </div>{{-- /ca-form-cols --}}
            </form>

            {{-- ════════ CSV IMPORT ════════ --}}
            <div class="ca-divider"></div>
            <div class="ca-section-label">Bulk Import via CSV</div>
            <form method="POST" action="{{ route('teacher.students.import') }}" enctype="multipart/form-data" class="ca-import-row">
                @csrf
                <div class="ca-field" style="flex:1; min-width:220px; margin-bottom:0;">
                    <label class="ca-label" for="s-csv">CSV file</label>
                    <input id="s-csv" type="file" name="csv_file" accept=".csv,text/csv" class="ca-input">
                    <p class="ca-import-hint">
                        Columns: <strong>Full name, Email address</strong>. All students are added to {{ $schoolClass->name }}. Passwords are generated automatically (<code>st-</code> + the part of the email before @).
                    </p>
                </div>
                <button type="submit" class="ca-submit ca-import-btn">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Import CSV
                </button>
            </form>

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}
</div>{{-- /page --}}

<script>
    (function () {
        function getTemporaryPassword(email) {
            if (!email || !email.includes('@')) return '';
            const local = email.split('@')[0].trim();
            if (!local) return '';
            return 'st-' + local;
        }

        function syncTemporaryPassword(form) {
            const emailInput = form.querySelector('[data-auto-password-email]');
            const passwordInput = form.querySelector('[data-auto-password]');
            const confirmInput = form.querySelector('[data-auto-password-confirm]');
            const genBtn = form.querySelector('[data-password-generate]');

            if (!emailInput || !passwordInput || !confirmInput) return;

            const applyPassword = () => {
                const pw = getTemporaryPassword(emailInput.value);
                if (!pw) return;
                passwordInput.value = pw;
                confirmInput.value = pw;
            };

            emailInput.addEventListener('input', applyPassword);
            emailInput.addEventListener('blur', applyPassword);

            if (genBtn) {
                genBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const pw = getTemporaryPassword(emailInput.value);
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

        document.querySelectorAll('[data-auto-password-form]').forEach(syncTemporaryPassword);
        document.querySelectorAll('[data-password-toggle]').forEach(syncPasswordToggle);
    })();
</script>

@endsection
