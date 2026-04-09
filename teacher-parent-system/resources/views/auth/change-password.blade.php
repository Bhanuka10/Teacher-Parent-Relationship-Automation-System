<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-xl shadow w-full max-w-md">
    <h1 class="text-xl font-bold text-gray-800 mb-1">Set your new password</h1>
    <p class="text-sm text-gray-500 mb-6">Your account requires a password change before continuing.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
            <input type="password" name="password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm new password</label>
            <input type="password" name="password_confirmation"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        </div>

        <button type="submit"
            class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg font-medium text-sm transition">
            Update password & log in
        </button>
    </form>
</div>

</body>
</html>