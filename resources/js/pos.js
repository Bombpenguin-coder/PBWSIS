let cart = [];
let heldOrders = JSON.parse(localStorage.getItem('pbwsis_held_orders')) || [];
let currentCategory = 'all';

// Default fallback discounts in case API is not yet configured
window.availableDiscounts = window.availableDiscounts || [
    { id: 'sc_pwd', name: 'SC/PWD', rate: 20 },
    { id: 'employee', name: 'Employee', rate: 15 },
    { id: 'promo', name: 'Promo', rate: 10 }
];

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    updateHeldCount();
    fetchActiveDiscounts();
});

// --- FETCH DISCOUNTS FROM BACKEND ---

async function fetchActiveDiscounts() {
    try {
        const response = await fetch('/discounts/active', {
            headers: { 'Accept': 'application/json' }
        });
        if (response.ok) {
            const data = await response.json();
            if (Array.isArray(data) && data.length > 0) {
                // Map database fields (e.g. name, percentage) to standardized format
                window.availableDiscounts = data.map(d => ({
                    id: d.id || d.slug || d.name.toLowerCase().replace(/\s+/g, '_'),
                    name: d.name,
                    rate: parseFloat(d.percentage || d.value || d.rate || 0)
                }));
                
                populateGlobalDiscountDropdown(); // Populate static/global UI dropdowns
                updateCartUI(); // Re-render cart with new dynamic discount options
            }
        }
    } catch (err) {
        console.warn("Could not load dynamic discounts, using current options:", err);
    }
}

// Populate global/static select elements (like #discountSelect in pointofsale.blade.php)
function populateGlobalDiscountDropdown() {
    const globalSelect = document.getElementById('discountSelect');
    if (!globalSelect) return;

    const options = window.availableDiscounts.map(d => `
        <option value="${d.id}" data-value="${d.rate}">${d.name} (${d.rate}%)</option>
    `).join('');

    globalSelect.innerHTML = `<option value="none" data-value="0">No Discount</option>` + options;
}

// --- SEARCH & CATEGORY FILTER LOGIC ---

function setCategory(category, btnElement) {
    currentCategory = category;

    document.querySelectorAll('.cat-btn').forEach(btn => {
        btn.className = "cat-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap";
    });
    btnElement.className = "cat-btn bg-red-900 text-white text-xs font-bold py-1.5 px-4 rounded-full transition shadow-sm whitespace-nowrap";

    filterProducts();
}

function filterProducts() {
    const query = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    const cards = document.querySelectorAll('.product-card');

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
            matchesCategory = cleanCategory.includes('chicken') || cleanName.includes('chicken') || cleanName.includes('wings');
        } else {
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

// --- CART & DISCOUNT STEPPER LOGIC ---

function addToCart(element) {
    if (!element) return;

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
        cart.push({ 
            id: id, 
            name: name, 
            price: price, 
            quantity: 1, 
            maxStock: maxStock,
            discountType: 'none',
            discountRate: 0,
            discountedQty: 0
        });
    }

    updateStockDisplay(id);
    updateCartUI();
}

function updateItemDiscountType(index, selectElement) {
    const item = cart[index];
    if (!item) return;

    const selectedValue = selectElement.value;

    if (selectedValue === 'none') {
        item.discountType = 'none';
        item.discountRate = 0;
        item.discountedQty = 0;
    } else {
        const foundDiscount = window.availableDiscounts.find(d => String(d.id) === String(selectedValue));
        const rate = foundDiscount ? foundDiscount.rate : 0;

        item.discountType = selectedValue;
        item.discountRate = rate / 100;
        item.discountedQty = item.discountedQty > 0 ? item.discountedQty : 1; 
    }

    updateCartUI();
}

function updateItemDiscountQty(index, delta) {
    const item = cart[index];
    if (!item) return;

    let currentDiscounted = item.discountedQty || 0;
    let newDiscounted = currentDiscounted + delta;

    if (newDiscounted < 0) newDiscounted = 0;
    if (newDiscounted > item.quantity) newDiscounted = item.quantity;

    item.discountedQty = newDiscounted;
    updateCartUI();
}

