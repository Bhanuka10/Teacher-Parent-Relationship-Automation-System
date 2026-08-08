@extends('layouts.admin')
@section('title', 'Classes')

@push('styles')
<style>
    /* ── tokens ── */
    :root {
        --accent: #7c3aed;
        --accent-light: #ede9fe;
        --accent-mid: #4f46e5;
    }

    /* ── page header ── */
    .cl-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 6px; }
    .cl-title  { font-size: 22px; font-weight: 800; color: #111827; margin: 0; }
    .cl-sub    { font-size: 13px; color: #6b7280; margin: 4px 0 24px; }
    .cl-sub span { color: var(--accent); font-weight: 600; }

    /* shared new btn */
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

    /* ── stats strip ── */
    .stats-strip { display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
        padding: 14px 20px; box-shadow: 0 1px 3px rgba(0,0,0,.04);
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .stat-icon.purple { background: var(--accent-light); color: var(--accent); }
    .stat-icon.teal   { background: #ccfbf1; color: #0f766e; }
    .stat-icon.amber  { background: #fef3c7; color: #b45309; }
    .stat-value { font-size: 20px; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

    /* ── search bar ── */
    .cl-filter {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 14px; padding: 16px 20px;
        margin-bottom: 28px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .cl-filter-inner { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .cl-search-wrap { position: relative; flex: 1; min-width: 200px; }
    .cl-search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; pointer-events: none;
    }
    .cl-search-wrap input {
        width: 100%; padding: 10px 14px 10px 38px;
        border: 1px solid #d1d5db; border-radius: 9px;
        font-size: 13px; color: #374151;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .cl-search-wrap input:focus {
        outline: none; border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    .cl-search-wrap input::placeholder { color: #9ca3af; }
    .btn-search {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: var(--accent-mid); color: #fff;
        border: none; cursor: pointer; transition: background .15s; white-space: nowrap;
    }
    .btn-search:hover { background: #4338ca; }
    .btn-reset-sm {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 16px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb; text-decoration: none;
        transition: background .15s; white-space: nowrap;
    }
    .btn-reset-sm:hover { background: #f9fafb; }

    /* ── card grid ── */
    .class-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .class-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        transition: transform .18s, box-shadow .18s;
        display: flex; flex-direction: column;
    }
    .class-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(0,0,0,.1);
    }

    /* coloured top strip */
    .card-stripe {
        height: 5px;
        border-radius: 16px 16px 0 0;
    }

    .card-body { padding: 20px 20px 16px; flex: 1; }

    /* class name badge */
    .card-class-row { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .card-class-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 800; color: #fff;
        flex-shrink: 0;
    }
    .card-class-name  { font-size: 18px; font-weight: 800; color: #111827; }
    .card-class-label { font-size: 11px; color: #9ca3af; font-weight: 500; }

    /* meta rows */
    .card-meta { display: flex; flex-direction: column; gap: 8px; }
    .card-meta-row { display: flex; align-items: center; gap: 8px; }
    .card-meta-icon {
        width: 26px; height: 26px; border-radius: 7px;
        background: #f3f4f6; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; color: #9ca3af;
    }
    .card-meta-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; }
    .card-meta-val   { font-size: 13px; font-weight: 600; color: #374151; }
    .teacher-avatar {
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; color: #fff;
        vertical-align: middle; margin-right: 5px;
    }
    .no-teacher { color: #d1d5db; font-style: italic; font-size: 13px; }

    /* student count pill */
    .student-pill {
        display: inline-flex; align-items: center; gap: 4px;
        background: #ede9fe; color: var(--accent);
        border-radius: 20px; padding: 3px 10px;
        font-size: 12px; font-weight: 700;
    }

    /* card footer */
    .card-footer {
        border-top: 1px solid #f3f4f6;
        padding: 12px 20px;
        display: flex; align-items: center; gap: 8px;
        flex-wrap: wrap;
    }
    .btn-open {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 16px; border-radius: 8px;
        font-size: 12px; font-weight: 700;
        background: var(--accent-mid); color: #fff;
        text-decoration: none;
        transition: background .15s;
        flex: 1; justify-content: center;
    }
    .btn-open:hover { background: #4338ca; }
    .btn-rm-teacher {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 600;
        background: #fef3c7; color: #92400e;
        border: none; cursor: pointer;
        transition: background .12s;
    }
    .btn-rm-teacher:hover { background: #fde68a; }
    .btn-reset {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 600;
        background: #fee2e2; color: #991b1b;
        border: none; cursor: pointer;
        transition: background .12s;
    }
    .btn-reset:hover { background: #fecaca; }

    /* empty state */
    .cl-empty {
        grid-column: 1 / -1;
        padding: 64px 24px; text-align: center; color: #9ca3af;
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 16px;
    }
    .cl-empty svg { margin: 0 auto 14px; opacity: .3; }
    .cl-empty p   { font-size: 14px; }
</style>
@endpush

@section('content')
@php
    $totalClasses   = $classes->count();
    $assignedCount  = $classes->whereNotNull('teacher_id')->count();
    $totalStudents  = $classes->sum('students_count');

    /* palette for card accents — cycle by class name char */
    $palette = [
        ['stripe'=>'#6366f1','icon'=>'#6366f1'],
        ['stripe'=>'#8b5cf6','icon'=>'#8b5cf6'],
        ['stripe'=>'#0ea5e9','icon'=>'#0ea5e9'],
        ['stripe'=>'#10b981','icon'=>'#10b981'],
        ['stripe'=>'#f59e0b','icon'=>'#f59e0b'],
        ['stripe'=>'#ec4899','icon'=>'#ec4899'],
        ['stripe'=>'#14b8a6','icon'=>'#14b8a6'],
        ['stripe'=>'#ef4444','icon'=>'#ef4444'],
    ];
    $teacherColours = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#0ea5e9','#ef4444','#14b8a6'];
@endphp

<div class="max-w-7xl">

    {{-- heading --}}
    <div class="cl-header">
        <div>
            <h1 class="cl-title">Manage Classes</h1>
            <p class="cl-sub">Overview of all <span>school classes</span>, assigned teachers and enrolled students.</p>
        </div>
    </div>

    {{-- stats --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="total-classes-count">{{ $totalClasses }}</div>
                <div class="stat-label">Total Classes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="assigned-teachers-count">{{ $assignedCount }}</div>
                <div class="stat-label">Classes with Teacher</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="total-students-count">{{ $totalStudents }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
    </div>

    {{-- search --}}
    <div class="cl-filter">
        <form method="GET" action="{{ route('admin.classes.index') }}" id="class-search-form">
            <div class="cl-filter-inner">
                <div class="cl-search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" id="class-search-input"
                           value="{{ $search }}"
                           placeholder="Search by class name (e.g. 5-B, 12-E)…">
                </div>
                <button type="submit" class="btn-search" id="class-search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('admin.classes.index') }}" class="btn-reset-sm" id="class-reset-btn">
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

    {{-- card grid --}}
    <div class="class-grid">
        @forelse($classes as $index => $class)
            @php
                $p           = $palette[$index % count($palette)];
                $stripe      = $p['stripe'];
                $iconBg      = $p['icon'];
                $teacherName = $class->teacher?->name;
                $tColor      = $teacherName
                    ? $teacherColours[ord(strtolower($teacherName[0])) % count($teacherColours)]
                    : null;
            @endphp
            <div class="class-card" id="class-card-{{ $class->id }}">
                <div class="card-stripe" style="background:{{ $stripe }}"></div>

                <div class="card-body">
                    {{-- class name --}}
                    <div class="card-class-row">
                        <div class="card-class-icon" style="background:{{ $iconBg }}">
                            {{ $class->name }}
                        </div>
                        <div>
                            <div class="card-class-name">Class {{ $class->name }}</div>
                            <div class="card-class-label">School Class</div>
                        </div>
                    </div>

                    {{-- meta --}}
                    <div class="card-meta">
                        {{-- teacher --}}
                        <div class="card-meta-row">
                            <div class="card-meta-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="card-meta-label">Teacher</div>
                                @if($teacherName)
                                    <div class="card-meta-val">
                                        <span class="teacher-avatar" style="background:{{ $tColor }}">
                                            {{ strtoupper(substr($teacherName, 0, 1)) }}
                                        </span>
                                        {{ $teacherName }}
                                    </div>
                                @else
                                    <div class="no-teacher">Not assigned</div>
                                @endif
                            </div>
                        </div>

                        {{-- students --}}
                        <div class="card-meta-row">
                            <div class="card-meta-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <div>
                                <div class="card-meta-label">Students</div>
                                <div class="card-meta-val">
                                    <span class="student-pill">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        {{ $class->students_count }} enrolled
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- footer actions --}}
                <div class="card-footer">
                    <a href="{{ route('admin.classes.show', $class) }}"
                       class="btn-open" id="open-class-{{ $class->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        Open Class
                    </a>

                    <form method="POST" action="{{ route('admin.classes.remove-teacher', $class) }}"
                          onsubmit="return confirm('Remove teacher from this class?')" style="margin:0">
                        @csrf @method('PATCH')
                        <button class="btn-rm-teacher" type="submit" id="rm-teacher-{{ $class->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <line x1="23" y1="11" x2="17" y2="11"/>
                            </svg>
                            Remove Teacher
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.classes.reset', $class) }}"
                          onsubmit="return confirm('Reset this class? Teacher and all students will be unlinked.')" style="margin:0">
                        @csrf @method('PATCH')
                        <button class="btn-reset" type="submit" id="reset-class-{{ $class->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 .49-5"/>
                            </svg>
                            Reset
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="cl-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                <p>No classes found{{ $search ? ' matching "'.$search.'"' : '' }}.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
