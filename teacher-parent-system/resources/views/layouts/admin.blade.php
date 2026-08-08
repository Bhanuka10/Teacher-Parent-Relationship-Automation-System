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
    <aside class="app-sidebar bg-purple-800 text-white flex flex-col min-h-screen shrink-0">
        <div class="app-sidebar-header px-4 py-5 border-b border-purple-700">
            <div class="app-sidebar-icon bg-purple-700/70">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21V8.5L12 3l8 5.5V21M9 21v-6h6v6M8 10h.01M16 10h.01" />
                </svg>
            </div>
            <div class="app-sidebar-text">
                <p class="text-xs font-semibold uppercase tracking-widest text-purple-300 mb-1">Admin Panel</p>
                <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h6.5v8h-6.5v-8ZM13.75 4h6.5v16h-6.5V4ZM3.75 4h6.5v5h-6.5V4Z" />
                </svg>
                <span class="app-sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.users.*') && request('role') !== 'parent' ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.25 0 5.75 1.54 5.75 3.5v1.5H6.25v-1.5c0-1.96 2.5-3.5 5.75-3.5ZM12 11a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 12 11ZM18.5 6.75h2.75M19.88 5.38v2.75" />
                </svg>
                <span class="app-sidebar-text">Teachers</span>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'parent']) }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.users.*') && request('role') === 'parent' ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5ZM4.75 20.25h14.5" />
                </svg>
                <span class="app-sidebar-text">Students</span>
            </a>
            <a href="{{ route('admin.classes.index') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.classes.*') ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 5.5h14.5v13H4.75v-13ZM8 9h8M8 12h5M8 15h7" />
                </svg>
                <span class="app-sidebar-text">Classes</span>
            </a>
            <a href="{{ route('admin.attendance.history') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.attendance.*') ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.75 4.75v3M16.25 4.75v3M5.25 8.25h13.5M6 6.25h12a1.5 1.5 0 0 1 1.5 1.5V18A1.5 1.5 0 0 1 18 19.5H6A1.5 1.5 0 0 1 4.5 18V7.75a1.5 1.5 0 0 1 1.5-1.5ZM8 14l2 2 5-5" />
                </svg>
                <span class="app-sidebar-text">Attendance</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-purple-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="app-sidebar-link w-full text-left text-sm text-purple-300 hover:text-white px-3 py-2 rounded-lg hover:bg-purple-700">
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
            <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')))
            <div class="mb-6 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg text-sm">
                <p class="font-semibold mb-1">Some rows were skipped:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach(session('import_errors') as $importError)
                        <li>{{ $importError }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
