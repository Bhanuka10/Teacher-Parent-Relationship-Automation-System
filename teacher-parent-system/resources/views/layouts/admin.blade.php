<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="app-sidebar bg-indigo-900 text-white">

        {{-- Header --}}
        <div class="app-sidebar-header">
            <div class="app-sidebar-icon bg-indigo-700">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21V8.5L12 3l8 5.5V21M9 21v-6h6v6M8 10h.01M16 10h.01" />
                </svg>
            </div>
            <div class="app-sidebar-text">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-indigo-300 mb-0.5">Admin Panel</p>
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" title="Dashboard"
               class="app-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h6.5v8h-6.5v-8ZM13.75 4h6.5v16h-6.5V4ZM3.75 4h6.5v5h-6.5V4Z" />
                </svg>
                <span class="app-sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" title="Teachers"
               class="app-sidebar-link {{ request()->routeIs('admin.users.*') && request('role') !== 'parent' ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11ZM18.5 6.75h2.75M19.88 5.38v2.75" />
                </svg>
                <span class="app-sidebar-text">Teachers</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'parent']) }}" title="Students"
               class="app-sidebar-link {{ request()->routeIs('admin.users.*') && request('role') === 'parent' ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5ZM4.75 20.25h14.5" />
                </svg>
                <span class="app-sidebar-text">Students</span>
            </a>

            <a href="{{ route('admin.classes.index') }}" title="Classes"
               class="app-sidebar-link {{ request()->routeIs('admin.classes.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 5.5h14.5v13H4.75v-13ZM8 9h8M8 12h5M8 15h7" />
                </svg>
                <span class="app-sidebar-text">Classes</span>
            </a>

            <a href="{{ route('admin.attendance.history') }}" title="Attendance"
               class="app-sidebar-link {{ request()->routeIs('admin.attendance.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.75 4.75v3M16.25 4.75v3M5.25 8.25h13.5M6 6.25h12a1.5 1.5 0 0 1 1.5 1.5V18A1.5 1.5 0 0 1 18 19.5H6A1.5 1.5 0 0 1 4.5 18V7.75a1.5 1.5 0 0 1 1.5-1.5ZM8 14l2 2 5-5" />
                </svg>
                <span class="app-sidebar-text">Attendance</span>
            </a>

            <a href="{{ route('admin.leaves.index') }}" title="Leave Requests"
               class="app-sidebar-link {{ request()->routeIs('admin.leaves.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                </svg>
                <span class="app-sidebar-text">Leave Requests</span>
            </a>

            @php($pendingStaffLeaveCount = \App\Models\TeacherLeaveRequest::where('status', 'pending')->count())
            <a href="{{ route('admin.staff-leaves.index') }}" title="Staff Leave{{ $pendingStaffLeaveCount ? ' ('.$pendingStaffLeaveCount.' pending)' : '' }}"
               class="app-sidebar-link {{ request()->routeIs('admin.staff-leaves.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25M17.25 6.75l2 2 3.5-3.5" />
                </svg>
                <span class="app-sidebar-text">Staff Leave @if($pendingStaffLeaveCount)<span class="ml-1 rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] text-white">{{ $pendingStaffLeaveCount }}</span>@endif</span>
            </a>

            <a href="{{ route('admin.homework.index') }}" title="Homework"
               class="app-sidebar-link {{ request()->routeIs('admin.homework.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="app-sidebar-text">Homework</span>
            </a>

            <a href="{{ route('admin.exams.index') }}" title="Exams"
               class="app-sidebar-link {{ request()->routeIs('admin.exams.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z" />
                </svg>
                <span class="app-sidebar-text">Exams</span>
            </a>

            <a href="{{ route('admin.grading-scheme.index') }}" title="Grading Scheme"
               class="app-sidebar-link {{ request()->routeIs('admin.grading-scheme.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="app-sidebar-text">Grading Scheme</span>
            </a>

            <a href="{{ route('admin.messages.index') }}" title="Messages"
               class="app-sidebar-link {{ request()->routeIs('admin.messages.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z" />
                </svg>
                <span class="app-sidebar-text">Messages</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Log Out" class="app-sidebar-link text-indigo-300 hover:bg-indigo-800 hover:text-white">
                    <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 8.75V6.5a1.75 1.75 0 0 0-1.75-1.75H6.5A1.75 1.75 0 0 0 4.75 6.5v11A1.75 1.75 0 0 0 6.5 19.25H14a1.75 1.75 0 0 0 1.75-1.75v-2.25M11.75 12h7.5M16.75 9.5l2.5 2.5-2.5 2.5" />
                    </svg>
                    <span class="app-sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 p-8">
        @if(session('success'))
            <div class="flash-success">
                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash-error">
                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')))
            <div class="flash-warning">
                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                </svg>
                <div>
                    <p class="font-semibold mb-1">Some rows were skipped:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach(session('import_errors') as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
