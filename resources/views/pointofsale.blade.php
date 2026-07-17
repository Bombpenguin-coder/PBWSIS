<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - POS Terminal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans h-screen flex flex-col overflow-hidden">

    <!-- POS Top Navigation -->
    <nav class="bg-black text-white p-4 shadow-md shrink-0">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wider">PBWSIS <span class="text-red-900">|</span> POS Terminal</h1>
            <div class="flex space-x-4 items-center">
                <span class="text-gray-400 text-sm">Cashier on Duty</span>
                <a href="{{ route('dashboard') }}" class="bg-red-900 hover:bg-red-800 text-white font-bold py-1 px-4 rounded transition duration-200 text-sm">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- POS Main Interface (Split Screen) -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Left Side: Product Grid (70% width) -->
        <div class="w-2/3 p-6 bg-gray-100 overflow-y-auto">
            <h2 class="text-lg font-bold text-black mb-4">Available Menu Items</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($products as $product)
                <!-- Product Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-md hover:border-red-900 transition duration-200" 
                         data-id="{{ $product->id }}" 
                         data-name="{{ $product->product_name }}" 
                         data-price="{{ $product->price }}"
                         onclick="addToCart(this)">
                         
                        <div class="h-24 bg-gray-200 rounded-md mb-3 flex items-center justify-center text-gray-400">
                            <!-- Placeholder for product image -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800 truncate">{{ $product->product_name }}</h3>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-red-900 font-bold">₱{{ number_format($product->price, 2) }}</span>
                            <span class="text-xs text-gray-500">Stock: {{ $product->stock_quantity }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                        No available products found. Please add stock via File Maintenance.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Order Summary / Cart (30% width) -->
        <div class="w-1/3 bg-white border-l border-gray-200 shadow-xl flex flex-col">
            <div class="p-4 bg-black text-white">
                <h2 class="font-bold text-lg">Current Order</h2>
            </div>
            
            <!-- Cart Items Area -->
            <div id="cartItemsContainer" class="flex-1 p-4 overflow-y-auto bg-gray-50">
                <p class="text-gray-500 text-center mt-10 text-sm">Cart is currently empty. Click an item to add it.</p>
                <!-- JS will inject cart items here later -->
            </div>

            <!-- Cart Totals & Checkout -->
            <div class="p-4 border-t border-gray-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                
                <div class="flex justify-between mb-2 text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span id="subtotalDisplay">₱0.00</span>
                </div>
                
                <div class="flex justify-between mb-4 text-sm text-red-900 font-bold">
                    <span>Discount</span>
                    <span id="discountDisplay">-₱0.00</span>
                </div>

                <div class="flex justify-between mb-6 text-xl font-bold text-black border-t pt-2">
                    <span>Total</span>
                    <span id="totalDisplay">₱0.00</span>
                </div>

                <button class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-200 text-lg">
                    Process Payment
                </button>
            </div>
        </div>

    </div>

<!-- Future JavaScript Logic -->
    <script>
        function addToCart(element) {
            // Extract the data from the custom HTML attributes
            const id = element.getAttribute('data-id');
            const name = element.getAttribute('data-name');
            const price = parseFloat(element.getAttribute('data-price'));

            console.log("Clicked:", name, "Price: ₱" + price);
            // We will build the logic to update the cart interface here
        }
    </script>
</body>
</html>