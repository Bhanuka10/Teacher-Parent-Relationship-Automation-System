<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teacher') — School System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex min-h-screen">

    <aside class="w-64 bg-teal-700 text-white flex flex-col min-h-screen shrink-0">
        <div class="px-6 py-5 border-b border-teal-600">
            <p class="text-xs font-semibold uppercase tracking-widest text-teal-200 mb-1">Teacher Portal</p>
            <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('teacher.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('teacher.dashboard') ? 'bg-teal-500 text-white' : 'text-teal-100 hover:bg-teal-600' }}">
                Dashboard
            </a>
            {{-- Attendance, Marks, Messages will be added in future phases --}}
        </nav>

        <div class="px-4 py-4 border-t border-teal-600">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-sm text-teal-200 hover:text-white px-3 py-2 rounded-lg hover:bg-teal-600">
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