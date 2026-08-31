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
<body class="bg-[#18191c] text-zinc-100 font-sans antialiased flex h-screen overflow-hidden selection:bg-[#ff8c00] selection:text-black">

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden md:hidden transition-all duration-300" onclick="toggleSidebar()"></div>

    <!-- Push/Collapsible Sidebar -->
    <aside id="sidebar" class="fixed md:static inset-y-0 left-0 w-64 bg-[#111214] text-white transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 shadow-2xl border-r border-zinc-800 flex flex-col shrink-0">
        
        <!-- Sidebar Header -->
        <div class="p-5 border-b border-zinc-800 flex justify-between items-center">
            <h1 class="text-xl font-black tracking-wider inline-flex items-center gap-1">
                <span class="text-white">PBW</span>
                <span class="bg-[#ff8c00] text-black px-1.5 py-0.5 rounded font-extrabold text-lg">SIS</span>
            </h1>
            <button onclick="toggleSidebar()" class="text-zinc-400 hover:text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            
            <!-- 1. OPERATIONS -->
            <div>
                <button onclick="toggleSubmenu('opsMenu', 'opsArrow')" class="w-full flex justify-between items-center px-4 py-3 text-zinc-300 hover:bg-zinc-800/80 hover:text-white rounded-lg transition duration-200 focus:outline-none">
                    <span class="font-bold tracking-wider text-xs uppercase">Operations</span>
                    <svg id="opsArrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="opsMenu" class="hidden pl-4 pr-2 py-2 mt-1 space-y-1 bg-[#090a0b] rounded-lg border-l-2 border-[#ff8c00] ml-2">
                    <a href="{{ route('pos') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('pos') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}"> POS / Billing</a>
                    <a href="{{ route('operations.held') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('operations.held') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Held Orders</a>
                    <a href="{{ route('operations.kot') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('operations.kot') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">KOT / Counter Tickets</a>
                    <a href="{{ route('operations.bills') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('operations.bills') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Bills</a>
                </div>
            </div>

            <!-- 2. INSIGHTS -->
            <div>
                <button onclick="toggleSubmenu('insightsMenu', 'insightsArrow')" class="w-full flex justify-between items-center px-4 py-3 text-zinc-300 hover:bg-zinc-800/80 hover:text-white rounded-lg transition duration-200 focus:outline-none">
                    <span class="font-bold tracking-wider text-xs uppercase">Insights</span>
                    <svg id="insightsArrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="insightsMenu" class="hidden pl-4 pr-2 py-2 mt-1 space-y-1 bg-[#090a0b] rounded-lg border-l-2 border-[#ff8c00] ml-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('dashboard') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Dashboard</a>
                    <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('reports.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Reports</a>
                </div>
            </div>

            <!-- 3. INVENTORY -->
            <div>
                <button onclick="toggleSubmenu('inventoryMenu', 'inventoryArrow')" class="w-full flex justify-between items-center px-4 py-3 text-zinc-300 hover:bg-zinc-800/80 hover:text-white rounded-lg transition duration-200 focus:outline-none">
                    <span class="font-bold tracking-wider text-xs uppercase">Inventory</span>
                    <svg id="inventoryArrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="inventoryMenu" class="hidden pl-4 pr-2 py-2 mt-1 space-y-1 bg-[#090a0b] rounded-lg border-l-2 border-[#ff8c00] ml-2">
                    <a href="{{ route('ingredients.index') ?? '#' }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('ingredients.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Ingredients</a>
                    <a href="{{ route('wastage.index') ?? '#' }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('wastage.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Wastage Logs</a>
                </div>
            </div>

            <!-- 4. FILE MAINTENANCE -->
            <div>
                <button onclick="toggleSubmenu('fileMenu', 'fileArrow')" class="w-full flex justify-between items-center px-4 py-3 text-zinc-300 hover:bg-zinc-800/80 hover:text-white rounded-lg transition duration-200 focus:outline-none">
                    <span class="font-bold tracking-wider text-xs uppercase">File Maintenance</span>
                    <svg id="fileArrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="fileMenu" class="hidden pl-4 pr-2 py-2 mt-1 space-y-1 bg-[#090a0b] rounded-lg border-l-2 border-[#ff8c00] ml-2">
                    <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('categories.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Categories</a>
                    <a href="{{ route('inventory') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('inventory') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Products Catalog</a>
                    <a href="{{ route('discounts.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('discounts.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Discount</a>
                    <a href="{{ route('vat.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('vat.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">VAT</a>
                    <a href="{{ route('purchases.index') }}" class="block px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/40 rounded-md transition duration-200">Purchases</a>
                    <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('suppliers.*') ? 'text-[#ff8c00] font-bold bg-zinc-800/60' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}">Suppliers</a>
                </div>
            </div>

            <!-- 5. ADMINISTRATION -->
            <div>
                <button onclick="toggleSubmenu('adminMenu', 'adminArrow')" class="w-full flex justify-between items-center px-4 py-3 text-zinc-300 hover:bg-zinc-800/80 hover:text-white rounded-lg transition duration-200 focus:outline-none">
                    <span class="font-bold tracking-wider text-xs uppercase">Administration</span>
                    <svg id="adminArrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="adminMenu" class="hidden pl-4 pr-2 py-2 mt-1 space-y-1 bg-[#090a0b] rounded-lg border-l-2 border-[#ff8c00] ml-2">
                    <a href="#" class="block px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/40 rounded-md transition duration-200">Import / Export</a>
                    <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/40 rounded-md transition duration-200">User Management</a>
                    <a href="#" class="block px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/40 rounded-md transition duration-200">List Management</a>
                    <a href="#" class="block px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/40 rounded-md transition duration-200">Settings</a>
                    <a href="#" class="block px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/40 rounded-md transition duration-200">Audit Log</a>
                </div>
            </div>

        </nav>
        
        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-zinc-800 text-sm text-zinc-400 flex items-center justify-between bg-[#111214]">
            <div>Logged in as <span class="text-white font-bold">Owner</span></div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold text-[#ff8c00] hover:text-amber-400 transition flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main View Canvas (Medium Dark Slate Gray Background) -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto w-full bg-[#18191c]">
        
        <!-- Top Navigation Header -->
        <header class="bg-[#202226] border-b border-zinc-800 sticky top-0 z-30 shadow-md">
            <div class="flex items-center justify-between p-4 px-6">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-zinc-300 focus:outline-none hover:text-[#ff8c00] transition mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Dynamic Page Header -->
                    <h2 class="text-xl font-bold text-white tracking-wide">@yield('header_title', 'Dashboard')</h2>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                </form>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="p-6 container mx-auto">
            @yield('content')
        </main>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#202226] border border-zinc-700 rounded-xl shadow-2xl max-w-md w-full p-6 text-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="editModalTitle" class="text-lg font-bold text-white">Edit Item</h3>
                <button type="button" onclick="closeEditModal()" class="text-zinc-400 hover:text-white">&times;</button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div id="editFormFields" class="space-y-3"></div>

                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 rounded-lg text-sm transition font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#ff8c00] hover:bg-[#e07b00] text-black rounded-lg text-sm font-bold transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar) {
                if (window.innerWidth >= 768) {
                    sidebar.classList.toggle('hidden');
                } else {
                    sidebar.classList.toggle('-translate-x-full');
                }
            }
            if (overlay) overlay.classList.toggle('hidden');
        }

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

        function openEditModal(actionUrl, title, fields) {
            const form = document.getElementById('editForm');
            const titleEl = document.getElementById('editModalTitle');
            const container = document.getElementById('editFormFields');

            if (form) form.action = actionUrl;
            if (titleEl) titleEl.innerText = title;
            if (container) container.innerHTML = '';

            fields.forEach(field => {
                let inputHtml = '';
                if (field.type === 'textarea') {
                    inputHtml = `
                        <div>
                            <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">${field.label}</label>
                            <textarea name="${field.name}" class="w-full bg-[#18191c] border border-zinc-700 rounded-lg p-2 text-sm text-white focus:outline-none focus:border-[#ff8c00]" ${field.required ? 'required' : ''}>${field.value || ''}</textarea>
                        </div>
                    `;
                } else {
                    inputHtml = `
                        <div>
                            <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">${field.label}</label>
                            <input type="${field.type || 'text'}" name="${field.name}" value="${field.value ?? ''}" class="w-full bg-[#18191c] border border-zinc-700 rounded-lg p-2 text-sm text-white focus:outline-none focus:border-[#ff8c00]" ${field.required ? 'required' : ''}>
                        </div>
                    `;
                }
                container.innerHTML += inputHtml;
            });

            document.getElementById('editModal')?.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal')?.classList.add('hidden');
        }
    </script>
</body>
</html>