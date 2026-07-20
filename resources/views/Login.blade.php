<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-900 flex items-center justify-center min-h-screen text-white font-sans">

    <div class="w-full max-w-md p-8 bg-zinc-800 rounded-xl shadow-2xl border border-zinc-700">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-wider">
                <span class="text-red-600">PBW</span><span class="text-white">SIS</span>
            </h1>
            <p class="text-zinc-400 text-sm mt-1 font-medium">Prince Buffalo Wings Management System</p>
        </div>

        <!-- Success Message Alert -->
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-900/50 border border-green-600 text-green-200 text-xs rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-900/50 border border-red-600 text-red-200 text-xs rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="/login" method="POST" class="space-y-5">
            @csrf

            <!-- Username Input -->
            <div>
                <label class="block text-xs font-semibold uppercase text-zinc-300 mb-2 tracking-wide">
                    Username
                </label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username" required
                    class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 transition">
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-xs font-semibold uppercase text-zinc-300 mb-2 tracking-wide">
                    Password
                </label>
                <input type="password" name="password" placeholder="••••••••" required
                    class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 transition">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 mt-2 bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-lg hover:shadow-red-900/40 transition duration-200">
                Log In
            </button>
        </form>

        <!-- Create Account Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('register') }}" class="text-xs text-zinc-400 hover:text-white transition underline">
                Don't have an account? Create one here
            </a>
        </div>
    </div>

</body>
</html>