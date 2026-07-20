<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Create Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-900 flex items-center justify-center min-h-screen text-white font-sans">

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

        <!-- Registration Form -->
        <form action="/register" method="POST" class="space-y-4">
            @csrf

            <!-- Username -->
            <div>
                <label class="block text-xs font-semibold uppercase text-zinc-300 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Choose username" required
                    class="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 transition">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold uppercase text-zinc-300 mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required
                    class="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 transition">
            </div>

            <!-- Role Selection -->
            <div>
                <label class="block text-xs font-semibold uppercase text-zinc-300 mb-1">Role</label>
                <select name="role" required
                    class="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-red-600 transition">
                    <option value="Cashier">Cashier</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>

            <!-- Contact Number -->
            <div>
                <label class="block text-xs font-semibold uppercase text-zinc-300 mb-1">Contact Number (Optional)</label>
                <input type="text" name="contact_number" placeholder="09xxxxxxxxx"
                    class="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 transition">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 mt-2 bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-lg hover:shadow-red-900/40 transition duration-200">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-xs text-zinc-400 hover:text-white transition underline">Already have an account? Log In</a>
        </div>
    </div>

</body>
</html>