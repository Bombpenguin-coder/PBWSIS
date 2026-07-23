<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Dynamic Title -->
    <title>PBWSIS - @yield('title', 'System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            
            <!-- NEW: File Maintenance Accordion Menu -->
            <div>
                <!-- The Parent Toggle Button -->
                <button onclick="toggleSubmenu('fileMaintenanceMenu', 'fileMaintenanceArrow')" class="w-full flex justify-between items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200 focus:outline-none">
                    <span>File Maintenance</span>
                    <!-- The SVG Arrow -->
                    <svg id="fileMaintenanceArrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <!-- The Hidden Submenu Links -->
                <div id="fileMaintenanceMenu" class="hidden pl-4 pr-2 py-2 mt-1 space-y-1 bg-black/40 rounded-md border-l-2 border-red-900 ml-2">
                    <a href="{{ route('inventory') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('inventory') ? 'text-white font-bold' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Products
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded-md transition duration-200">
                        Ingredients
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded-md transition duration-200">
                        Wastage Logs
                    </a>
                </div>
            </div>
            
            <!-- POS Link -->
            <a href="{{ route('pos') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200">
                Point of Sale (POS)
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
            Logged in as <span class="text-white font-bold">Owner</span>
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
            
            // Toggle the visibility of the dropdown
            menu.classList.toggle('hidden');
            
            // Flip the arrow upside down smoothly
            arrow.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>