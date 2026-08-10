<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="app-sidebar bg-orange-900 text-white">

        {{-- Header --}}
        <div class="app-sidebar-header">
            <div class="app-sidebar-icon bg-orange-700">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5c3.4 0 6 1.54 6 3.45v1.8H6v-1.8c0-1.91 2.6-3.45 6-3.45ZM12 10.75A3.25 3.25 0 1 0 12 4.25a3.25 3.25 0 0 0 0 6.5ZM4.75 20.25h14.5" />
                </svg>
            </div>
            <div class="app-sidebar-text">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-orange-300 mb-0.5">Student Portal</p>
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <a href="{{ route('parent.dashboard') }}" title="Dashboard"
               class="app-sidebar-link {{ request()->routeIs('parent.dashboard') ? 'bg-orange-700 text-white' : 'text-orange-200 hover:bg-orange-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h6.5v8h-6.5v-8ZM13.75 4h6.5v16h-6.5V4ZM3.75 4h6.5v5h-6.5V4Z" />
                </svg>
                <span class="app-sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('parent.profile') }}" title="My Profile"
               class="app-sidebar-link {{ request()->routeIs('parent.profile') ? 'bg-orange-700 text-white' : 'text-orange-200 hover:bg-orange-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 13.25a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5.75 19.25c.8-2.56 3.08-4.25 6.25-4.25s5.45 1.69 6.25 4.25" />
                </svg>
                <span class="app-sidebar-text">My Profile</span>
            </a>

            @php($dueHomeworkCount = \App\Models\HomeworkSubmission::whereHas('student', fn ($q) => $q->where('parent_id', auth()->id()))->whereNull('submitted_at')->whereHas('homework', fn ($q) => $q->whereNull('due_at')->orWhere('due_at', '>', now()))->count())
            <a href="{{ route('parent.homework.index') }}" title="Homework{{ $dueHomeworkCount ? ' ('.$dueHomeworkCount.' due)' : '' }}"
               class="app-sidebar-link {{ request()->routeIs('parent.homework.*') ? 'bg-orange-700 text-white' : 'text-orange-200 hover:bg-orange-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="app-sidebar-text">Homework @if($dueHomeworkCount)<span class="ml-1 rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] text-white">{{ $dueHomeworkCount }}</span>@endif</span>
            </a>

            @php($unreadMessages = \App\Models\MessageRecipient::where('user_id', auth()->id())->whereNull('read_at')->count())
            <a href="{{ route('parent.messages.index') }}" title="Messages{{ $unreadMessages ? ' ('.$unreadMessages.' unread)' : '' }}"
               class="app-sidebar-link {{ request()->routeIs('parent.messages.*') ? 'bg-orange-700 text-white' : 'text-orange-200 hover:bg-orange-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 6.75A1.75 1.75 0 0 1 6.5 5h11A1.75 1.75 0 0 1 19.25 6.75v7.5A1.75 1.75 0 0 1 17.5 16h-6l-3.75 3V16H6.5a1.75 1.75 0 0 1-1.75-1.75v-7.5Z" />
                </svg>
                <span class="app-sidebar-text">Messages @if($unreadMessages)<span class="ml-1 rounded-full bg-orange-500 px-1.5 py-0.5 text-[10px] text-white">{{ $unreadMessages }}</span>@endif</span>
            </a>

            <a href="{{ route('parent.results.index') }}" title="Results"
               class="app-sidebar-link {{ request()->routeIs('parent.results.*') ? 'bg-orange-700 text-white' : 'text-orange-200 hover:bg-orange-800 hover:text-white' }}">
                <svg class="app-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-5.5M12 17V7M16 17v-3.5M4.75 4.75h14.5v14.5H4.75V4.75Z" />
                </svg>
                <span class="app-sidebar-text">Results</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Log Out" class="app-sidebar-link text-orange-300 hover:bg-orange-800 hover:text-white">
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

    @stack('scripts')
</body>
</html>