function updateQuantity(index, delta) {
    const item = cart[index];
    if (!item) return;

    const newQty = item.quantity + delta;

    if (newQty > item.maxStock) return;

    if (newQty <= 0) {
        removeFromCart(index);
        return;
    }

    item.quantity = newQty;
    
    if ((item.discountedQty || 0) > item.quantity) {
        item.discountedQty = item.quantity;
    }

    updateStockDisplay(item.id);
    updateCartUI();
}

function removeFromCart(index) {
    const item = cart[index];
    if (!item) return;
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
    if (!container) return;
    
    container.innerHTML = '';

    if (cart.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center mt-10 text-sm">Cart is currently empty. Click an item to add it.</p>';
        updateTotals();
        return;
    }

    cart.forEach((item, index) => {
        const itemSubtotal = item.price * item.quantity;
        const discountedUnits = item.discountedQty || 0;
        const itemDiscount = discountedUnits * (item.price * (item.discountRate || 0));
        const itemFinalPrice = itemSubtotal - itemDiscount;
        const hasDiscount = item.discountType && item.discountType !== 'none';

        // Build dynamic <option> list from database array
        const dynamicOptionsHTML = window.availableDiscounts.map(d => `
            <option value="${d.id}" data-value="${d.rate}" ${String(item.discountType) === String(d.id) ? 'selected' : ''}>
                ${d.name} (${d.rate}%)
            </option>
        `).join('');

        const itemHTML = `
            <div class="bg-white p-3 rounded shadow-sm border border-gray-200 space-y-2">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 mr-2 flex-1">
                        <h4 class="text-sm font-bold text-gray-800 truncate">${item.name}</h4>
                        <div class="text-xs text-gray-500">₱${item.price.toFixed(2)} each ${item.quantity > 1 ? `(x${item.quantity})` : ''}</div>
                        
                        <!-- DYNAMIC DISCOUNT SELECTOR UNDER PRODUCT NAME -->
                        <div class="mt-1.5">
                            <select onchange="updateItemDiscountType(${index}, this)" class="text-xs border border-gray-300 rounded px-1.5 py-1 bg-gray-50 text-gray-700 font-medium focus:outline-none focus:ring-1 focus:ring-red-900">
                                <option value="none" data-value="0" ${item.discountType === 'none' ? 'selected' : ''}>No Discount</option>
                                ${dynamicOptionsHTML}
                            </select>
                        </div>
                    </div>

                    <div class="font-bold ${hasDiscount && discountedUnits > 0 ? 'text-red-900' : 'text-gray-800'} text-right text-sm">
                        ₱${itemFinalPrice.toFixed(2)}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <!-- DISCOUNT STEPPER -->
                    <div>
                        ${hasDiscount ? `
                            <div class="flex items-center gap-1 bg-red-50 border border-red-200 px-2 py-0.5 rounded-lg">
                                <button type="button" onclick="updateItemDiscountQty(${index}, -1)" class="w-5 h-5 bg-white border border-red-300 rounded font-black text-red-900 hover:bg-red-200 flex items-center justify-center text-xs shadow-sm">-</button>
                                <span class="text-xs font-bold text-red-900 px-1 min-w-[35px] text-center">${discountedUnits}/${item.quantity}</span>
                                <button type="button" onclick="updateItemDiscountQty(${index}, 1)" class="w-5 h-5 bg-white border border-red-300 rounded font-black text-red-900 hover:bg-red-200 flex items-center justify-center text-xs shadow-sm">+</button>
                            </div>
                        ` : ''}
                    </div>

                    <!-- QUANTITY ADJUSTMENT & REMOVE -->
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center border border-gray-300 rounded bg-gray-50">
                            <button onclick="updateQuantity(${index}, -1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-gray-600 hover:bg-gray-200 rounded-l transition">-</button>
                            <span class="px-2 text-xs font-bold text-gray-800">${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-gray-600 hover:bg-gray-200 rounded-r transition ${item.quantity >= item.maxStock ? 'opacity-40 cursor-not-allowed' : ''}">+</button>
                        </div>

                        <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-600 transition p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += itemHTML;
    });

    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = cart.reduce((sum, item) => {
        if (!item.discountType || item.discountType === 'none') return sum;
        const discountedUnits = item.discountedQty || 0;
        return sum + (discountedUnits * (item.price * (item.discountRate || 0)));
    }, 0);

    const grandTotal = subtotal - discount;

    const subtotalEl = document.getElementById('subtotalDisplay');
    const discountEl = document.getElementById('discountDisplay');
    const totalEl = document.getElementById('grandTotalDisplay') || document.getElementById('totalDisplay');

    if (subtotalEl) subtotalEl.innerText = '₱' + subtotal.toFixed(2);
    if (discountEl) discountEl.innerText = '-₱' + discount.toFixed(2);
    if (totalEl) totalEl.innerText = '₱' + grandTotal.toFixed(2);
}

// --- PARKED / HOLD ORDER MODAL LOGIC ---

function updateHeldCount() {
    const badge = document.getElementById('heldCountBadge');
    if (badge) badge.innerText = heldOrders.length;
}

function holdCurrentOrder() {
    if (cart.length === 0) {
        showEmptyCartModal();
        return;
    }

    const holdRef = document.getElementById('holdReferenceInput');
    if (holdRef) holdRef.value = '';
    document.getElementById('holdOrderModal')?.classList.remove('hidden');
    setTimeout(() => document.getElementById('holdReferenceInput')?.focus(), 100);
}

function closeHoldModal() {
    document.getElementById('holdOrderModal')?.classList.add('hidden');
}

function confirmHoldOrder() {
    const referenceInput = document.getElementById('holdReferenceInput')?.value;
    if (!referenceInput || referenceInput.trim() === "") {
        alert("Please enter a Table Number or Customer Name.");
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = cart.reduce((sum, item) => {
        if (!item.discountType || item.discountType === 'none') return sum;
        return sum + ((item.discountedQty || 0) * (item.price * (item.discountRate || 0)));
    }, 0);
    const total = subtotal - discount;

    const heldSale = {
        id: 'HOLD-' + Date.now().toString().slice(-4),
        reference: referenceInput.trim(),
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        items: [...cart],
        channel: document.getElementById('orderChannel')?.value || 'Walk-in',
        total: total
    };

    heldOrders.push(heldSale);
    localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));

    cart = [];
    updateCartUI();
    updateHeldCount();
    closeHoldModal();
}

function openHeldOrdersModal() {
    const container = document.getElementById('heldOrdersContainer');
    if (!container) return;
    container.innerHTML = '';

    if (heldOrders.length === 0) {
        container.innerHTML = '<p class="text-gray-400 text-center py-8 text-sm">No orders found.</p>';
    } else {
        heldOrders.forEach((order, index) => {
            const itemsSummary = order.items.map(i => `${i.quantity}x ${i.name}`).join(', ');
            container.innerHTML += `
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 flex items-center justify-between gap-3 shadow-sm">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="bg-red-900 text-white font-bold text-[10px] px-2 py-0.5 rounded-full uppercase">
                                ${order.reference || order.id}
                            </span>
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

    document.getElementById('heldOrdersModal')?.classList.remove('hidden');
}

function recallOrder(index) {
    if (cart.length > 0) {
        if (!confirm("Recalling this order will replace your current active cart. Continue?")) {
            return;
        }
    }

    const selectedOrder = heldOrders[index];
    cart = [...selectedOrder.items];

    const channelEl = document.getElementById('orderChannel');
    if (channelEl) channelEl.value = selectedOrder.channel || 'Walk-in';

    heldOrders.splice(index, 1);
    localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));
    updateHeldCount();

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
    document.getElementById('heldOrdersModal')?.classList.add('hidden');
}

