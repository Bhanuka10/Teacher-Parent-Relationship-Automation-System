<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-purple-800 text-white flex flex-col min-h-screen shrink-0">
        <div class="px-6 py-5 border-b border-purple-700">
            <p class="text-xs font-semibold uppercase tracking-widest text-purple-300 mb-1">Admin Panel</p>
            <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('admin.users.*') ? 'bg-purple-600 text-white' : 'text-purple-200 hover:bg-purple-700' }}">
                Manage Users
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-purple-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-sm text-purple-300 hover:text-white px-3 py-2 rounded-lg hover:bg-purple-700">
                    Log Out
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

        @yield('content')
    </main>

</body>
</html>