<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f0f10] flex items-center justify-center min-h-screen text-white font-sans selection:bg-amber-500 selection:text-black">

    <div class="w-full max-w-md p-8 bg-[#1a1a1e] rounded-xl shadow-2xl border border-zinc-800/80">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-wider inline-flex items-center justify-center gap-1">
                <span class="text-white">PBW</span>
                <span class="bg-[#ff8c00] text-black px-2 py-0.5 rounded-md font-extrabold text-3xl tracking-tight">SIS</span>
            </h1>
            <p class="text-zinc-400 text-sm mt-2 font-medium">Prince Buffalo Wings Management System</p>
        </div>

        <!-- Success Message Alert -->
        @if (session('success'))
            <div class="mb-5 p-3.5 bg-emerald-950/60 border border-emerald-600/50 text-emerald-300 text-xs rounded-lg text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 bg-rose-950/60 border border-rose-600/50 text-rose-300 text-xs rounded-lg font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <!-- Username Input -->
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2 tracking-wider">
                    Username
                </label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username" required
                    class="w-full px-4 py-3 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white placeholder-zinc-600 focus:outline-none focus:border-[#ff8c00] focus:ring-1 focus:ring-[#ff8c00] transition duration-200">
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2 tracking-wider">
                    Password
                </label>
                <input type="password" name="password" placeholder="••••••••" required
                    class="w-full px-4 py-3 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white placeholder-zinc-600 focus:outline-none focus:border-[#ff8c00] focus:ring-1 focus:ring-[#ff8c00] transition duration-200">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 mt-2 bg-[#ff8c00] hover:bg-[#e07b00] active:bg-[#c66c00] text-black font-extrabold rounded-lg shadow-lg shadow-amber-500/10 hover:shadow-amber-500/20 transition duration-200 uppercase tracking-wider text-sm">
                Log In
            </button>
        </form>

        <!-- Create Account Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('register') }}" class="text-xs text-zinc-400 hover:text-[#ff8c00] transition duration-200 underline underline-offset-4">
                Don't have an account? Create one here
            </a>
        </div>
    </div>

</body>
</html>