// --- EMPTY CART MODAL ---

function showEmptyCartModal() {
    document.getElementById('emptyCartModal')?.classList.remove('hidden');
}

function closeEmptyCartModal() {
    document.getElementById('emptyCartModal')?.classList.add('hidden');
}

// --- MODAL CONTROLLERS & CASH CALCULATOR ---

function openReviewModal() {
    if (cart.length === 0) {
        showEmptyCartModal();
        return;
    }

    const modalCartItems = document.getElementById('modalCartItems');
    if (!modalCartItems) return;

    modalCartItems.innerHTML = '';

    let subtotal = 0;
    let totalDiscount = 0;

    cart.forEach(item => {
        const itemSubtotal = item.price * item.quantity;
        const discountedUnits = item.discountedQty || 0;
        const itemDiscount = discountedUnits * (item.price * (item.discountRate || 0));
        const itemFinalPrice = itemSubtotal - itemDiscount;

        subtotal += itemSubtotal;
        totalDiscount += itemDiscount;

        const foundDiscount = window.availableDiscounts.find(d => String(d.id) === String(item.discountType));
        const discountLabel = foundDiscount ? foundDiscount.name : (item.discountType || '').toUpperCase();

        const discountBadge = (discountedUnits > 0 && item.discountType !== 'none')
            ? `<span class="text-[10px] bg-red-100 text-red-900 font-bold px-1.5 py-0.5 rounded ml-1">${discountedUnits}x ${discountLabel}</span>` 
            : '';

        const itemHTML = `
            <div class="flex justify-between items-center text-xs py-1.5 border-b border-gray-100 last:border-0">
                <div>
                    <span class="font-bold text-gray-800">${item.name}</span>
                    <span class="text-gray-500"> (x${item.quantity})</span>
                    ${discountBadge}
                </div>
                <div class="font-bold text-gray-800">
                    ₱${itemFinalPrice.toFixed(2)}
                </div>
            </div>
        `;
        modalCartItems.innerHTML += itemHTML;
    });

    const grandTotal = subtotal - totalDiscount;

    const modalSubtotal = document.getElementById('modalSubtotal');
    const modalDiscount = document.getElementById('modalDiscount');
    const modalTotal = document.getElementById('modalTotal');

    if (modalSubtotal) modalSubtotal.innerText = '₱' + subtotal.toFixed(2);
    if (modalDiscount) modalDiscount.innerText = '-₱' + totalDiscount.toFixed(2);
    if (modalTotal) modalTotal.innerText = '₱' + grandTotal.toFixed(2);

    const amountTendered = document.getElementById('amountTendered');
    if (amountTendered) amountTendered.value = '';
    calculateChange();

    document.getElementById('reviewModal')?.classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal')?.classList.add('hidden');
}

