@extends('layouts.teacher')
@section('title', 'My Profile')

@push('styles')
<style>
    :root {
        --pf-accent: #0f766e;
        --pf-accent-mid: #14b8a6;
        --pf-accent-light: #ccfbf1;
    }

    /* ── Hero ── */
    .pf-hero {
        position: relative; overflow: hidden;
        border-radius: 18px; padding: 28px 30px;
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
        color: #fff; margin-bottom: 22px;
        display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
    }
    .pf-hero::after {
        content: ''; position: absolute; top: -60px; right: -60px;
        width: 220px; height: 220px; border-radius: 50%;
        background: rgba(255,255,255,.07);
    }
    .pf-avatar {
        width: 68px; height: 68px; border-radius: 50%; flex-shrink: 0;
        background: rgba(255,255,255,.16); border: 2px solid rgba(255,255,255,.35);
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; font-weight: 800; color: #fff; z-index: 1;
    }
    .pf-hero-body { z-index: 1; min-width: 0; }
    .pf-name { font-size: 21px; font-weight: 800; margin: 0 0 4px; }
    .pf-meta { display: flex; flex-wrap: wrap; gap: 8px 14px; font-size: 12.5px; color: rgba(255,255,255,.85); }
    .pf-meta-item { display: inline-flex; align-items: center; gap: 5px; }
    .pf-meta-item svg { width: 13px; height: 13px; flex-shrink: 0; }
    .pf-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
        border-radius: 999px; padding: 3px 12px; font-size: 11px; font-weight: 700;
        letter-spacing: .03em; text-transform: uppercase; margin-bottom: 8px; z-index: 1;
    }

    /* ── Layout grid ── */
    .pf-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .pf-grid { grid-template-columns: 1fr; } }

    .pf-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden; }
    .pf-card-header {
        display: flex; align-items: center; gap: 10px; padding: 16px 22px;
        border-bottom: 1px solid #f3f4f6;
    }
    .pf-card-header svg { width: 16px; height: 16px; color: var(--pf-accent); }
    .pf-card-title { font-size: 13.5px; font-weight: 800; color: #111827; }
    .pf-card-body { padding: 22px; }

    .pf-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 560px) { .pf-field-row { grid-template-columns: 1fr; } }
    .pf-field { margin-bottom: 16px; }
    .pf-error { color: #ef4444; font-size: 11.5px; margin-top: 4px; }

    .pf-stat-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 12.5px;
    }
    .pf-stat-row:last-child { border-bottom: none; }
    .pf-stat-label { color: #6b7280; display: flex; align-items: center; gap: 7px; }
    .pf-stat-label svg { width: 14px; height: 14px; color: #9ca3af; }
    .pf-stat-value { font-weight: 700; color: #111827; }

    .pf-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; background: var(--pf-accent); color: #fff;
        border: none; border-radius: 11px; padding: 12px 20px; font-size: .875rem; font-weight: 700;
        cursor: pointer; transition: all .2s; box-shadow: 0 4px 14px rgba(15,118,110,.28);
    }
    .pf-submit:hover { background: #0d5f58; box-shadow: 0 6px 20px rgba(15,118,110,.35); transform: translateY(-1px); }
</style>
@endpush

@section('content')
@php
    $displayName = $profile?->full_name ?: $teacher->name;
    $initial = strtoupper(substr($displayName, 0, 1));
@endphp

<div class="max-w-7xl">

    {{-- ── Hero ── --}}
    <div class="pf-hero">
        <div class="pf-avatar">{{ $initial }}</div>
        <div class="pf-hero-body">
            <span class="pf-badge">
                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11Z"/></svg>
                Teacher
            </span>
            <p class="pf-name">{{ $displayName }}</p>
            <div class="pf-meta">
                <span class="pf-meta-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                    {{ $profile?->email_address ?? $teacher->email }}
                </span>
                <span class="pf-meta-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    {{ $schoolClass ? "Class {$schoolClass->name}" : 'No class assigned' }}
                </span>
                @if($schoolClass)
                    <span class="pf-meta-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $schoolClass->students_count }} {{ $schoolClass->students_count === 1 ? 'student' : 'students' }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="pf-grid">
        {{-- ── Left: editable personal details ── --}}
        <div class="pf-card">
            <div class="pf-card-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25"/></svg>
                <span class="pf-card-title">Personal Details</span>
            </div>
            <div class="pf-card-body">
                <form method="POST" action="{{ route('teacher.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="pf-field">
                        <label class="ui-field-label">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $profile?->full_name ?? $teacher->name) }}" class="ui-input">
                        @error('full_name') <p class="pf-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pf-field">
                        <label class="ui-field-label">Address</label>
                        <input type="text" name="address" value="{{ old('address', $profile?->address) }}" class="ui-input" placeholder="Street, city">
                        @error('address') <p class="pf-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pf-field-row">
                        <div class="pf-field">
                            <label class="ui-field-label">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $profile?->phone_number) }}" class="ui-input" placeholder="e.g. 077 123 4567">
                            @error('phone_number') <p class="pf-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="pf-field">
                            <label class="ui-field-label">Email Address</label>
                            <input type="email" name="email_address" value="{{ old('email_address', $profile?->email_address ?? $teacher->email) }}" class="ui-input">
                            @error('email_address') <p class="pf-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <button type="submit" class="pf-submit" style="margin-top:6px">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Right: snapshot + password ── --}}
        <div class="flex flex-col gap-5">
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 17V9m6 8V5M9 17H5.75A1.75 1.75 0 0 1 4 15.25V15m5 2h4m0 0h3.25A1.75 1.75 0 0 0 20 15.25V6.75A1.75 1.75 0 0 0 18.25 5H15"/></svg>
                    <span class="pf-card-title">Account Overview</span>
                </div>
                <div class="pf-card-body" style="padding-top:6px;padding-bottom:6px">
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                            Login Email
                        </span>
                        <span class="pf-stat-value">{{ $teacher->email }}</span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            Assigned Class
                        </span>
                        <span class="pf-stat-value">{{ $schoolClass?->name ?? '—' }}</span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Students
                        </span>
                        <span class="pf-stat-value">{{ $schoolClass->students_count ?? 0 }}</span>
                    </div>
                </div>
                @if($schoolClass)
                    <div style="padding:14px 22px 20px;display:flex;gap:8px;flex-wrap:wrap">
                        <a href="{{ route('teacher.attendance.index') }}" class="ui-tag ui-tag-teal" style="text-decoration:none">Mark Attendance</a>
                        <a href="{{ route('teacher.homework.index') }}" class="ui-tag ui-tag-indigo" style="text-decoration:none">View Homework</a>
                    </div>
                @endif
            </div>

            <div class="pf-card">
                <div class="pf-card-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4.75" y="10.75" width="14.5" height="9" rx="1.75"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>
                    <span class="pf-card-title">Change Password</span>
                </div>
                <div class="pf-card-body">
                    <form method="POST" action="{{ route('teacher.profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="pf-field">
                            <label class="ui-field-label">Current Password</label>
                            <input type="password" name="current_password" class="ui-input">
                            @error('current_password') <p class="pf-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="pf-field">
                            <label class="ui-field-label">New Password</label>
                            <input type="password" name="password" class="ui-input">
                            @error('password') <p class="pf-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="pf-field" style="margin-bottom:6px">
                            <label class="ui-field-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="ui-input">
                        </div>

                        <button type="submit" class="pf-submit">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4.75" y="10.75" width="14.5" height="9" rx="1.75"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
