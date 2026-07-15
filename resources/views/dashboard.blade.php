<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS | Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cranberry: '#6b1225', // Prince Buffalo Wings Dark Cranberry
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen">

    <aside class="w-64 bg-black text-white flex flex-col">
        <div class="p-6 text-center border-b border-gray-800">
            <h1 class="text-2xl font-bold text-cranberry tracking-wider">PBWSIS</h1>
            <p class="text-xs text-gray-400 mt-1">Owner Panel</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="#" class="block px-4 py-2 bg-cranberry rounded text-white font-medium">Dashboard</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-800 rounded text-gray-300">Point of Sale</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-800 rounded text-gray-300">Inventory</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-800 rounded text-gray-300">Reports</a>
        </nav>
        <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
            Logged in as: <span class="text-white">admin_owner</span>
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        
        <header class="h-16 bg-cranberry text-white flex items-center px-8 shadow-md">
            <h2 class="text-xl font-semibold">System Overview</h2>
        </header>

        <div class="p-8 grid grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-black">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Today's Sales</h3>
                <p class="text-3xl font-bold text-black mt-2">₱ 0.00</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-cranberry">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Active Orders</h3>
                <p class="text-3xl font-bold text-cranberry mt-2">{{ $activeProductsCount }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-red-600">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Low Stock Alerts</h3>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ $lowStockCount }} Items</p>
                <p class="text-xs text-gray-400 mt-1">Check Ingredients table</p>
            </div>

        </div>
    </main>

</body>
</html>