function calculateChange() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = cart.reduce((sum, item) => {
        if (!item.discountType || item.discountType === 'none') return sum;
        return sum + ((item.discountedQty || 0) * (item.price * (item.discountRate || 0)));
    }, 0);
    const grandTotal = subtotal - discount;

    const amountInput = document.getElementById('amountTendered');
    if (!amountInput) return;

    let rawVal = amountInput.value.trim();

    if (rawVal.startsWith('.') || rawVal.startsWith('0')) {
        rawVal = rawVal.replace(/^[0.]+/g, '');
        amountInput.value = rawVal;
    }

    if (rawVal.includes('.')) {
        const parts = rawVal.split('.');
        if (parts[1] && parts[1].length > 2) {
            rawVal = `${parts[0]}.${parts[1].slice(0, 2)}`;
            amountInput.value = rawVal;
        }
    }

    let amountTendered = parseFloat(rawVal) || 0;

    const MAX_CASH_LIMIT = 100000;
    if (amountTendered > MAX_CASH_LIMIT) {
        amountTendered = MAX_CASH_LIMIT;
        amountInput.value = MAX_CASH_LIMIT;
    }

    const change = amountTendered - grandTotal;
    const changeDisplay = document.getElementById('changeDisplay');
    const confirmBtn = document.getElementById('confirmSubmitBtn');

    if (changeDisplay) {
        if (change >= 0 && amountTendered > 0) {
            const formattedChange = change.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            changeDisplay.innerText = '₱' + formattedChange;
            changeDisplay.className = 'text-lg font-bold text-green-600 truncate';
        } else {
            changeDisplay.innerText = '₱0.00';
            changeDisplay.className = 'text-lg font-bold text-gray-400';
        }
    }

    if (confirmBtn) {
        if (amountTendered >= grandTotal && grandTotal > 0) {
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            confirmBtn.disabled = true;
            confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
}

function setExactAmount() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = cart.reduce((sum, item) => {
        if (!item.discountType || item.discountType === 'none') return sum;
        return sum + ((item.discountedQty || 0) * (item.price * (item.discountRate || 0)));
    }, 0);
    const grandTotal = subtotal - discount;

    const amountInput = document.getElementById('amountTendered');
    if (amountInput) {
        amountInput.value = grandTotal.toFixed(2);
        calculateChange();
    }
}

