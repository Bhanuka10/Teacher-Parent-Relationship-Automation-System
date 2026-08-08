<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teacher') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex min-h-screen">

    <aside class="app-sidebar bg-teal-700 text-white flex flex-col min-h-screen shrink-0">
        <div class="app-sidebar-header px-4 py-5 border-b border-teal-600">
            <div class="app-sidebar-icon bg-teal-600/70">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11ZM16.5 5.75l2.75-1v4.5l-2.75-1" />
                </svg>
            </div>
            <div class="app-sidebar-text">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-200 mb-1">Teacher Portal</p>
                <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('teacher.dashboard') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('teacher.dashboard') ? 'bg-teal-500 text-white' : 'text-teal-100 hover:bg-teal-600' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h6.5v8h-6.5v-8ZM13.75 4h6.5v16h-6.5V4ZM3.75 4h6.5v5h-6.5V4Z" />
                </svg>
                <span class="app-sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('teacher.profile') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('teacher.profile*') ? 'bg-teal-500 text-white' : 'text-teal-100 hover:bg-teal-600' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25" />
                </svg>
                <span class="app-sidebar-text">My Profile</span>
            </a>
            <a href="{{ route('teacher.attendance.index') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('teacher.attendance.index') ? 'bg-teal-500 text-white' : 'text-teal-100 hover:bg-teal-600' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.75 12.5 9.5 16.25 18.25 7.75M4.75 5.75h14.5v12.5H4.75V5.75Z" />
                </svg>
                <span class="app-sidebar-text">Mark Attendance</span>
            </a>
            <a href="{{ route('teacher.attendance.history') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('teacher.attendance.history') ? 'bg-teal-500 text-white' : 'text-teal-100 hover:bg-teal-600' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.75 4.75v3M16.25 4.75v3M5.25 8.25h13.5M6 6.25h12a1.5 1.5 0 0 1 1.5 1.5V18A1.5 1.5 0 0 1 18 19.5H6A1.5 1.5 0 0 1 4.5 18V7.75a1.5 1.5 0 0 1 1.5-1.5ZM8 13.5h8M8 16h5" />
                </svg>
                <span class="app-sidebar-text">Attendance History</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-teal-600">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="app-sidebar-link w-full text-left text-sm text-teal-200 hover:text-white px-3 py-2 rounded-lg hover:bg-teal-600">
                    <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 8.75V6.5a1.75 1.75 0 0 0-1.75-1.75H6.5A1.75 1.75 0 0 0 4.75 6.5v11A1.75 1.75 0 0 0 6.5 19.25H14a1.75 1.75 0 0 0 1.75-1.75v-2.25M11.75 12h7.5M16.75 9.5l2.5 2.5-2.5 2.5" />
                    </svg>
                    <span class="app-sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
