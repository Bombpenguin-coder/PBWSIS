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
                    <!-- Product Card with corrected data-id -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-md hover:border-red-900 transition duration-200" 
                         data-id="{{ $product->product_id }}" 
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
                
                <!-- NEW: Discount Selection -->
                <div class="mb-4 pb-4 border-b border-gray-100">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Apply Discount</label>
                    <select id="discountType" onchange="updateTotals()" class="w-full text-sm border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 bg-gray-50">
                        <option value="none">None (Regular)</option>
                        <option value="senior">Senior Citizen (20%)</option>
                        <option value="pwd">PWD (20%)</option>
                    </select>
                </div>
                
                <!-- Existing Totals -->
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

<!-- JavaScript Cart Logic -->
    <script>
        // 1. The Cart Array (State)
        let cart = [];

        // 2. Add item to cart
        function addToCart(element) {
            // 1. Extract the data safely
            const id = element.getAttribute('data-id');
            const name = element.getAttribute('data-name');
            const price = parseFloat(element.getAttribute('data-price'));

            // 2. Debugging: Check what data is actually being pulled
            console.log("Extracted Data -> ID:", id, "| Name:", name, "| Price: ₱" + price);

            // 3. Check if this item is already in the cart array
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += 1;
                console.log("Item exists. Increasing quantity to:", existingItem.quantity);
            } else {
                cart.push({ id, name, price, quantity: 1 });
                console.log("New item added to cart array.");
            }

            // 4. Redraw the cart UI
            updateCartUI();
        }

        // 3. Remove item from cart
        function removeFromCart(index) {
            // Remove 1 item at the specific index
            cart.splice(index, 1);
            updateCartUI();
        }

        // 4. Update the visual Cart UI
        function updateCartUI() {
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = ''; // Clear out the current HTML

            // If empty, show the default message
            if (cart.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center mt-10 text-sm">Cart is currently empty. Click an item to add it.</p>';
                updateTotals();
                return;
            }

            // Loop through the cart array and build the HTML for each item
            cart.forEach((item, index) => {
                const itemHTML = `
                    <div class="flex justify-between items-center mb-4 bg-white p-3 rounded shadow-sm border border-gray-100">
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-800">${item.name}</h4>
                            <div class="text-xs text-gray-500">₱${item.price.toFixed(2)} x ${item.quantity}</div>
                        </div>
                        <div class="font-bold text-red-900 mr-4">
                            ₱${(item.price * item.quantity).toFixed(2)}
                        </div>
                        <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                container.innerHTML += itemHTML;
            });

            updateTotals();
        }

        // 5. Calculate and display totals
        function updateTotals() {
            // Calculate subtotal by multiplying price * quantity for every item
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            // Check the selected discount type
            const discountSelect = document.getElementById('discountType');
            let discountRate = 0;
            
            // Apply 20% discount if Senior or PWD is chosen
            if (discountSelect && (discountSelect.value === 'senior' || discountSelect.value === 'pwd')) {
                discountRate = 0.20; 
            }

            // Calculate final numbers
            const discount = subtotal * discountRate;
            const total = subtotal - discount;

            // Update the DOM elements
            document.getElementById('subtotalDisplay').innerText = '₱' + subtotal.toFixed(2);
            document.getElementById('discountDisplay').innerText = '-₱' + discount.toFixed(2);
            document.getElementById('totalDisplay').innerText = '₱' + total.toFixed(2);
        }
    </script>
</body>
</html>