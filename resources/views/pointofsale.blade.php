<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - Point of Sale Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased h-screen overflow-hidden flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-[#66001a] text-white shadow-md py-4 px-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold tracking-wider">PRINCE BUFFALO WINGS</h1>
            <span class="bg-black text-xs font-semibold px-2 py-1 rounded">POS TERMINAL</span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm">Cashier: <span class="font-bold">Active User</span></span>
            <button class="bg-black hover:bg-gray-800 text-white px-4 py-2 rounded text-sm transition">Logout</button>
        </div>
    </header>

    <!-- Main POS Layout -->
    <main class="flex-1 flex overflow-hidden">
        
        <!-- Left Column: Product Selection -->
        <section class="flex-1 p-6 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Menu Items</h2>
                <input type="text" placeholder="Search products..." class="border border-gray-300 rounded px-4 py-2 w-64 focus:outline-none focus:border-[#66001a]">
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Sample Product Card -->
                <button class="bg-white rounded-lg shadow hover:shadow-lg transition border border-transparent hover:border-[#66001a] p-4 flex flex-col items-center">
                    <div class="h-24 w-full bg-gray-200 rounded mb-3 flex items-center justify-center text-gray-500">Image</div>
                    <span class="font-bold text-gray-800 text-sm mb-1">Classic Buffalo Wings</span>
                    <span class="text-[#66001a] font-black">₱250.00</span>
                </button>

                <button class="bg-white rounded-lg shadow hover:shadow-lg transition border border-transparent hover:border-[#66001a] p-4 flex flex-col items-center">
                    <div class="h-24 w-full bg-gray-200 rounded mb-3 flex items-center justify-center text-gray-500">Image</div>
                    <span class="font-bold text-gray-800 text-sm mb-1">Garlic Parmesan</span>
                    <span class="text-[#66001a] font-black">₱260.00</span>
                </button>
                
                <button class="bg-white rounded-lg shadow hover:shadow-lg transition border border-transparent hover:border-[#66001a] p-4 flex flex-col items-center">
                    <div class="h-24 w-full bg-gray-200 rounded mb-3 flex items-center justify-center text-gray-500">Image</div>
                    <span class="font-bold text-gray-800 text-sm mb-1">Classic Milk Tea</span>
                    <span class="text-[#66001a] font-black">₱120.00</span>
                </button>
            </div>
        </section>

        <!-- Right Column: Cart and Checkout -->
        <aside class="w-[400px] bg-white shadow-xl flex flex-col z-10 border-l border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">Current Order</h2>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Sample Cart Item -->
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <p class="font-bold text-sm text-gray-800">Classic Buffalo Wings</p>
                        <p class="text-xs text-gray-500">₱250.00 x 2</p>
                    </div>
                    <div class="font-bold text-gray-800">₱500.00</div>
                </div>
            </div>

            <!-- Order Channel & Discounts -->
            <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-4">
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase">Order Channel</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button class="bg-black text-white text-xs py-2 rounded">Walk-in</button>
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs py-2 rounded transition">Grabfood</button>
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs py-2 rounded transition">Foodpanda</button>
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs py-2 rounded transition">Facebook</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase">Discounts</label>
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs py-2 rounded transition">None</button>
                        <button class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs py-2 rounded transition">Senior (20%)</button>
                        <button class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs py-2 rounded transition">PWD (20%)</button>
                    </div>
                </div>
            </div>

            <!-- Totals & Payment -->
            <div class="p-4 bg-gray-800 text-white">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-300">Subtotal</span>
                    <span>₱500.00</span>
                </div>
                <div class="flex justify-between mb-3 text-red-400">
                    <span>Discount</span>
                    <span>-₱0.00</span>
                </div>
                <div class="flex justify-between text-xl font-black mb-4">
                    <span>TOTAL</span>
                    <span>₱500.00</span>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-2">
                    <button class="bg-gray-600 hover:bg-gray-500 py-3 rounded font-bold transition">CASH</button>
                    <button class="bg-blue-600 hover:bg-blue-500 py-3 rounded font-bold transition">GCASH</button>
                </div>
                <button class="w-full bg-[#66001a] hover:bg-[#800020] py-4 rounded font-black text-lg transition tracking-wide mt-2 shadow-lg">
                    PROCESS PAYMENT
                </button>
            </div>
        </aside>
    </main>

</body>
</html>