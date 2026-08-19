@extends('layouts.admin')
@section('title', 'Message read status')

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
    .mt-title   { font-size: 22px; font-weight: 800; color: #111827; margin: 8px 0 0; }
    .mt-sub     { font-size: 13px; color: #6b7280; margin: 4px 0 24px; }
    .mt-sub span { color: var(--accent); font-weight: 600; }

    /* ── Stats card ── */
    .stats-strip {
        display: flex; gap: 16px; flex-wrap: wrap;
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
    .stat-icon.amber  { background: #fef3c7; color: #b45309; }
    .stat-value { font-size: 20px; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

    /* ── Message body card ── */
    .mt-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        margin-bottom: 24px;
    }
    .msg-body { padding: 22px 24px; font-size: 13px; line-height: 1.7; color: #374151; white-space: pre-line; }

    /* ── Search / filter bar ── */
    .mt-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .mt-filter-inner { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .search-wrap { position: relative; flex: 1; min-width: 200px; }
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
    .status-select {
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        font-size: 13px; color: #374151;
        background: #fff;
        min-width: 150px;
    }
    .status-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    .btn-reset-sm {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 16px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-reset-sm:hover { background: #f9fafb; }

    /* ── Table ── */
    .mt-table-wrap table { width: 100%; border-collapse: collapse; min-width: 620px; table-layout: fixed; }
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
    .mt-table-wrap tbody tr.is-hidden { display: none; }
    .mt-table-wrap tbody td {
        padding: 14px 18px;
        font-size: 13px; color: #374151;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Recipient cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
        flex-shrink: 0; text-transform: uppercase;
    }
    .user-avatar.teacher { background: #e0e7ff; color: #4338ca; }
    .user-avatar.student { background: #ffedd5; color: #c2410c; }
    .user-name  { font-weight: 700; color: #111827; font-size: 13px; }
    .user-email { font-size: 11px; color: #9ca3af; margin-top: 1px; }

    /* Badges */
    .class-badge {
        display: inline-flex; align-items: center;
        background: #f3f4f6; color: #374151;
        border-radius: 6px; padding: 3px 10px;
        font-size: 12px; font-weight: 600;
    }
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

    /* Empty state */
    .mt-empty { padding: 56px 24px; text-align: center; color: #9ca3af; }
    .mt-empty svg { margin: 0 auto 14px; opacity: .3; }
    .mt-empty p   { font-size: 14px; }
</style>
@endpush

@section('content')
@php
    $totalRecipients = $message->recipients->count();
    $readRecipients = $message->recipients->whereNotNull('read_at')->count();
    $unreadRecipients = $totalRecipients - $readRecipients;
    $isTeacherMsg = $message->audience === 'teacher';
@endphp

<div class="max-w-6xl">
    <a href="{{ route('admin.messages.index') }}" class="ui-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to messages
    </a>

    {{-- ── Page heading ── --}}
    <div class="mt-header" style="margin-top:16px">
        <div>
            <span class="ui-tag {{ $isTeacherMsg ? 'ui-tag-indigo' : 'ui-tag-orange' }}">{{ $isTeacherMsg ? 'Teacher message' : 'Student message' }}</span>
            <h1 class="mt-title">{{ $message->subject }}</h1>
            <p class="mt-sub">
                Sent {{ $message->created_at->format('d M Y, h:i A') }} by {{ $message->sender->name ?? 'Admin' }}
                <span>{{ $message->target_class_ids ? 'Selected classes' : 'All classes' }}</span>
            </p>
        </div>
    </div>

    {{-- ── Stats cards ── --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $totalRecipients }}</div>
                <div class="stat-label">Total Recipients</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $readRecipients }}</div>
                <div class="stat-label">Read</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $unreadRecipients }}</div>
                <div class="stat-label">Not Read</div>
            </div>
        </div>
    </div>

    {{-- ── Message body ── --}}
    <div class="mt-table-wrap">
        <div class="msg-body">{{ $message->body }}</div>
    </div>

    {{-- ── Search / filter bar ── --}}
    <div class="mt-filter-bar">
        <div class="mt-filter-inner">
            <div class="search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="recipient-search" placeholder="Filter by name or email…">
            </div>
            <select id="recipient-status-filter" class="status-select">
                <option value="">All statuses</option>
                <option value="read">Read</option>
                <option value="unread">Not read</option>
            </select>
            <button type="button" id="recipient-reset-btn" class="btn-reset-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5"/>
                </svg>
                Reset
            </button>
        </div>
    </div>

    {{-- ── Recipient table ── --}}
    <div class="mt-table-wrap">
        <table id="recipients-table">
            <colgroup>
                <col style="width:50%"><col style="width:24%"><col style="width:26%">
            </colgroup>
            <thead>
                <tr>
                    <th>Recipient</th>
                    <th>Class</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($message->recipients->sortBy('user.name') as $recipient)
                    @php
                        $classes = $recipient->user->isTeacher()
                            ? collect([$recipient->user->schoolClass])->filter()
                            : $recipient->user->students->pluck('schoolClass')->filter();
                        $initials = collect(explode(' ', $recipient->user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                        $isRead = (bool) $recipient->read_at;
                    @endphp
                    <tr id="recipient-row-{{ $recipient->id }}"
                        data-search="{{ mb_strtolower($recipient->user->name.' '.$recipient->user->email) }}"
                        data-status="{{ $isRead ? 'read' : 'unread' }}">
                        <td>
                            <div class="user-cell">
                                <span class="user-avatar {{ $isTeacherMsg ? 'teacher' : 'student' }}">{{ strtoupper($initials) }}</span>
                                <div class="min-w-0">
                                    <div class="user-name">{{ $recipient->user->name }}</div>
                                    <div class="user-email">{{ $recipient->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($classes->isNotEmpty())
                                <span class="class-badge">{{ $classes->pluck('name')->unique()->join(', ') }}</span>
                            @else
                                <span style="color:#d1d5db">—</span>
                            @endif
                        </td>
                        <td>
                            @if($isRead)
                                <span class="status-badge active"><span class="status-dot"></span>Read {{ $recipient->read_at->format('d M, h:i A') }}</span>
                            @else
                                <span class="status-badge pending"><span class="status-dot"></span>Not read</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="mt-empty"><p>This message has no recipients.</p></div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="recipients-empty" class="mt-empty" style="display:none">
            <p>No recipients match your filters.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const search = document.getElementById('recipient-search');
        const statusFilter = document.getElementById('recipient-status-filter');
        const resetBtn = document.getElementById('recipient-reset-btn');
        const rows = Array.from(document.querySelectorAll('#recipients-table tbody tr[data-search]'));
        const emptyState = document.getElementById('recipients-empty');

        function applyFilters() {
            const q = search.value.trim().toLowerCase();
            const status = statusFilter.value;
            let visible = 0;

            rows.forEach((row) => {
                const matchesSearch = row.dataset.search.includes(q);
                const matchesStatus = !status || row.dataset.status === status;
                const show = matchesSearch && matchesStatus;
                row.classList.toggle('is-hidden', !show);
                if (show) visible++;
            });

            emptyState.style.display = (rows.length > 0 && visible === 0) ? 'block' : 'none';
        }

        search.addEventListener('input', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        resetBtn.addEventListener('click', () => {
            search.value = '';
            statusFilter.value = '';
            applyFilters();
        });
    })();
</script>
@endpush
