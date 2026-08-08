<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-orange-50 flex min-h-screen">

    <aside class="app-sidebar bg-orange-700 text-white flex flex-col min-h-screen shrink-0">
        <div class="app-sidebar-header px-4 py-5 border-b border-orange-600">
            <div class="app-sidebar-icon bg-orange-600/70">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5ZM4.75 20.25h14.5" />
                </svg>
            </div>
            <div class="app-sidebar-text">
                <p class="text-xs font-semibold uppercase tracking-widest text-orange-200 mb-1">Student Portal</p>
                <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('parent.dashboard') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('parent.dashboard') ? 'bg-orange-500 text-white' : 'text-orange-100 hover:bg-orange-600' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h6.5v8h-6.5v-8ZM13.75 4h6.5v16h-6.5V4ZM3.75 4h6.5v5h-6.5V4Z" />
                </svg>
                <span class="app-sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('parent.profile') }}"
               class="app-sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('parent.profile') ? 'bg-orange-500 text-white' : 'text-orange-100 hover:bg-orange-600' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25" />
                </svg>
                <span class="app-sidebar-text">My Profile</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-orange-600">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="app-sidebar-link w-full text-left text-sm text-orange-200 hover:text-white px-3 py-2 rounded-lg hover:bg-orange-600">
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