function addQuickCash(amount) {
    const amountInput = document.getElementById('amountTendered');
    if (amountInput) {
        const current = parseFloat(amountInput.value) || 0;
        amountInput.value = (current + amount).toFixed(2);
        calculateChange();
    }
}

function clearCash() {
    const amountInput = document.getElementById('amountTendered');
    if (amountInput) {
        amountInput.value = '';
        calculateChange();
    }
}

// --- PROCESS / SUBMIT ORDER ---

async function confirmAndSubmitOrder() {
    if (cart.length === 0) return;

    const selectedChannel = document.getElementById('orderChannel')?.value || 'Walk-in';
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountAmount = cart.reduce((sum, item) => {
        if (!item.discountType || item.discountType === 'none') return sum;
        return sum + ((item.discountedQty || 0) * (item.price * (item.discountRate || 0)));
    }, 0);

    const finalTotal = subtotal - discountAmount;
    const amountTendered = parseFloat(document.getElementById('amountTendered')?.value) || 0;

    if (amountTendered < finalTotal) {
        alert('Insufficient cash tendered!');
        return;
    }

    const vatExclusiveSubtotal = subtotal / 1.12;
    const vatAmount = subtotal - vatExclusiveSubtotal;

    const orderPayload = {
        subtotal: subtotal,
        vat_amount: vatAmount,
        channel: selectedChannel,
        discount_amount: discountAmount,
        total_amount: finalTotal,
        amount_tendered: amountTendered,
        change_amount: amountTendered - finalTotal,
        items: cart.map(item => ({
            id: item.id,
            name: item.name,
            quantity: item.quantity,
            discount_type: item.discountType || 'none',
            discount_rate: item.discountRate || 0,
            discounted_qty: item.discountedQty || 0,
            price: item.price
        }))
    };

    const submitBtn = document.getElementById('confirmSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = "Processing...";
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const response = await fetch('/sales', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
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
            alert("Error: " + (result.error || result.message || "Failed to process order"));
        }
    } catch (error) {
        console.error("Fetch error:", error);
        alert("A network error occurred.");
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = "Confirm & Pay";
        }
    }
}

const processOrder = confirmAndSubmitOrder;

// --- PRINTING RECEIPT MODAL LOGIC ---

