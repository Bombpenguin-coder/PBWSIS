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
        
        <!-- Left Side: Product Grid + Search/Categories -->
        <div class="w-3/5 p-6 bg-gray-100 flex flex-col overflow-hidden">
            
            <!-- Search & Category Controls Section -->
            <div class="mb-4 space-y-3 shrink-0">
                <!-- Live Search Bar -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Search menu items (e.g. Taro, Pearl, Fried Chicken)..." 
                           class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-900 shadow-sm">
                </div>

                <!-- Category Pills Filter -->
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button type="button" onclick="setCategory('all', this)" class="cat-btn bg-red-900 text-white text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap">
                        All Items
                    </button>
                    <button type="button" onclick="setCategory('milktea', this)" class="cat-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap">
                        🧋 Milk Tea
                    </button>
                    <button type="button" onclick="setCategory('chicken', this)" class="cat-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap">
                        🍗 Chicken
                    </button>
                </div>
            </div>

            <!-- Scrollable Menu Grid -->
            <div class="flex-1 overflow-y-auto pr-1">
                <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($products as $product)
                        <!-- Product Card -->
                        <div id="product-card-{{ $product->product_id }}"
                             class="product-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-md hover:border-red-900 transition duration-200 select-none" 
                             data-id="{{ $product->product_id }}" 
                             data-name="{{ $product->product_name }}" 
                             data-category="{{ strtolower($product->category_name ?? $product->category ?? '') }}"
                             data-price="{{ $product->price }}"
                             data-stock="{{ $product->stock_quantity }}"
                             onclick="addToCart(this)">
                             
                            <div class="h-24 bg-gray-200 rounded-md mb-3 flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 truncate">{{ $product->product_name }}</h3>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-red-900 font-bold">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-xs text-gray-500">
                                    Stock: <span id="stock-val-{{ $product->product_id }}">{{ $product->stock_quantity }}</span>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-4 bg-yellow-100 text-yellow-800 rounded-lg text-sm">
                            No available products found. Please add stock via File Maintenance.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Side: Order Summary / Cart -->
        <div class="w-2/5 bg-white border-l border-gray-200 shadow-xl flex flex-col">
            <div class="p-4 bg-black text-white flex justify-between items-center">
                <h2 class="font-bold text-lg">Current Order</h2>
                
                <!-- Hold & Parked Sales Controls -->
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="holdCurrentOrder()" class="bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold py-1 px-3 rounded transition shadow">
                        Hold
                    </button>
                    <button type="button" onclick="openHeldOrdersModal()" class="bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold py-1 px-3 rounded border border-gray-700 transition flex items-center space-x-1">
                        <span>Parked</span>
                        <span id="heldCountBadge" class="bg-red-900 text-white text-[10px] px-1.5 py-0.5 rounded-full font-extrabold">0</span>
                    </button>
                </div>
            </div>
            
            <!-- Cart Items Area -->
            <div id="cartItemsContainer" class="flex-1 min-h-0 p-4 overflow-y-auto bg-gray-50 space-y-3">
                <p class="text-gray-500 text-center mt-10 text-sm">Cart is currently empty. Click an item to add it.</p>
            </div>

            <!-- Cart Controls & Checkout -->
            <div class="p-4 border-t border-gray-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] shrink-0">
                
                <!-- Order Channel & Discount Side-by-Side -->
                <div class="grid grid-cols-2 gap-3 mb-3 pb-3 border-b border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Channel</label>
                        <select id="orderChannel" class="w-full text-xs border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 bg-gray-50">
                            <option value="Walk-in">Walk-in</option>
                            <option value="Grabfood">Grabfood</option>
                            <option value="Foodpanda">Foodpanda</option>
                            <option value="Facebook">Facebook / Online</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Discount</label>
                        <select id="discountType" onchange="updateTotals()" class="w-full text-xs border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 bg-gray-50">
                            <option value="none">None (Regular)</option>
                            <option value="senior">Senior Citizen (20%)</option>
                            <option value="pwd">PWD (20%)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Totals -->
                <div class="flex justify-between mb-1 text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span id="subtotalDisplay">₱0.00</span>
                </div>
                
                <div class="flex justify-between mb-2 text-sm text-red-900 font-bold">
                    <span>Discount</span>
                    <span id="discountDisplay">-₱0.00</span>
                </div>

                <div class="flex justify-between mb-4 text-xl font-bold text-black border-t pt-2">
                    <span>Total</span>
                    <span id="totalDisplay">₱0.00</span>
                </div>

                <button onclick="openReviewModal()" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-200 text-base">
                    Review & Process Order
                </button>
            </div>
        </div>

    </div>

    <!-- HELD / PARKED ORDERS MODAL -->
    <div id="heldOrdersModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden flex flex-col max-h-[80vh]">
            <div class="bg-black text-white px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Parked / Held Sales</h3>
                    <p class="text-xs text-gray-400">Select an order to recall back to cart</p>
                </div>
                <button onclick="closeHeldOrdersModal()" class="text-gray-400 hover:text-white text-sm">✕</button>
            </div>

            <div id="heldOrdersContainer" class="p-6 overflow-y-auto space-y-3">
                <!-- Held orders populated via JS -->
            </div>

            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 text-right">
                <button onclick="closeHeldOrdersModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- EMPTY CART WARNING MODAL -->
    <div id="emptyCartModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
            <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Cart is Empty</h3>
            <p class="text-gray-500 text-sm mb-6">Please select at least one menu item before performing this action.</p>
            <button onclick="closeEmptyCartModal()" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-xl transition text-sm shadow">
                Got it
            </button>
        </div>
    </div>

    <!-- ORDER CONFIRMATION MODAL -->
    <div id="reviewModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-black text-white px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Order Confirmation</h3>
                    <p class="text-xs text-gray-400">Please review items and enter cash details</p>
                </div>
                <span id="modalChannel" class="bg-red-900 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">Walk-in</span>
            </div>

            <div class="p-6 overflow-y-auto space-y-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ordered Items</h4>
                <div id="modalCartItems" class="bg-gray-50 rounded-lg p-3 border border-gray-200 space-y-2 max-h-48 overflow-y-auto"></div>

                <div class="border-t border-b border-gray-200 py-3 space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span id="modalSubtotal" class="font-medium">₱0.00</span>
                    </div>
                    <div class="flex justify-between text-red-900">
                        <span>Discount (<span id="modalDiscountType">None</span>):</span>
                        <span id="modalDiscount" class="font-bold">-₱0.00</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-black border-t pt-2">
                        <span>Grand Total:</span>
                        <span id="modalTotal" class="text-xl text-red-900">₱0.00</span>
                    </div>
                </div>

                <!-- Cash Calculator -->
                <div class="bg-gray-100 p-4 rounded-lg border border-gray-200 space-y-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Amount Received / Cash Tendered</label>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-200 text-gray-600 text-sm font-bold">₱</span>
                        <input type="number" id="amountTendered" oninput="calculateChange()" placeholder="0.00" step="0.01"
                               class="w-full text-lg font-bold p-2 border border-gray-300 rounded-r focus:outline-none focus:ring-2 focus:ring-red-900">
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="setExactAmount()" class="flex-1 bg-white hover:bg-gray-200 border text-xs font-bold py-1.5 px-2 rounded transition">Exact</button>
                        <button type="button" onclick="addQuickCash(100)" class="flex-1 bg-white hover:bg-gray-200 border text-xs font-bold py-1.5 px-2 rounded transition">+₱100</button>
                        <button type="button" onclick="addQuickCash(500)" class="flex-1 bg-white hover:bg-gray-200 border text-xs font-bold py-1.5 px-2 rounded transition">+₱500</button>
                        <button type="button" onclick="addQuickCash(1000)" class="flex-1 bg-white hover:bg-gray-200 border text-xs font-bold py-1.5 px-2 rounded transition">+₱1000</button>
                        <button type="button" onclick="clearCash()" class="bg-red-100 hover:bg-red-200 text-red-900 border border-red-300 text-xs font-bold py-1.5 px-3 rounded transition">Clear</button>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-700">Change:</span>
                        <span id="changeDisplay" class="text-lg font-bold text-gray-400">₱0.00</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex space-x-3 border-t border-gray-200">
                <button type="button" onclick="closeReviewModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2.5 px-4 rounded-lg transition text-sm">
                    ← Back / Edit Order
                </button>
                <button type="button" id="confirmSubmitBtn" onclick="confirmAndSubmitOrder()" class="w-1/2 bg-red-900 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm shadow opacity-50 cursor-not-allowed" disabled>
                    Confirm & Pay
                </button>
            </div>
        </div>
    </div>

    <!-- PRINTING RECEIPT MODAL -->
    <div id="printingModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center overflow-hidden flex flex-col items-center">
            <div class="relative w-16 h-16 mb-3 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-red-900/20 border-t-red-900 animate-spin"></div>
                <svg class="w-8 h-8 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h14z"></path>
                </svg>
            </div>

            <h3 class="text-lg font-black text-gray-900 mb-1">Printing Thermal Receipt...</h3>
            <p class="text-xs text-gray-400 mb-4">Please wait standard printing cycle</p>

            <div class="w-full bg-slate-50 border border-gray-200 rounded-lg p-4 text-left font-mono text-xs text-gray-700 space-y-2 shadow-inner max-h-60 overflow-y-auto">
                <div class="text-center border-b border-gray-300 pb-2">
                    <p class="font-bold text-sm text-black uppercase tracking-wider">PBWSIS POS</p>
                    <p class="text-[10px] text-gray-500">Official Receipt Preview</p>
                    <p id="receiptDate" class="text-[10px] text-gray-400 mt-1"></p>
                </div>

                <div id="receiptItemsList" class="space-y-1 py-1 border-b border-dashed border-gray-300"></div>

                <div class="space-y-1 text-[11px] pt-1">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span id="receiptSubtotal">₱0.00</span>
                    </div>
                    <div class="flex justify-between text-red-800">
                        <span>Discount:</span>
                        <span id="receiptDiscount">-₱0.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-black border-t border-gray-300 pt-1 text-xs">
                        <span>TOTAL:</span>
                        <span id="receiptTotal">₱0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-500 pt-1">
                        <span>Tendered:</span>
                        <span id="receiptTendered">₱0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Change:</span>
                        <span id="receiptChange">₱0.00</span>
                    </div>
                </div>

                <div class="text-center border-t border-gray-300 pt-2 text-[10px] text-gray-400">
                    Thank you for your purchase!
                </div>
            </div>
        </div>
    </div>

    <!-- THANK YOU / SUCCESS MODAL -->
    <div id="thankYouModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h3 class="text-2xl font-black text-gray-900 mb-1">Thank You!</h3>
            <p class="text-gray-500 text-sm mb-5">Order has been processed successfully.</p>

            <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200 text-left space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Amount:</span>
                    <span id="thankYouTotal" class="font-bold text-gray-800">₱0.00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Cash Received:</span>
                    <span id="thankYouTendered" class="font-bold text-gray-800">₱0.00</span>
                </div>
                <div class="flex justify-between text-base border-t border-gray-200 pt-2">
                    <span class="font-bold text-gray-800">Change Due:</span>
                    <span id="thankYouChange" class="font-bold text-green-700 text-xl">₱0.00</span>
                </div>
            </div>

            <button onclick="closeThankYouModal()" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-3 px-4 rounded-xl transition text-base shadow-lg">
                Start Next Order (<span id="countdownText">5</span>s)
            </button>
        </div>
    </div>

    <!-- JavaScript Logic -->
    <script>
        let cart = [];
        let heldOrders = JSON.parse(localStorage.getItem('pbwsis_held_orders')) || [];
        let currentCategory = 'all';
        let thankYouTimer = null;
        let countdownValue = 5;

        // Initialize badges on page load
        document.addEventListener("DOMContentLoaded", () => {
            updateHeldCount();
        });

        // --- SEARCH & CATEGORY FILTER LOGIC ---

        function setCategory(category, btnElement) {
            currentCategory = category;

            // Update UI buttons styling
            document.querySelectorAll('.cat-btn').forEach(btn => {
                btn.className = "cat-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap";
            });
            btnElement.className = "cat-btn bg-red-900 text-white text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap";

            filterProducts();
        }

        function filterProducts() {
    const query = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.product-card');

    // Helper function to remove spaces & special characters for clean matching
    const sanitize = (str) => (str || '').toLowerCase().replace(/[^a-z0-9]/g, '');

    cards.forEach(card => {
        const rawName = card.getAttribute('data-name') || '';
        const rawCategory = card.getAttribute('data-category') || '';

        const cleanName = sanitize(rawName);
        const cleanCategory = sanitize(rawCategory);
        const cleanSelectedCat = sanitize(currentCategory);

        let matchesCategory = false;

        if (currentCategory === 'all') {
            matchesCategory = true;
        } else if (cleanSelectedCat === 'chicken') {
            // Matches category OR item name containing 'chicken' or 'wings'
            matchesCategory = cleanCategory.includes('chicken') || cleanName.includes('chicken') || cleanName.includes('wings');
        } else {
            // Ignores spaces (e.g. 'milktea' will match 'milk tea')
            matchesCategory = cleanCategory.includes(cleanSelectedCat) || cleanName.includes(cleanSelectedCat);
        }

        const cleanQuery = sanitize(query);
        const matchesSearch = cleanName.includes(cleanQuery);

        if (matchesCategory && matchesSearch) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

        // --- CART & STOCK MANAGEMENT ---

        function addToCart(element) {
            const id = element.getAttribute('data-id');
            const name = element.getAttribute('data-name');
            const price = parseFloat(element.getAttribute('data-price'));
            const maxStock = parseInt(element.getAttribute('data-stock')) || 0;

            const existingItem = cart.find(item => item.id === id);
            const currentCartQty = existingItem ? existingItem.quantity : 0;

            if (currentCartQty >= maxStock) return;

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id, name, price, quantity: 1, maxStock: maxStock });
            }

            updateStockDisplay(id);
            updateCartUI();
        }

        function updateQuantity(index, delta) {
            const item = cart[index];
            const newQty = item.quantity + delta;

            if (newQty > item.maxStock) return;

            if (newQty <= 0) {
                removeFromCart(index);
                return;
            }

            item.quantity = newQty;
            updateStockDisplay(item.id);
            updateCartUI();
        }

        function removeFromCart(index) {
            const item = cart[index];
            const productId = item.id;
            
            cart.splice(index, 1);
            
            updateStockDisplay(productId);
            updateCartUI();
        }

        function updateStockDisplay(productId) {
            const card = document.getElementById(`product-card-${productId}`);
            if (!card) return;

            const maxStock = parseInt(card.getAttribute('data-stock')) || 0;
            const cartItem = cart.find(item => item.id === productId);
            const cartQty = cartItem ? cartItem.quantity : 0;
            const remainingStock = maxStock - cartQty;

            const stockValElement = document.getElementById(`stock-val-${productId}`);
            if (stockValElement) {
                stockValElement.innerText = remainingStock;
            }

            if (remainingStock <= 0) {
                card.classList.add('opacity-40', 'cursor-not-allowed');
            } else {
                card.classList.remove('opacity-40', 'cursor-not-allowed');
            }
        }

        function updateCartUI() {
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center mt-10 text-sm">Cart is currently empty. Click an item to add it.</p>';
                updateTotals();
                return;
            }

            cart.forEach((item, index) => {
                const itemHTML = `
                    <div class="flex justify-between items-center bg-white p-3 rounded shadow-sm border border-gray-200">
                        <div class="flex-1 min-w-0 mr-2">
                            <h4 class="text-sm font-bold text-gray-800 truncate">${item.name}</h4>
                            <div class="text-xs text-gray-500">₱${item.price.toFixed(2)} each</div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex items-center border border-gray-300 rounded bg-gray-50">
                                <button onclick="updateQuantity(${index}, -1)" class="w-7 h-7 flex items-center justify-center text-sm font-bold text-gray-600 hover:bg-gray-200 active:bg-gray-300 rounded-l transition">-</button>
                                <span class="px-2.5 text-xs font-bold text-gray-800">${item.quantity}</span>
                                <button onclick="updateQuantity(${index}, 1)" class="w-7 h-7 flex items-center justify-center text-sm font-bold text-gray-600 hover:bg-gray-200 active:bg-gray-300 rounded-r transition ${item.quantity >= item.maxStock ? 'opacity-40 cursor-not-allowed' : ''}">+</button>
                            </div>

                            <div class="font-bold text-red-900 w-16 text-right text-sm">
                                ₱${(item.price * item.quantity).toFixed(2)}
                            </div>

                            <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-600 transition p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                `;
                container.innerHTML += itemHTML;
            });

            updateTotals();
        }

        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discountSelect = document.getElementById('discountType');
            let discountRate = (discountSelect && (discountSelect.value === 'senior' || discountSelect.value === 'pwd')) ? 0.20 : 0;

            const discount = subtotal * discountRate;
            const total = subtotal - discount;

            document.getElementById('subtotalDisplay').innerText = '₱' + subtotal.toFixed(2);
            document.getElementById('discountDisplay').innerText = '-₱' + discount.toFixed(2);
            document.getElementById('totalDisplay').innerText = '₱' + total.toFixed(2);
        }

        // --- PARKED SALES / HOLD ORDER LOGIC ---

        function updateHeldCount() {
            const badge = document.getElementById('heldCountBadge');
            if (badge) badge.innerText = heldOrders.length;
        }

        function holdCurrentOrder() {
            if (cart.length === 0) {
                showEmptyCartModal();
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discountSelect = document.getElementById('discountType').value;
            let discountRate = (discountSelect === 'senior' || discountSelect === 'pwd') ? 0.20 : 0;
            const total = subtotal - (subtotal * discountRate);

            const heldSale = {
                id: 'HOLD-' + Date.now().toString().slice(-4),
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                items: [...cart],
                channel: document.getElementById('orderChannel').value,
                discount: discountSelect,
                total: total
            };

            heldOrders.push(heldSale);
            localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));

            // Reset current cart
            cart = [];
            updateCartUI();

            // Refresh cards stock display
            document.querySelectorAll('.product-card').forEach(card => {
                updateStockDisplay(card.getAttribute('data-id'));
            });

            updateHeldCount();
        }

       function openHeldOrdersModal() {
    const container = document.getElementById('heldOrdersContainer');
    container.innerHTML = '';

    if (heldOrders.length === 0) {
        container.innerHTML = '<p class="text-gray-400 text-center py-8 text-sm">No parked orders found.</p>';
    } else {
        heldOrders.forEach((order, index) => {
            const itemsSummary = order.items.map(i => `${i.quantity}x ${i.name}`).join(', ');
            container.innerHTML += `
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 flex items-center justify-between gap-3 shadow-sm">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-gray-900 text-xs">${order.id}</span>
                            <span class="text-[10px] text-gray-400">${order.timestamp}</span>
                            <span class="bg-gray-200 text-gray-700 text-[10px] px-1.5 py-0.5 rounded uppercase font-semibold">${order.channel}</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate mt-1">${itemsSummary}</p>
                        <p class="text-xs font-bold text-red-900 mt-0.5">₱${order.total.toFixed(2)}</p>
                    </div>
                    <div class="flex items-center space-x-1.5 shrink-0">
                        <button onclick="recallOrder(${index})" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm">Recall</button>
                        <button onclick="deleteHeldOrder(${index})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold w-7 h-7 rounded-lg transition flex items-center justify-center">✕</button>
                    </div>
                </div>
            `;
        });
    }

    document.getElementById('heldOrdersModal').classList.remove('hidden');
}

        function recallOrder(index) {
            if (cart.length > 0) {
                if (!confirm("Recalling this order will replace your current active cart. Continue?")) {
                    return;
                }
            }

            const selectedOrder = heldOrders[index];
            cart = [...selectedOrder.items];

            document.getElementById('orderChannel').value = selectedOrder.channel || 'Walk-in';
            document.getElementById('discountType').value = selectedOrder.discount || 'none';

            // Remove from held list
            heldOrders.splice(index, 1);
            localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));
            updateHeldCount();

            // Refresh cards stock display & cart UI
            document.querySelectorAll('.product-card').forEach(card => {
                updateStockDisplay(card.getAttribute('data-id'));
            });

            updateCartUI();
            closeHeldOrdersModal();
        }

        function deleteHeldOrder(index) {
            heldOrders.splice(index, 1);
            localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));
            updateHeldCount();
            openHeldOrdersModal();
        }

        function closeHeldOrdersModal() {
            document.getElementById('heldOrdersModal').classList.add('hidden');
        }

        // --- EMPTY CART MODAL ---

        function showEmptyCartModal() {
            document.getElementById('emptyCartModal').classList.remove('hidden');
        }

        function closeEmptyCartModal() {
            document.getElementById('emptyCartModal').classList.add('hidden');
        }

        // --- REVIEW & CHECKOUT MODAL ---

        function openReviewModal() {
            if (cart.length === 0) {
                showEmptyCartModal();
                return;
            }

            const selectedChannel = document.getElementById('orderChannel').value;
            const discountSelect = document.getElementById('discountType');
            const discountText = discountSelect.options[discountSelect.selectedIndex].text;

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let discountRate = (discountSelect.value === 'senior' || discountSelect.value === 'pwd') ? 0.20 : 0;
            const discountAmount = subtotal * discountRate;
            const finalTotal = subtotal - discountAmount;

            const modalItems = document.getElementById('modalCartItems');
            modalItems.innerHTML = cart.map(item => `
                <div class="flex justify-between items-center text-sm py-1 border-b border-gray-100 last:border-0">
                    <div>
                        <span class="font-bold text-gray-800">${item.name}</span>
                        <div class="text-xs text-gray-500">₱${item.price.toFixed(2)} × ${item.quantity}</div>
                    </div>
                    <div class="font-bold text-gray-900">₱${(item.price * item.quantity).toFixed(2)}</div>
                </div>
            `).join('');

            document.getElementById('modalChannel').innerText = selectedChannel;
            document.getElementById('modalDiscountType').innerText = discountText;
            document.getElementById('modalSubtotal').innerText = '₱' + subtotal.toFixed(2);
            document.getElementById('modalDiscount').innerText = '-₱' + discountAmount.toFixed(2);
            document.getElementById('modalTotal').innerText = '₱' + finalTotal.toFixed(2);

            document.getElementById('amountTendered').value = '';
            calculateChange(finalTotal);

            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }

        function calculateChange(overrideTotal) {
            const discountSelect = document.getElementById('discountType').value;
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let discountRate = (discountSelect === 'senior' || discountSelect === 'pwd') ? 0.20 : 0;
            const total = overrideTotal !== undefined ? overrideTotal : (subtotal - (subtotal * discountRate));

            const tenderedInput = document.getElementById('amountTendered');
            const tendered = parseFloat(tenderedInput.value) || 0;
            const change = tendered - total;

            const changeDisplay = document.getElementById('changeDisplay');
            const submitBtn = document.getElementById('confirmSubmitBtn');

            if (tendered < total) {
                changeDisplay.innerText = "Insufficient Cash";
                changeDisplay.className = "text-sm font-bold text-red-600";
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                changeDisplay.innerText = "₱" + change.toFixed(2);
                changeDisplay.className = "text-lg font-bold text-green-700";
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function addQuickCash(amount) {
            const input = document.getElementById('amountTendered');
            const currentVal = parseFloat(input.value) || 0;
            input.value = (currentVal + amount).toFixed(2);
            calculateChange();
        }

        function clearCash() {
            document.getElementById('amountTendered').value = '';
            calculateChange();
        }

        function setExactAmount() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discountSelect = document.getElementById('discountType').value;
            let discountRate = (discountSelect === 'senior' || discountSelect === 'pwd') ? 0.20 : 0;
            const total = subtotal - (subtotal * discountRate);

            document.getElementById('amountTendered').value = total.toFixed(2);
            calculateChange();
        }

        async function confirmAndSubmitOrder() {
            const selectedChannel = document.getElementById('orderChannel').value;
            const selectedDiscount = document.getElementById('discountType').value;
            
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let discountRate = (selectedDiscount === 'senior' || selectedDiscount === 'pwd') ? 0.20 : 0;
            const discountAmount = subtotal * discountRate;
            const finalTotal = subtotal - discountAmount;
            const amountTendered = parseFloat(document.getElementById('amountTendered').value) || 0;

            const orderPayload = {
                channel: selectedChannel,
                discount_type: selectedDiscount,
                discount_amount: discountAmount,
                total_amount: finalTotal,
                amount_tendered: amountTendered,
                change_amount: amountTendered - finalTotal,
                items: cart
            };

            const submitBtn = document.getElementById('confirmSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = "Processing...";

            try {
                const response = await fetch("{{ route('pos.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(orderPayload)
                });

                const result = await response.json();

                if (response.ok) {
                    closeReviewModal();
                    
                    cart.forEach(item => {
                        const card = document.getElementById(`product-card-${item.id}`);
                        if (card) {
                            const currentMax = parseInt(card.getAttribute('data-stock')) || 0;
                            const newMax = Math.max(0, currentMax - item.quantity);
                            card.setAttribute('data-stock', newMax);
                        }
                    });

                    const soldItems = [...cart];
                    showPrintingModal(subtotal, discountAmount, finalTotal, amountTendered, amountTendered - finalTotal, soldItems);

                    cart = [];
                    updateCartUI();
                    soldItems.forEach(item => updateStockDisplay(item.id));

                } else {
                    alert("Error: " + (result.error || "Failed to process order"));
                }
            } catch (error) {
                console.error("Fetch error:", error);
                alert("A network error occurred.");
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = "Confirm & Pay";
            }
        }

        // --- PRINTING RECEIPT MODAL LOGIC ---

        function showPrintingModal(subtotal, discount, total, tendered, change, items) {
            document.getElementById('receiptDate').innerText = new Date().toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });

            const itemsContainer = document.getElementById('receiptItemsList');
            itemsContainer.innerHTML = items.map(item => `
                <div class="flex justify-between">
                    <span class="truncate pr-2">${item.quantity}x ${item.name}</span>
                    <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `).join('');

            document.getElementById('receiptSubtotal').innerText = '₱' + subtotal.toFixed(2);
            document.getElementById('receiptDiscount').innerText = '-₱' + discount.toFixed(2);
            document.getElementById('receiptTotal').innerText = '₱' + total.toFixed(2);
            document.getElementById('receiptTendered').innerText = '₱' + tendered.toFixed(2);
            document.getElementById('receiptChange').innerText = '₱' + change.toFixed(2);

            document.getElementById('printingModal').classList.remove('hidden');

            setTimeout(() => {
                document.getElementById('printingModal').classList.add('hidden');
                showThankYouModal(total, tendered, change);
            }, 2500);
        }

        // --- THANK YOU MODAL TIMER LOGIC ---

        function showThankYouModal(total, tendered, change) {
            document.getElementById('thankYouTotal').innerText = '₱' + total.toFixed(2);
            document.getElementById('thankYouTendered').innerText = '₱' + tendered.toFixed(2);
            document.getElementById('thankYouChange').innerText = '₱' + change.toFixed(2);

            document.getElementById('thankYouModal').classList.remove('hidden');

            countdownValue = 5;
            document.getElementById('countdownText').innerText = countdownValue;

            if (thankYouTimer) clearInterval(thankYouTimer);

            thankYouTimer = setInterval(() => {
                countdownValue--;
                if (countdownValue <= 0) {
                    closeThankYouModal();
                } else {
                    document.getElementById('countdownText').innerText = countdownValue;
                }
            }, 1000);
        }

        function closeThankYouModal() {
            if (thankYouTimer) clearInterval(thankYouTimer);
            document.getElementById('thankYouModal').classList.add('hidden');
        }
    </script>
</body>
</html>