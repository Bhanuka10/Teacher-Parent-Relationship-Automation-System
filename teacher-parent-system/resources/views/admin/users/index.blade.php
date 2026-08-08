@extends('layouts.admin')
@section('title', $role === 'parent' ? 'Manage Students' : 'Manage Teachers')

@push('styles')
<style>
    /* ── Shared tokens ── */
    :root {
        --accent: #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid: #4f46e5;
    }

    /* ── Page header row ── */
    .mt-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
    .mt-title   { font-size: 22px; font-weight: 800; color: #111827; margin: 0; }
    .mt-sub     { font-size: 13px; color: #6b7280; margin: 4px 0 24px; }
    .mt-sub span { color: var(--accent); font-weight: 600; cursor: pointer; }

    .btn-new {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--accent-mid); color: #fff;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 700;
        text-decoration: none; white-space: nowrap;
        box-shadow: 0 4px 14px rgba(79,70,229,.28);
        transition: background .15s, box-shadow .15s;
    }
    .btn-new:hover { background: #4338ca; box-shadow: 0 6px 18px rgba(79,70,229,.38); }
    .btn-new svg   { flex-shrink: 0; }

    /* ── Stats card ── */
    .stats-strip {
        display: flex; gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.purple { background: var(--accent-light); color: var(--accent); }
    .stat-icon.teal   { background: #ccfbf1; color: #0f766e; }
    .stat-value { font-size: 20px; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

    /* ── Search / filter bar ── */
    .mt-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .mt-filter-inner {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; pointer-events: none;
    }
    .search-wrap input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        font-size: 13px; color: #374151;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .search-wrap input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    .search-wrap input::placeholder { color: #9ca3af; }
    .btn-search {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: var(--accent-mid); color: #fff;
        border: none; cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-search:hover { background: #4338ca; }
    .btn-reset-sm {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 16px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-reset-sm:hover { background: #f9fafb; }

    /* ── Table ── */
    .mt-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        overflow-x: auto;
    }
    .mt-table-wrap table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .mt-table-wrap thead th {
        padding: 12px 18px;
        font-size: 11px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #9ca3af;
        background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
        text-align: left;
    }
    .mt-table-wrap tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .12s;
    }
    .mt-table-wrap tbody tr:last-child { border-bottom: none; }
    .mt-table-wrap tbody tr:hover { background: #fafafa; }
    .mt-table-wrap tbody td {
        padding: 14px 18px;
        font-size: 13px; color: #374151;
        vertical-align: middle;
        white-space: nowrap;
    }

    /* User name cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
        flex-shrink: 0; text-transform: uppercase;
    }
    .user-name  { font-weight: 700; color: #111827; font-size: 13px; }
    .user-email { font-size: 11px; color: #9ca3af; margin-top: 1px; }

    /* Badges */
    .class-badge {
        display: inline-flex; align-items: center;
        background: #f3f4f6; color: #374151;
        border-radius: 6px; padding: 3px 10px;
        font-size: 12px; font-weight: 600;
    }
    .role-badge {
        display: inline-flex; align-items: center; gap: 4px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
    }
    .role-badge.teacher { background: #ccfbf1; color: #0f766e; }
    .role-badge.parent  { background: #fef3c7; color: #92400e; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 3px 10px;
        font-size: 11px; font-weight: 700;
    }
    .status-badge.active  { background: #d1fae5; color: #065f46; }
    .status-badge.pending { background: #fef9c3; color: #854d0e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-badge.active .status-dot  { background: #10b981; }
    .status-badge.pending .status-dot { background: #eab308; }

    /* Action buttons */
    .act-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 7px;
        font-size: 12px; font-weight: 600;
        text-decoration: none;
        transition: background .12s;
        border: none; cursor: pointer;
    }
    .act-btn.edit   { background: #ede9fe; color: #6d28d9; }
    .act-btn.edit:hover { background: #ddd6fe; }
    .act-btn.delete { background: #fee2e2; color: #991b1b; }
    .act-btn.delete:hover { background: #fecaca; }

    /* Empty state */
    .mt-empty { padding: 56px 24px; text-align: center; color: #9ca3af; }
    .mt-empty svg { margin: 0 auto 14px; opacity: .3; }
    .mt-empty p   { font-size: 14px; }
    .mt-empty a   { color: var(--accent); font-weight: 600; text-decoration: underline; margin-top: 8px; display: inline-block; }

    /* Info cell (truncate) */
    .info-cell { max-width: 180px; overflow: hidden; text-overflow: ellipsis; color: #6b7280; }
</style>
@endpush

@section('content')
@php
    $isTeacher  = $role !== 'parent';
    $label      = $isTeacher ? 'Teacher' : 'Student';
    $labelPlural = $isTeacher ? 'Teachers' : 'Students';
    $totalCount = $users->count();
    $activeCount = $users->where('must_change_password', false)->count();
    $colours = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
@endphp

<div class="max-w-7xl">

    {{-- ── Page heading ── --}}
    <div class="mt-header">
        <div>
            <h1 class="mt-title">Manage {{ $labelPlural }}</h1>
            <p class="mt-sub">
                View, search and manage all <span>{{ strtolower($labelPlural) }}</span> registered in the system.
            </p>
        </div>
        <a href="{{ route('admin.users.create', ['role' => $isTeacher ? 'teacher' : 'parent']) }}"
           class="btn-new" id="create-{{ strtolower($label) }}-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            New {{ $label }}
        </a>
    </div>

    {{-- ── Stats cards ── --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon {{ $isTeacher ? 'purple' : 'teal' }}">
                @if($isTeacher)
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                @endif
            </div>
            <div>
                <div class="stat-value" id="total-count">{{ $totalCount }}</div>
                <div class="stat-label">Total {{ $labelPlural }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="active-count">{{ $activeCount }}</div>
                <div class="stat-label">Active Accounts</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="pending-count">{{ $totalCount - $activeCount }}</div>
                <div class="stat-label">Pending Password Change</div>
            </div>
        </div>
    </div>

    {{-- ── Search bar ── --}}
    <div class="mt-filter-bar">
        <form method="GET" action="{{ route('admin.users.index') }}" id="search-form">
            <input type="hidden" name="role" value="{{ $isTeacher ? 'teacher' : 'parent' }}">
            <div class="mt-filter-inner">
                <div class="search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" id="search-input"
                           value="{{ $search }}"
                           list="user-search-options"
                           placeholder="Search by name, email{{ $isTeacher ? ', or class' : ', class, or admission no.' }}…">
                    <datalist id="user-search-options">
                        @foreach($searchOptions as $option)
                            <option value="{{ $option }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <button type="submit" class="btn-search" id="search-submit-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('admin.users.index', ['role' => $isTeacher ? 'teacher' : 'parent']) }}"
                   class="btn-reset-sm" id="reset-search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <polyline points="1 4 1 10 7 10"/>
                        <path d="M3.51 15a9 9 0 1 0 .49-5"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ── Table ── --}}
    <div class="mt-table-wrap">
        <table id="users-table">
            <thead>
                <tr>
                    <th>{{ $label }}</th>
                    <th>Class</th>
                    @if($isTeacher)
                        <th>Phone</th>
                        <th>Alt. Email</th>
                        <th>Address</th>
                    @else
                        <th>Admission No.</th>
                        <th>Index No.</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                    @endif
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $teacherProfile  = $user->role === 'teacher' ? $user->teacherProfile : null;
                    $studentRecord   = $user->role === 'parent'  ? $user->students->first() : null;
                    $studentProfile  = $studentRecord?->profile;
                    $admissionNumber = $studentRecord?->admission_number ?? '-';
                    $className       = $user->role === 'teacher'
                        ? ($user->schoolClass?->name ?? '—')
                        : ($studentRecord?->schoolClass?->name ?? '—');
                    $avatarColor = $colours[ord(strtolower($user->name[0] ?? 'a')) % count($colours)];
                    $isActive    = !$user->must_change_password;
                @endphp
                <tr id="user-row-{{ $user->id }}">
                    {{-- Name + email --}}
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar" style="background:{{ $avatarColor }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Class --}}
                    <td>
                        @if($className !== '—')
                            <span class="class-badge">{{ $className }}</span>
                        @else
                            <span style="color:#d1d5db">—</span>
                        @endif
                    </td>

                    @if($isTeacher)
                        <td class="info-cell">{{ $teacherProfile?->phone_number ?? '—' }}</td>
                        <td class="info-cell">{{ $teacherProfile?->email_address ?? '—' }}</td>
                        <td class="info-cell" title="{{ $teacherProfile?->address }}">{{ $teacherProfile?->address ?? '—' }}</td>
                    @else
                        <td>{{ $admissionNumber }}</td>
                        <td>{{ $studentProfile?->index_number ?? '—' }}</td>
                        <td>{{ optional($studentProfile?->date_of_birth)?->format('M j, Y') ?? '—' }}</td>
                        <td>{{ $studentProfile?->gender ? ucfirst($studentProfile->gender) : '—' }}</td>
                    @endif

                    {{-- Role --}}
                    <td>
                        <span class="role-badge {{ $user->role === 'teacher' ? 'teacher' : 'parent' }}">
                            {{ $user->role === 'parent' ? 'Student' : 'Teacher' }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="status-badge {{ $isActive ? 'active' : 'pending' }}">
                            <span class="status-dot"></span>
                            {{ $isActive ? 'Active' : 'Pending' }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="act-btn edit" id="edit-user-{{ $user->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')" style="margin:0">
                                @csrf @method('DELETE')
                                <button class="act-btn delete" type="submit" id="delete-user-{{ $user->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isTeacher ? '8' : '9' }}">
                        <div class="mt-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <p>No {{ strtolower($labelPlural) }} found{{ $search ? ' matching "'.$search.'"' : '' }}.</p>
                            <a href="{{ route('admin.users.create', ['role' => $isTeacher ? 'teacher' : 'parent']) }}">+ Add a {{ $label }}</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer row --}}
        @if($users->isNotEmpty())
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;
                    border-top:1px solid #f3f4f6;font-size:12px;color:#9ca3af;">
            <span>Showing {{ $users->count() }} {{ strtolower($labelPlural) }}{{ $search ? ' matching &ldquo;'.$search.'&rdquo;' : '' }}</span>
            <a href="{{ route('admin.users.create', ['role' => $isTeacher ? 'teacher' : 'parent']) }}"
               class="btn-new" style="padding:7px 14px;font-size:12px;box-shadow:none;" id="footer-create-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                New {{ $label }}
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
