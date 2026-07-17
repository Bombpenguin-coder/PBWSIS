<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <!-- 1. The Frosted Glass Overlay (Lightened and blurred) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden transition-all duration-300" onclick="toggleSidebar()"></div>

    <!-- 2. The Retractable Sidebar (Slightly transparent black with glass effect) -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-black/85 backdrop-blur-md text-white transform -translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-2xl border-r-4 border-red-900 flex flex-col">    
    
    <!-- Sidebar Header / Branding -->
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wider text-white">PBW<span class="text-red-900">SIS</span></h1>
            <!-- Close Button inside sidebar -->
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Sidebar Links -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 bg-red-900 text-white rounded-md font-semibold shadow">
                Dashboard Overview
            </a>
            <a href="{{ route('inventory') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200">
                File Maintenance
            </a>
            <a href="/pos" class="block px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200">
                Point of Sale (POS)
            </a>
        </nav>
        
        <!-- User Profile Placeholder -->
        <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
            Logged in as <span class="text-white font-bold">Owner</span>
        </div>
    </div>

    <!-- 3. The Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto w-full">
        
        <!-- Modern Header with Hamburger Menu Button -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="flex items-center justify-between p-4 px-6">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-gray-800 focus:outline-none hover:text-red-900 transition mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
                </div>
            </div>
        </header>

        <!-- Dashboard Widgets (Reused from previous step) -->
        <main class="p-6 container mx-auto">
            
            <div class="mb-8">
                <p class="text-gray-600 text-lg">Welcome back. Here is the current status of Prince Buffalo Wings.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-black hover:shadow-lg transition duration-200">
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Today's Sales</h3>
                    <p class="text-3xl font-bold text-black">₱0.00</p>
                    <p class="text-sm text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Data pending POS integration
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-900 hover:shadow-lg transition duration-200">
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Low Stock Alerts</h3>
                    <p class="text-3xl font-bold {{ $totalLowStock > 0 ? 'text-red-900' : 'text-green-600' }}">
                        {{ $totalLowStock }} {{ Str::plural('Item', $totalLowStock) }}
                    </p>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Below 50% capacity threshold
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-black hover:shadow-lg transition duration-200">
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Monthly Profit</h3>
                    <p class="text-3xl font-bold text-black">₱0.00</p>
                    <p class="text-sm text-gray-500 mt-2">Data pending expense records</p>
                </div>
            </div>

        </main>
    </div>

    <!-- 4. The JavaScript Logic to Toggle Menu -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Toggle the off-screen CSS class
            sidebar.classList.toggle('-translate-x-full');
            
            // Toggle the dark overlay visibility
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>