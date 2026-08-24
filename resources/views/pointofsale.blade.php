<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS - POS Terminal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Thermal Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printableReceipt, #printableReceipt * {
                visibility: visible;
            }
            #printableReceipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                background: white !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<script>
    // Expose Laravel VAT variable globally to window
    window.vatConfig = @json($vat);
</script>

<!-- Load external JS -->
@vite(['resources/js/pos.js'])
<body class="bg-gray-100 text-gray-800 font-sans h-screen flex flex-col overflow-hidden">

    <!-- POS Top Navigation -->
    <nav class="bg-black text-white p-4 shadow-md shrink-0 no-print">
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
    <div class="flex-1 flex overflow-hidden no-print w-full">
        
        <!-- Left Side: Product Grid + Search/Categories -->
        <div class="w-3/5 p-6 bg-gray-100 flex flex-col h-full overflow-hidden">
            
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
                <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($products as $product)
                        <!-- Product Card -->
                        <div id="product-card-{{ $product->product_id }}"
                             class="product-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-md hover:border-red-900 transition duration-200 select-none flex flex-col justify-between" 
                             data-id="{{ $product->product_id }}" 
                             data-name="{{ $product->product_name }}" 
                             data-category="{{ strtolower($product->category_name ?? $product->category ?? '') }}"
                             data-price="{{ $product->price }}"
                             onclick="addToCart(this)">
                             
                            <!-- Product Image Container -->
                            <div class="h-28 bg-gray-100 rounded-md mb-3 flex items-center justify-center overflow-hidden">
                                @if(!empty($product->image) || !empty($product->image_path))
                                    <img src="{{ asset('storage/' . ($product->image ?? $product->image_path)) }}" 
                                         alt="{{ $product->product_name }}" 
                                         class="w-full h-full object-cover rounded-md"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'flex items-center justify-center w-full h-full text-gray-400\'><svg class=\'w-8 h-8\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                @else
                                    <div class="flex items-center justify-center w-full h-full text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-gray-800 truncate">{{ $product->product_name }}</h3>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-red-900 font-bold">₱{{ number_format($product->price, 2) }}</span>
                                </div>
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
        <div class="w-2/5 bg-white border-l border-gray-200 shadow-xl flex flex-col h-full shrink-0">
            <div class="p-4 bg-black text-white flex justify-between items-center shrink-0">
                <h2 class="font-bold text-lg">Current Order</h2>
                
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="holdCurrentOrder()" class="bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold py-1 px-3 rounded transition shadow">
                        Hold
                    </button>
                    <button type="button" onclick="openHeldOrdersModal()" class="bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold py-1 px-3 rounded border border-gray-700 transition flex items-center space-x-1">
                        <span>Hold Ordered</span>
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
                <div class="space-y-3">
                    <!-- Order Type Selector -->
                    <div class="flex items-center justify-between bg-gray-100 p-1.5 rounded-lg border border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase px-1">Order Type:</span>
                        <select id="orderChannel" onchange="updateOrderChannel(this.value)" class="text-xs font-bold bg-white text-gray-800 border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-red-900 cursor-pointer">
                            <option value="Dine-in">Dine-in</option>
                            <option value="Take-out">Take-out</option>
                        </select>
                    </div>

                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay" class="font-semibold text-gray-800">₱0.00</span>
                        </div>

                        <div class="flex justify-between text-red-900 font-medium">
                            <span>Discount</span>
                            <span id="discountDisplay">-₱0.00</span>
                        </div>

                        <div class="flex justify-between text-sm text-gray-500 py-1">
                            <span>VAT ({{ ($vat->is_enabled ?? $vat->is_active ?? true) ? number_format($vat->rate, 2) . '% ' . ($vat->is_inclusive ? 'Incl.' : 'Excl.') : 'Disabled' }}):</span>
                            <span id="vatDisplay">₱0.00</span>
                        </div>

                        <div class="flex justify-between text-lg font-bold text-red-900 border-t pt-2 mt-1">
                            <span>Grand Total:</span>
                            <span id="grandTotalDisplay">₱0.00</span>
                        </div>
                    </div>

                    <button type="button" onclick="openReviewModal()" 
                            class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-3 px-4 rounded-xl transition text-sm shadow-md flex items-center justify-center gap-2">
                        <span>Review & Process Order</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- HOLD ORDER REFERENCE MODAL -->
    <div id="holdOrderModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
            <div class="w-12 h-12 bg-red-100 text-red-900 rounded-full flex items-center justify-center mx-auto mb-3 font-bold text-xl">
                🏷️
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-1">Hold Order</h3>
            <p class="text-xs text-gray-500 mb-4">Enter a Table Number or Customer Name to identify this order.</p>
            <input type="text" id="holdReferenceInput" placeholder="e.g., Table 4 or Juan" 
                   class="w-full text-sm p-3 border border-gray-300 rounded-xl mb-4 focus:outline-none focus:ring-2 focus:ring-red-900 bg-gray-50 font-medium">
            <div class="flex gap-2">
                <button type="button" onclick="closeHoldModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 px-4 rounded-xl text-xs transition">
                    Cancel
                </button>
                <button type="button" onclick="confirmHoldOrder()" class="flex-1 bg-red-900 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition shadow">
                    Save & Hold
                </button>
            </div>
        </div>
    </div>

    <!-- HELD / PARKED ORDERS MODAL -->
    <div id="heldOrdersModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 no-print">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden flex flex-col max-h-[80vh]">
            <div class="bg-black text-white px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Hold Ordered</h3>
                    <p class="text-xs text-gray-400">Select an order to recall back to cart</p>
                </div>
                <button onclick="closeHeldOrdersModal()" class="text-gray-400 hover:text-white text-sm">✕</button>
            </div>
            <div id="heldOrdersContainer" class="p-6 overflow-y-auto space-y-3"></div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 text-right">
                <button onclick="closeHeldOrdersModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- EMPTY CART WARNING MODAL -->
    <div id="emptyCartModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 no-print">
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
    <div id="reviewModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 no-print">
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
                    <div class="flex justify-between text-xs py-1">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="modalSubtotal" class="font-bold text-gray-800">₱0.00</span>
                    </div>

                    <div class="flex justify-between text-xs py-1">
                        <span class="text-red-600">Discount:</span>
                        <span id="modalDiscount" class="font-bold text-red-600">-₱0.00</span>
                    </div>

                    <div class="flex justify-between text-xs py-1">
                        <span class="text-gray-500">VAT:</span>
                        <span id="modalVatDisplay" class="font-bold text-gray-700">₱0.00</span>
                    </div>

                    <div class="flex justify-between text-sm font-bold border-t border-gray-200 pt-2 mt-1">
                        <span>Grand Total:</span>
                        <span id="modalTotal" class="text-red-900">₱0.00</span>
                    </div>
                </div>

                <!-- Cash Calculator -->
                <div class="bg-gray-100 p-4 rounded-lg border border-gray-200 space-y-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Amount Received / Cash Tendered</label>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-200 text-gray-600 text-sm font-bold">₱</span>
                        <input type="number" id="amountTendered" oninput="calculateChange()" placeholder="0.00" step="0.01" min="0" max="100000" autofocus
                               class="w-full text-lg font-bold p-2 border border-gray-300 rounded-r focus:outline-none focus:ring-2 focus:ring-red-900 bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
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
                <button type="button" id="confirmSubmitBtn" onclick="processOrder()" 
                        class="w-full py-2.5 bg-red-900 text-white font-bold rounded-lg hover:bg-red-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Confirm & Pay
                </button>
            </div>
        </div>
    </div>

    <!-- PRINTING RECEIPT MODAL -->
    <div id="printingModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center overflow-hidden flex flex-col items-center">
            <h3 class="text-lg font-black text-gray-900 mb-1 no-print">Receipt Preview</h3>
            <p class="text-xs text-gray-400 mb-4 no-print">Review official receipt before printing</p>

            <div id="printableReceipt" class="w-full bg-slate-50 border border-gray-200 rounded-lg p-4 text-left font-mono text-xs text-gray-700 space-y-2 shadow-inner max-h-72 overflow-y-auto">
                <div class="text-center border-b border-gray-300 pb-2">
                    <p class="font-bold text-sm text-black uppercase tracking-wider">PBWSIS POS</p>
                    <p class="text-[10px] text-gray-500">Official Receipt Preview</p>
                    <p id="receiptDate" class="text-[10px] text-gray-400 mt-1"></p>
                </div>

                <div id="receiptItemsList" class="space-y-1 py-1 border-b border-dashed border-gray-300"></div>

                <div class="space-y-1 text-[11px] pt-1">
                    <div class="flex justify-between text-xs py-0.5">
                        <span>Subtotal:</span>
                        <span id="receiptSubtotal">₱0.00</span>
                    </div>

                    <div class="flex justify-between text-xs text-red-600 py-0.5">
                        <span>Discount:</span>
                        <span id="receiptDiscount">-₱0.00</span>
                    </div>

                    <div class="flex justify-between text-xs text-gray-500 py-0.5">
                        <span>VAT (12% Incl.):</span>
                        <span id="receiptVat">₱0.00</span>
                    </div>

                    <div class="flex justify-between text-xs font-bold border-t border-dashed border-gray-300 pt-1 mt-1">
                        <span>TOTAL:</span>
                        <span id="receiptTotal">₱0.00</span>
                    </div>
                </div>

                <div class="text-center border-t border-gray-300 pt-2 text-[10px] text-gray-400">
                    Thank you for your purchase!
                </div>
            </div>

            <div class="w-full mt-4 flex gap-2 no-print">
                <button type="button" onclick="printReceipt()" class="flex-1 bg-black hover:bg-gray-800 text-white font-bold py-2.5 px-3 rounded-xl transition text-xs flex items-center justify-center gap-1.5 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h14z"></path></svg>
                    Print Receipt
                </button>
                <button type="button" onclick="finishPrinting()" class="flex-1 bg-red-900 hover:bg-red-800 text-white font-bold py-2.5 px-3 rounded-xl transition text-xs shadow">
                    Done / Next →
                </button>
            </div>
        </div>
    </div>

    <!-- THANK YOU / SUCCESS MODAL -->
    <div id="thankYouModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 no-print">
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
                Start Next Order
            </button>
        </div>
    </div>

    <!-- PASS BLADE DISCOUNTS & CONFIG DIRECTLY TO JS -->
    <script>
        window.VAT_CONFIG = {
            rate: {{ $vat->is_active ? ($vat->rate / 100) : 0 }},
            isInclusive: {{ $vat->is_inclusive ? 'true' : 'false' }},
            isActive: {{ $vat->is_active ? 'true' : 'false' }}
        };

        @if(isset($discounts))
            window.availableDiscounts = [
                @foreach($discounts as $d)
                {
                    id: '{{ $d->id ?? $d->discount_id ?? $d->name }}',
                    name: '{{ $d->name }}',
                    rate: {{ $d->value ?? $d->percentage ?? $d->rate ?? 0 }}
                },
                @endforeach
            ];
        @endif
    </script>
</body>
</html>