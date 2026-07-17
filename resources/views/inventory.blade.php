<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Inventory Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <!-- 1. The Frosted Glass Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden transition-all duration-300" onclick="toggleSidebar()"></div>

    <!-- 2. The Retractable Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-black/85 backdrop-blur-md text-white transform -translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-2xl border-r-4 border-red-900 flex flex-col">
        
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wider text-white">PBW<span class="text-red-900">SIS</span></h1>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200">
                Dashboard Overview
            </a>
            <!-- Active State applied to File Maintenance -->
            <a href="{{ route('inventory') }}" class="block px-4 py-3 bg-red-900 text-white rounded-md font-semibold shadow">
                File Maintenance
            </a>
            <a href="/pos" class="block px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition duration-200">
                Point of Sale (POS)
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
            Logged in as <span class="text-white font-bold">Owner</span>
        </div>
    </div>

    <!-- 3. The Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto w-full">
        
        <!-- Modern Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="flex items-center justify-between p-4 px-6">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-gray-800 focus:outline-none hover:text-red-900 transition mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">Inventory Management</h2>
                </div>
            </div>
        </header>

        <!-- Main Inventory Content -->
        <main class="p-6 container mx-auto">
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- File Maintenance: Add Product Form -->
                <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900">
                    <h2 class="text-lg font-bold mb-4 text-black">Add New Product</h2>
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2" for="product_name">Product Name</label>
                            <input type="text" name="product_name" id="product_name" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2" for="price">Price (₱)</label>
                            <input type="number" step="0.01" name="price" id="price" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2" for="stock_quantity">Initial Stock</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold mb-2" for="status">Status</label>
                            <select name="status" id="status" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Save Product
                        </button>
                    </form>
                </div>

                <!-- Inventory List Table -->
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-bold mb-4 text-black">Current Products</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-black text-white">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">ID</th>
                                    <th class="py-2 px-4 border-b text-left">Product Name</th>
                                    <th class="py-2 px-4 border-b text-left">Price</th>
                                    <th class="py-2 px-4 border-b text-left">Stock</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="py-2 px-4 border-b">{{ $product->id }}</td>
                                        <td class="py-2 px-4 border-b">{{ $product->product_name }}</td>
                                        <td class="py-2 px-4 border-b">₱{{ number_format($product->price, 2) }}</td>
                                        <td class="py-2 px-4 border-b font-bold {{ $product->stock_quantity < 10 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $product->stock_quantity }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $product->status === 'Available' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                                {{ $product->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">No products found. Start by adding one!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 4. The JavaScript Logic to Toggle Menu -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>