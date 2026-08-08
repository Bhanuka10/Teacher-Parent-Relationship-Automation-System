@extends('layouts.admin')
@section('title', 'Class '.$school_class->name)

@push('styles')
<style>
    :root {
        --accent: #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid: #4f46e5;
    }

    /* back link */
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--accent);
        text-decoration: none; margin-bottom: 22px;
        transition: opacity .15s;
    }
    .back-link:hover { opacity: .75; }

    /* hero card */
    .detail-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        margin-bottom: 28px;
    }
    .detail-stripe { height: 6px; }
    .detail-body   { padding: 24px 28px; }

    .detail-top {
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 20px; flex-wrap: wrap;
    }
    .detail-left  { display: flex; align-items: center; gap: 16px; }
    .detail-icon  {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 900; color: #fff; flex-shrink: 0;
    }
    .detail-class-name  { font-size: 24px; font-weight: 800; color: #111827; }
    .detail-class-sub   { font-size: 13px; color: #9ca3af; margin-top: 2px; }

    /* meta pills row */
    .detail-meta {
        display: flex; align-items: center; gap: 12px;
        margin-top: 20px; flex-wrap: wrap;
    }
    .meta-pill {
        display: inline-flex; align-items: center; gap: 7px;
        background: #f9fafb; border: 1px solid #f3f4f6;
        border-radius: 10px; padding: 8px 14px;
    }
    .meta-pill-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; }
    .meta-pill-val   { font-size: 13px; font-weight: 700; color: #374151; }
    .teacher-chip {
        display: inline-flex; align-items: center; gap: 7px;
    }
    .teacher-av {
        width: 26px; height: 26px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .student-count-pill {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--accent-light); color: var(--accent);
        border-radius: 20px; padding: 4px 12px;
        font-size: 13px; font-weight: 700;
    }

    /* danger actions */
    .detail-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-start; }
    .btn-danger-amber {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 16px; border-radius: 9px;
        font-size: 12px; font-weight: 700;
        background: #fef3c7; color: #92400e;
        border: none; cursor: pointer;
        transition: background .12s;
    }
    .btn-danger-amber:hover { background: #fde68a; }
    .btn-danger-red {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 16px; border-radius: 9px;
        font-size: 12px; font-weight: 700;
        background: #fee2e2; color: #991b1b;
        border: none; cursor: pointer;
        transition: background .12s;
    }
    .btn-danger-red:hover { background: #fecaca; }

    /* student roster table */
    .roster-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    .roster-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 22px;
        border-bottom: 1px solid #f3f4f6;
    }
    .roster-title {
        font-size: 15px; font-weight: 800; color: #111827;
        display: flex; align-items: center; gap: 8px;
    }
    .roster-count {
        background: var(--accent-light); color: var(--accent);
        border-radius: 20px; padding: 2px 10px;
        font-size: 12px; font-weight: 700;
    }
    .roster-wrap table { width: 100%; border-collapse: collapse; }
    .roster-wrap thead th {
        padding: 11px 20px;
        font-size: 10px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #9ca3af; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
        text-align: left;
    }
    .roster-wrap tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .12s;
    }
    .roster-wrap tbody tr:last-child { border-bottom: none; }
    .roster-wrap tbody tr:hover { background: #fafafa; }
    .roster-wrap tbody td {
        padding: 13px 20px;
        font-size: 13px; color: #374151;
        vertical-align: middle;
    }
    .student-cell { display: flex; align-items: center; gap: 10px; }
    .student-av {
        width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .student-name { font-weight: 600; color: #111827; }
    .adm-no { color: #6b7280; font-size: 12px; font-family: monospace; }
    .roster-empty {
        padding: 52px 24px; text-align: center; color: #9ca3af;
    }
    .roster-empty svg { margin: 0 auto 12px; opacity: .3; }
    .roster-empty p { font-size: 14px; }
</style>
@endpush

@section('content')
@php
    $colours  = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
    $palettes = [
        '#6366f1','#8b5cf6','#0ea5e9','#10b981','#f59e0b','#ec4899','#14b8a6','#ef4444',
    ];
    $accentIdx   = ord($school_class->name[0] ?? 'A') % count($palettes);
    $accent      = $palettes[$accentIdx];
    $teacherName = $school_class->teacher?->name;
    $tColor      = $teacherName ? $colours[ord(strtolower($teacherName[0])) % count($colours)] : null;
@endphp

<div class="max-w-5xl">

    {{-- back link --}}
    <a href="{{ route('admin.classes.index') }}" class="back-link" id="back-to-classes">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        Back to Classes
    </a>

    {{-- hero detail card --}}
    <div class="detail-card">
        <div class="detail-stripe" style="background:{{ $accent }}"></div>
        <div class="detail-body">
            <div class="detail-top">
                <div class="detail-left">
                    <div class="detail-icon" style="background:{{ $accent }}">
                        {{ $school_class->name }}
                    </div>
                    <div>
                        <div class="detail-class-name">Class {{ $school_class->name }}</div>
                        <div class="detail-class-sub">School Class &mdash; Detail View</div>
                    </div>
                </div>

                <div class="detail-actions">
                    <form method="POST" action="{{ route('admin.classes.remove-teacher', $school_class) }}"
                          onsubmit="return confirm('Remove teacher from this class?')" style="margin:0">
                        @csrf @method('PATCH')
                        <button class="btn-danger-amber" type="submit" id="remove-teacher-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <line x1="23" y1="11" x2="17" y2="11"/>
                            </svg>
                            Remove Teacher
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.classes.reset', $school_class) }}"
                          onsubmit="return confirm('Reset this class? Teacher and all students will be unlinked.')" style="margin:0">
                        @csrf @method('PATCH')
                        <button class="btn-danger-red" type="submit" id="reset-class-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 .49-5"/>
                            </svg>
                            Reset Class
                        </button>
                    </form>
                </div>
            </div>

            {{-- meta pills --}}
            <div class="detail-meta">
                <div class="meta-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <div>
                        <div class="meta-pill-label">Teacher</div>
                        @if($teacherName)
                            <div class="teacher-chip">
                                <span class="teacher-av" style="background:{{ $tColor }}">
                                    {{ strtoupper(substr($teacherName, 0, 1)) }}
                                </span>
                                <span class="meta-pill-val">{{ $teacherName }}</span>
                            </div>
                        @else
                            <span style="font-size:13px;color:#d1d5db;font-style:italic">Not assigned</span>
                        @endif
                    </div>
                </div>

                <div class="meta-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <div>
                        <div class="meta-pill-label">Enrolled Students</div>
                        <div>
                            <span class="student-count-pill">
                                {{ $school_class->students->count() }} students
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- student roster --}}
    <div class="roster-wrap">
        <div class="roster-header">
            <div class="roster-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                     viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Student Roster
                <span class="roster-count" id="roster-count">{{ $school_class->students->count() }}</span>
            </div>
        </div>

        <table id="roster-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Admission No.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($school_class->students as $student)
                    @php
                        $sColor = $colours[ord(strtolower($student->name[0] ?? 'a')) % count($colours)];
                    @endphp
                    <tr id="student-row-{{ $student->id }}">
                        <td>
                            <div class="student-cell">
                                <div class="student-av" style="background:{{ $sColor }}">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <span class="student-name">{{ $student->name }}</span>
                            </div>
                        </td>
                        <td><span class="adm-no">{{ $student->admission_number }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">
                            <div class="roster-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <p>No students assigned to this class yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
