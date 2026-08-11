@extends('layouts.parent')
@section('title', 'My Profile')

@push('styles')
<style>
    :root {
        --pf-accent: #c2410c;
        --pf-accent-mid: #f97316;
        --pf-accent-light: #ffedd5;
    }

    /* ── Hero ── */
    .pf-hero {
        position: relative; overflow: hidden;
        border-radius: 18px; padding: 28px 30px;
        background: linear-gradient(135deg, #c2410c 0%, #9a3412 100%);
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
    .pf-input-disabled {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 13px;
        font-size: .875rem; color: #6b7280; background: #f9fafb; box-sizing: border-box;
    }

    .pf-stat-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 12.5px;
    }
    .pf-stat-row:last-child { border-bottom: none; }
    .pf-stat-label { color: #6b7280; display: flex; align-items: center; gap: 7px; }
    .pf-stat-label svg { width: 14px; height: 14px; color: #9ca3af; }
    .pf-stat-value { font-weight: 700; color: #111827; text-align: right; }

    .pf-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; background: var(--pf-accent); color: #fff;
        border: none; border-radius: 11px; padding: 12px 20px; font-size: .875rem; font-weight: 700;
        cursor: pointer; transition: all .2s; box-shadow: 0 4px 14px rgba(194,65,12,.28);
    }
    .pf-submit:hover { background: #9a3412; box-shadow: 0 6px 20px rgba(194,65,12,.35); transform: translateY(-1px); }
</style>
@endpush

@section('content')
@php
    $displayName = auth()->user()->name;
    $initial = strtoupper(substr($displayName, 0, 1));
@endphp

<div class="max-w-5xl">

    {{-- ── Hero ── --}}
    <div class="pf-hero">
        <div class="pf-avatar">{{ $initial }}</div>
        <div class="pf-hero-body">
            <span class="pf-badge">
                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25"/></svg>
                Parent
            </span>
            <p class="pf-name">{{ $displayName }}</p>
            <div class="pf-meta">
                <span class="pf-meta-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z"/></svg>
                    {{ auth()->user()->email }}
                </span>
                @if($student)
                    <span class="pf-meta-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5Z"/></svg>
                        {{ $student->name }}
                    </span>
                    <span class="pf-meta-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        {{ $student->schoolClass?->name ?? 'No class assigned' }}
                    </span>
                @else
                    <span class="pf-meta-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5Z"/></svg>
                        No student linked yet
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if($errors->has('profile'))
        <div class="flash-error">
            <svg style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            {{ $errors->first('profile') }}
        </div>
    @endif

    <div class="pf-grid">
        {{-- ── Left: editable student details ── --}}
        <div class="pf-card">
            <div class="pf-card-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5Z"/></svg>
                <span class="pf-card-title">Student Details</span>
            </div>
            <div class="pf-card-body">
                @if(!$student)
                    <div class="ui-empty" style="padding:24px 0">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5Z"/></svg>
                        <p>No student record is linked to this account yet.</p>
                        <p class="ui-empty-sub">Contact an admin to have your child linked.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('parent.profile.student') }}">
                        @csrf
                        @method('PUT')

                        <div class="pf-field">
                            <label class="ui-field-label">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $profile?->full_name ?? $student->name) }}" class="ui-input">
                            @error('full_name') <p class="pf-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="pf-field-row">
                            <div class="pf-field">
                                <label class="ui-field-label">Admission No.</label>
                                <input type="text" value="{{ $student->admission_number ?? '—' }}" readonly class="pf-input-disabled">
                            </div>
                            <div class="pf-field">
                                <label class="ui-field-label">Index Number</label>
                                <input type="text" name="index_number" value="{{ old('index_number', $profile?->index_number) }}" class="ui-input" placeholder="Unique index number">
                                @error('index_number') <p class="pf-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pf-field-row">
                            <div class="pf-field">
                                <label class="ui-field-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($profile?->date_of_birth)->format('Y-m-d')) }}" class="ui-input">
                                @error('date_of_birth') <p class="pf-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="pf-field">
                                <label class="ui-field-label">Gender</label>
                                <select name="gender" class="ui-input">
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender', $profile?->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $profile?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $profile?->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <p class="pf-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pf-field-row">
                            <div class="pf-field">
                                <label class="ui-field-label">Parent Phone Number</label>
                                <input type="text" name="parent_phone_number" value="{{ old('parent_phone_number', $profile?->parent_phone_number) }}" class="ui-input" placeholder="e.g. 077 123 4567">
                                @error('parent_phone_number') <p class="pf-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="pf-field">
                                <label class="ui-field-label">Parent Email</label>
                                <input type="email" name="parent_email" value="{{ old('parent_email', $profile?->parent_email ?? auth()->user()->email) }}" class="ui-input">
                                @error('parent_email') <p class="pf-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pf-field" style="margin-bottom:22px">
                            <label class="ui-field-label">Address</label>
                            <textarea name="address" rows="3" class="ui-input" style="resize:vertical">{{ old('address', $profile?->address) }}</textarea>
                            @error('address') <p class="pf-error">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="pf-submit">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Save Student Details
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- ── Right: account overview + password ── --}}
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
                        <span class="pf-stat-value">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5Z"/></svg>
                            Linked Student
                        </span>
                        <span class="pf-stat-value">{{ $student?->name ?? '—' }}</span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            Class
                        </span>
                        <span class="pf-stat-value">{{ $student?->schoolClass?->name ?? '—' }}</span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Admission No.
                        </span>
                        <span class="pf-stat-value">{{ $student?->admission_number ?? '—' }}</span>
                    </div>
                </div>
                @if($student)
                    <div style="padding:14px 22px 20px;display:flex;gap:8px;flex-wrap:wrap">
                        <a href="{{ route('parent.homework.index') }}" class="ui-tag ui-tag-orange" style="text-decoration:none">View Homework</a>
                        <a href="{{ route('parent.results.index') }}" class="ui-tag ui-tag-indigo" style="text-decoration:none">View Results</a>
                        <a href="{{ route('parent.messages.index') }}" class="ui-tag ui-tag-teal" style="text-decoration:none">Messages</a>
                    </div>
                @endif
            </div>

            <div class="pf-card">
                <div class="pf-card-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4.75" y="10.75" width="14.5" height="9" rx="1.75"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>
                    <span class="pf-card-title">Change Password</span>
                </div>
                <div class="pf-card-body">
                    <form method="POST" action="{{ route('parent.profile.password') }}">
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
