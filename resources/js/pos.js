let cart = [];
let heldOrders = JSON.parse(localStorage.getItem('pbwsis_held_orders')) || [];
let currentCategory = 'all';

// Initialize badges on page load
document.addEventListener("DOMContentLoaded", () => {
    updateHeldCount();
});

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
            id, 
            name, 
            price, 
            quantity: 1, 
            maxStock: maxStock,
            discountedQty: 0
        });
    }

    updateStockDisplay(id);
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
        const itemDiscount = discountedUnits * (item.price * 0.20);
        const itemFinalPrice = itemSubtotal - itemDiscount;

        const itemHTML = `
            <div class="bg-white p-3 rounded shadow-sm border border-gray-200 space-y-2">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 mr-2">
                        <h4 class="text-sm font-bold text-gray-800 truncate">${item.name}</h4>
                        <div class="text-xs text-gray-500">₱${item.price.toFixed(2)} each ${item.quantity > 1 ? `(x${item.quantity})` : ''}</div>
                    </div>
                    <div class="font-bold ${discountedUnits > 0 ? 'text-red-900' : 'text-gray-800'} text-right text-sm">
                        ₱${itemFinalPrice.toFixed(2)}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-1 bg-red-50 border border-red-200 px-2 py-1 rounded-lg">
                        <span class="text-[11px] font-bold text-red-900 mr-1">🏷️ SC/PWD:</span>
                        <button type="button" onclick="updateItemDiscountQty(${index}, -1)" class="w-5 h-5 bg-white border border-red-300 rounded font-black text-red-900 hover:bg-red-200 flex items-center justify-center text-xs shadow-sm">-</button>
                        <span class="text-xs font-bold text-red-900 px-1 min-w-[35px] text-center">${discountedUnits}/${item.quantity}</span>
                        <button type="button" onclick="updateItemDiscountQty(${index}, 1)" class="w-5 h-5 bg-white border border-red-300 rounded font-black text-red-900 hover:bg-red-200 flex items-center justify-center text-xs shadow-sm">+</button>
                    </div>

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
        const discountedUnits = item.discountedQty || 0;
        return sum + (discountedUnits * (item.price * 0.20));
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
    const discountSelect = document.getElementById('discountType')?.value;
    let discountRate = (discountSelect === 'senior' || discountSelect === 'pwd') ? 0.20 : 0;
    const total = subtotal - (subtotal * discountRate);

    const heldSale = {
        id: 'HOLD-' + Date.now().toString().slice(-4),
        reference: referenceInput.trim(),
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        items: [...cart],
        channel: document.getElementById('orderChannel')?.value || 'Walk-in',
        discount: discountSelect || 'none',
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
    const discEl = document.getElementById('discountType');
    if (discEl) discEl.value = selectedOrder.discount || 'none';

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
    let totalDiscountedUnits = 0;

    cart.forEach(item => {
        const itemSubtotal = item.price * item.quantity;
        const discountedUnits = item.discountedQty || 0;
        const itemDiscount = discountedUnits * (item.price * 0.20);
        const itemFinalPrice = itemSubtotal - itemDiscount;

        subtotal += itemSubtotal;
        totalDiscount += itemDiscount;
        totalDiscountedUnits += discountedUnits;

        const discountBadge = discountedUnits > 0 
            ? `<span class="text-[10px] bg-red-100 text-red-900 font-bold px-1.5 py-0.5 rounded ml-1">${discountedUnits}x 20% SC/PWD</span>` 
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
    const modalDiscountType = document.getElementById('modalDiscountType');
    const modalTotal = document.getElementById('modalTotal');

    if (modalSubtotal) modalSubtotal.innerText = '₱' + subtotal.toFixed(2);
    if (modalDiscount) modalDiscount.innerText = '-₱' + totalDiscount.toFixed(2);
    if (modalDiscountType) {
        modalDiscountType.innerText = totalDiscountedUnits > 0 
            ? `SC/PWD (${totalDiscountedUnits} item${totalDiscountedUnits > 1 ? 's' : ''})` 
            : 'None';
    }
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
    const discount = cart.reduce((sum, item) => sum + ((item.discountedQty || 0) * item.price * 0.20), 0);
    const grandTotal = subtotal - discount;

    const amountInput = document.getElementById('amountTendered');
    if (!amountInput) return;

    let rawVal = amountInput.value.trim();

    // 1. BLOCK LEADING DOT OR ZERO
    if (rawVal.startsWith('.') || rawVal.startsWith('0')) {
        rawVal = rawVal.replace(/^[0.]+/g, '');
        amountInput.value = rawVal;
    }

    // 2. TRUNCATE DECIMALS (max 2 digits)
    if (rawVal.includes('.')) {
        const parts = rawVal.split('.');
        if (parts[1] && parts[1].length > 2) {
            rawVal = `${parts[0]}.${parts[1].slice(0, 2)}`;
            amountInput.value = rawVal;
        }
    }

    let amountTendered = parseFloat(rawVal) || 0;

    // 3. HARD CAP (₱100,000)
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
    const discount = cart.reduce((sum, item) => sum + ((item.discountedQty || 0) * item.price * 0.20), 0);
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
    const selectedDiscount = document.getElementById('discountType')?.value || 'none';
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountAmount = cart.reduce((sum, item) => sum + ((item.discountedQty || 0) * item.price * 0.20), 0);
    const finalTotal = subtotal - discountAmount;
    const amountTendered = parseFloat(document.getElementById('amountTendered')?.value) || 0;

    if (amountTendered < finalTotal) {
        alert('Insufficient cash tendered!');
        return;
    }

    const orderPayload = {
        channel: selectedChannel,
        discount_type: selectedDiscount,
        discount_amount: discountAmount,
        total_amount: finalTotal,
        amount_tendered: amountTendered,
        change_amount: amountTendered - finalTotal,
        items: cart.map(item => ({
            id: item.id,
            name: item.name,
            quantity: item.quantity,
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
        // 🚨 Note: Change '/pos/checkout' if your web.php route uses a different path (e.g. '/pos/store')
        const response = await fetch('/pos/checkout', { 
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
            
            // Update stock badges on screen
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

// Alias processOrder to point to the main checkout logic
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

// --- EXPOSE FUNCTIONS TO WINDOW (Single Clean Block) ---

window.setCategory = setCategory;
window.filterProducts = filterProducts;
window.addToCart = addToCart;
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