function showPrintingModal(subtotal, discount, total, tendered, change, items) {
    const receiptDate = document.getElementById('receiptDate');
    if (receiptDate) receiptDate.innerText = new Date().toLocaleString();

    const listContainer = document.getElementById('receiptItemsList');
    if (listContainer) {
        listContainer.innerHTML = '';
        items.forEach(item => {
            const itemTotal = item.price * item.quantity;
            listContainer.innerHTML += `
                <div class="flex justify-between">
                    <span>${item.quantity}x ${item.name}</span>
                    <span>₱${itemTotal.toFixed(2)}</span>
                </div>
            `;
        });
    }

    const rSub = document.getElementById('receiptSubtotal');
    const rDisc = document.getElementById('receiptDiscount');
    const rTot = document.getElementById('receiptTotal');
    const rTen = document.getElementById('receiptTendered');
    const rCha = document.getElementById('receiptChange');

    if (rSub) rSub.innerText = '₱' + subtotal.toFixed(2);
    if (rDisc) rDisc.innerText = '-₱' + discount.toFixed(2);
    if (rTot) rTot.innerText = '₱' + total.toFixed(2);
    if (rTen) rTen.innerText = '₱' + tendered.toFixed(2);
    if (rCha) rCha.innerText = '₱' + change.toFixed(2);

    window.lastOrderTotals = { total, tendered, change };

    document.getElementById('printingModal')?.classList.remove('hidden');
}

function printReceipt() {
    window.print();
}

function finishPrinting() {
    document.getElementById('printingModal')?.classList.add('hidden');
    if (window.lastOrderTotals) {
        showThankYouModal(
            window.lastOrderTotals.total,
            window.lastOrderTotals.tendered,
            window.lastOrderTotals.change
        );
    }
}

// --- THANK YOU / SUCCESS MODAL LOGIC ---

function showThankYouModal(total, tendered, change) {
    const tTot = document.getElementById('thankYouTotal');
    const tTen = document.getElementById('thankYouTendered');
    const tCha = document.getElementById('thankYouChange');

    if (tTot) tTot.innerText = '₱' + total.toFixed(2);
    if (tTen) tTen.innerText = '₱' + tendered.toFixed(2);
    if (tCha) tCha.innerText = '₱' + change.toFixed(2);

    document.getElementById('thankYouModal')?.classList.remove('hidden');
}

function closeThankYouModal() {
    document.getElementById('thankYouModal')?.classList.add('hidden');
}

// --- EXPOSE FUNCTIONS TO WINDOW ---

window.fetchActiveDiscounts = fetchActiveDiscounts;
window.populateGlobalDiscountDropdown = populateGlobalDiscountDropdown;
window.setCategory = setCategory;
window.filterProducts = filterProducts;
window.addToCart = addToCart;
window.updateItemDiscountType = updateItemDiscountType;
window.updateItemDiscountQty = updateItemDiscountQty;
window.updateQuantity = updateQuantity;
window.removeFromCart = removeFromCart;
window.updateStockDisplay = updateStockDisplay;
window.updateCartUI = updateCartUI;
window.updateTotals = updateTotals;

window.updateHeldCount = updateHeldCount;
window.holdCurrentOrder = holdCurrentOrder;
window.closeHoldModal = closeHoldModal;
window.confirmHoldOrder = confirmHoldOrder;
window.openHeldOrdersModal = openHeldOrdersModal;
window.recallOrder = recallOrder;
window.deleteHeldOrder = deleteHeldOrder;
window.closeHeldOrdersModal = closeHeldOrdersModal;

window.showEmptyCartModal = showEmptyCartModal;
window.closeEmptyCartModal = closeEmptyCartModal;
window.openReviewModal = openReviewModal;
window.closeReviewModal = closeReviewModal;
window.calculateChange = calculateChange;
window.setExactAmount = setExactAmount;
window.addQuickCash = addQuickCash;
window.clearCash = clearCash;

window.confirmAndSubmitOrder = confirmAndSubmitOrder;
window.processOrder = processOrder;

window.showPrintingModal = showPrintingModal;
window.printReceipt = printReceipt;
window.finishPrinting = finishPrinting;
window.showThankYouModal = showThankYouModal;
window.closeThankYouModal = closeThankYouModal;