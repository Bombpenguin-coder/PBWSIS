<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f0f10] text-zinc-100 font-sans selection:bg-[#8B0000] selection:text-white min-h-screen flex items-center justify-center">

    <!-- Login Card Container -->
    <div class="w-full max-w-md p-8 bg-[#1a1a1e] rounded-2xl shadow-2xl border border-zinc-800 border-t-4 border-t-[#8B0000]">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-wider inline-flex items-center justify-center gap-1.5">
                <span class="text-white">PBW</span>
                <span class="bg-[#8B0000] text-white px-2.5 py-0.5 rounded-lg font-extrabold text-3xl tracking-tight shadow-sm">SIS</span>
            </h1>
            <p class="text-zinc-400 text-xs mt-2.5 uppercase tracking-widest font-bold">Prince Buffalo Wings Management System</p>
        </div>

        <!-- Success Message Alert -->
        @if (session('success'))
            <div class="mb-5 p-3.5 bg-emerald-950/60 border border-emerald-800 text-emerald-400 text-xs rounded-lg text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 bg-rose-950/60 border border-rose-800 text-rose-300 text-xs rounded-lg font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Username Input -->
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2 tracking-wider">
                    Username
                </label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username" required
                    class="w-full px-4 py-3 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-[#8B0000] focus:ring-2 focus:ring-[#8B0000]/30 transition duration-200 font-medium">
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2 tracking-wider">
                    Password
                </label>
                <input type="password" name="password" placeholder="••••••••" required
                    class="w-full px-4 py-3 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-[#8B0000] focus:ring-2 focus:ring-[#8B0000]/30 transition duration-200 font-medium">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 mt-2 bg-[#8B0000] hover:bg-[#700000] active:scale-[0.99] text-white font-black rounded-lg shadow-lg hover:shadow-red-950/40 transition-all duration-200 uppercase tracking-wider text-sm">
                Log In
            </button>
        </form>

        <!-- Create Account Link -->
        <div class="mt-8 pt-6 border-t border-zinc-800 text-center">
            <a href="{{ route('register') }}" class="text-xs text-zinc-400 hover:text-white transition duration-200 font-medium">
                Don't have an account? <span class="text-[#8B0000] hover:underline underline-offset-4 font-bold">Create one here</span>
            </a>
        </div>
    </div>

</body>
</html>