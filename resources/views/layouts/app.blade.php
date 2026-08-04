<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - @yield('title', 'System')</title>
    
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Load CSS and JS files via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <!-- The Frosted Glass Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden transition-all duration-300" onclick="toggleSidebar()"></div>

    <!-- The Retractable Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-black/85 backdrop-blur-md text-white transform -translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-2xl border-r-4 border-red-900 flex flex-col">
        
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wider text-white">PBW<span class="text-red-900">SIS</span></h1>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
    <!-- Dashboard Link -->
    <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-md transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-red-900 text-white font-semibold shadow' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        Dashboard Overview
    </a>

    @php
        $isInventoryActive = request()->routeIs('inventory', 'ingredients.index', 'wastage.index');
        $isFileMaintActive = request()->routeIs('suppliers.*', 'vat.*', 'discounts.*');
        $isUserMaintActive = request()->routeIs('users.*');
    @endphp

    <!-- 1. Inventory Maintenance -->
    <div>
        <button onclick="toggleSubmenu('inventoryMenu', 'inventoryArrow')" class="w-full flex justify-between items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200 focus:outline-none">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span>Inventory Maintenance</span>
            </span>
            <svg id="inventoryArrow" class="w-4 h-4 transform transition-transform duration-300 {{ $isInventoryActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div id="inventoryMenu" class="{{ $isInventoryActive ? '' : 'hidden' }} pl-4 pr-2 py-2 mt-1 space-y-1 bg-black/40 rounded-md border-l-2 border-red-900 ml-2">
            <a href="{{ route('inventory') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('inventory') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                Products
            </a>
            <a href="{{ route('ingredients.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('ingredients.index') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                Ingredients
            </a>
            <a href="{{ route('wastage.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('wastage.index') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                Wastage Logs
            </a>
        </div>
    </div>

    <!-- 2. File Maintenance -->
    <div>
        <button onclick="toggleSubmenu('fileMaintMenu', 'fileMaintArrow')" class="w-full flex justify-between items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200 focus:outline-none">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 002 2z"></path></svg>
                <span>File Maintenance</span>
            </span>
            <svg id="fileMaintArrow" class="w-4 h-4 transform transition-transform duration-300 {{ $isFileMaintActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div id="fileMaintMenu" class="{{ $isFileMaintActive ? '' : 'hidden' }} pl-4 pr-2 py-2 mt-1 space-y-1 bg-black/40 rounded-md border-l-2 border-red-900 ml-2">
            <a href="{{ Route::has('suppliers.index') ? route('suppliers.index') : '#' }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('suppliers.*') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                Suppliers
            </a>
            <a href="{{ Route::has('vat.index') ? route('vat.index') : '#' }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('vat.*') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                VAT Settings
            </a>
            <a href="{{ Route::has('discounts.index') ? route('discounts.index') : '#' }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('discounts.*') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                Discounts
            </a>
        </div>
    </div>

    <!-- 3. User Maintenance -->
    <div>
        <button onclick="toggleSubmenu('userMaintMenu', 'userMaintArrow')" class="w-full flex justify-between items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200 focus:outline-none">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>User Maintenance</span>
            </span>
            <svg id="userMaintArrow" class="w-4 h-4 transform transition-transform duration-300 {{ $isUserMaintActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div id="userMaintMenu" class="{{ $isUserMaintActive ? '' : 'hidden' }} pl-4 pr-2 py-2 mt-1 space-y-1 bg-black/40 rounded-md border-l-2 border-red-900 ml-2">
            <a href="{{ Route::has('users.index') ? route('users.index') : '#' }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('users.*') ? 'text-white font-bold bg-red-900/40' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                Users & Roles
            </a>
        </div>
    </div>

    <!-- Point of Sale -->
    <a href="{{ route('pos') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200">
        Point of Sale (POS)
    </a>
</nav>
        
        <!-- Sidebar Footer with Logout Link -->
        <div class="p-4 border-t border-gray-800 text-sm text-gray-400 flex items-center justify-between">
            <div>Logged in as <span class="text-white font-bold">Owner</span></div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-300 transition flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- The Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto w-full">
        
        <!-- Modern Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="flex items-center justify-between p-4 px-6">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-gray-800 focus:outline-none hover:text-red-900 transition mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Dynamic Page Header -->
                    <h2 class="text-xl font-bold text-gray-800">@yield('header_title', 'Dashboard')</h2>
                </div>

                <!-- Top Right Header Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center space-x-1.5 text-xs font-bold text-gray-600 hover:text-red-900 bg-gray-100 hover:bg-red-50 px-3 py-2 rounded-lg border border-gray-200 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content Injection -->
        <main class="p-6 container mx-auto">
            @yield('content')
        </main>
    </div>

    <!-- The JavaScript Logic -->
    <script>
        // Toggles the entire main sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Toggles the nested submenus
        function toggleSubmenu(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);
            
            if (menu) {
                menu.classList.toggle('hidden');
            }
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
        }
    </script>
</body>
</html>