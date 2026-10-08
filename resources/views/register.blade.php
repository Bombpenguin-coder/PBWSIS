<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Create Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-900 flex items-center justify-center min-h-screen text-white font-sans p-4">

    <div class="w-full max-w-md p-8 bg-zinc-800 rounded-xl shadow-2xl border border-zinc-700">
        <!-- Logo Header -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-black tracking-wider">
                <span class="text-red-600">PBW</span><span class="text-white">SIS</span>
            </h1>
            <p class="text-zinc-400 text-xs mt-1">Create a New User Account</p>
        </div>

        <!-- Errors -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-900/50 border border-red-600 text-red-200 text-xs rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('setup.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Form Heading -->
            <div>
                <h2 class="text-2xl font-bold text-white">First-Time Setup</h2>
                <p class="text-zinc-400 text-sm">Create the Master Owner account to initialize PBWSIS.</p>
            </div>

            <!-- Username Field -->
            <div>
                <label for="username" class="block text-sm font-medium text-zinc-300 mb-1">Owner Username</label>
                <input type="text" 
                       name="username" 
                       id="username" 
                       value="{{ old('username') }}"
                       class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-white focus:outline-none focus:border-orange-500" 
                       placeholder="e.g. OWNER01" 
                       required>
                @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Number Field -->
            <div>
                <label for="contact_number" class="block text-sm font-medium text-zinc-300 mb-1">Contact Number (Optional)</label>
                <input type="text" 
                       name="contact_number" 
                       id="contact_number" 
                       value="{{ old('contact_number') }}"
                       maxlength="11"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-white focus:outline-none focus:border-orange-500" 
                       placeholder="09123456789">
                @error('contact_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-zinc-300 mb-1">Password</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-white focus:outline-none focus:border-orange-500" 
                       placeholder="••••••••" 
                       required>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hidden / Explicit Role Indicator -->
            <div class="p-3 bg-orange-500/10 border border-orange-500/30 rounded-md text-xs text-orange-400">
                <span class="font-bold">Role:</span> Master Owner (System Administrator)
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-2.5 bg-[#EA580C] hover:bg-orange-600 text-white font-semibold rounded-md transition shadow-md">
                Create Master Owner Account
            </button>
        </form>

        <!-- Login Link -->
        @if(\App\Models\User::exists())
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:underline">
                    Already have an account? Login here.
                </a>
            </div>
        @endif
    </div>

</body>
</html>