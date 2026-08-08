<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teacher') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="app-sidebar bg-teal-900 text-white">

        {{-- Header --}}
        <div class="app-sidebar-header">
            <div class="app-sidebar-icon bg-teal-700">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11ZM16.5 5.75l2.75-1v4.5l-2.75-1" />
                </svg>
            </div>
            <div class="app-sidebar-text">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-teal-300 mb-0.5">Teacher Portal</p>
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <a href="{{ route('teacher.dashboard') }}"
               class="app-sidebar-link {{ request()->routeIs('teacher.dashboard') ? 'bg-teal-700 text-white' : 'text-teal-200 hover:bg-teal-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h6.5v8h-6.5v-8ZM13.75 4h6.5v16h-6.5V4ZM3.75 4h6.5v5h-6.5V4Z" />
                </svg>
                <span class="app-sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('teacher.profile') }}"
               class="app-sidebar-link {{ request()->routeIs('teacher.profile*') ? 'bg-teal-700 text-white' : 'text-teal-200 hover:bg-teal-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25" />
                </svg>
                <span class="app-sidebar-text">My Profile</span>
            </a>

            <a href="{{ route('teacher.attendance.index') }}"
               class="app-sidebar-link {{ request()->routeIs('teacher.attendance.index') ? 'bg-teal-700 text-white' : 'text-teal-200 hover:bg-teal-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z" />
                </svg>
                <span class="app-sidebar-text">Mark Attendance</span>
            </a>

            <a href="{{ route('teacher.attendance.history') }}"
               class="app-sidebar-link {{ request()->routeIs('teacher.attendance.history') ? 'bg-teal-700 text-white' : 'text-teal-200 hover:bg-teal-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.75 4.75v3M16.25 4.75v3M5.25 8.25h13.5M6 6.25h12a1.5 1.5 0 0 1 1.5 1.5V18A1.5 1.5 0 0 1 18 19.5H6A1.5 1.5 0 0 1 4.5 18V7.75a1.5 1.5 0 0 1 1.5-1.5ZM8 13.5h8M8 16h5" />
                </svg>
                <span class="app-sidebar-text">Attendance History</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="app-sidebar-link text-teal-300 hover:bg-teal-800 hover:text-white">
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

        @yield('content')
    </main>

</body>
</html>
