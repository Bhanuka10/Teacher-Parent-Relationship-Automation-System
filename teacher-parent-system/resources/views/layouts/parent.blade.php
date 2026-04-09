<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Parent') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-orange-50 flex min-h-screen">

    <aside class="w-64 bg-orange-700 text-white flex flex-col min-h-screen shrink-0">
        <div class="px-6 py-5 border-b border-orange-600">
            <p class="text-xs font-semibold uppercase tracking-widest text-orange-200 mb-1">Parent Portal</p>
            <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('parent.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('parent.dashboard') ? 'bg-orange-500 text-white' : 'text-orange-100 hover:bg-orange-600' }}">
                Dashboard
            </a>
            <a href="{{ route('parent.profile') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('parent.profile') ? 'bg-orange-500 text-white' : 'text-orange-100 hover:bg-orange-600' }}">
                My Profile
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-orange-600">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-sm text-orange-200 hover:text-white px-3 py-2 rounded-lg hover:bg-orange-600">
                    Log Out
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