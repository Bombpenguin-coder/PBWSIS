let cart = [];
let heldOrders = JSON.parse(localStorage.getItem('pbwsis_held_orders')) || [];
let currentCategory = 'all';

// Default fallback discounts
window.availableDiscounts = window.availableDiscounts || [
    { id: 'sc_pwd', name: 'SC/PWD', rate: 20 },
    { id: 'employee', name: 'Employee', rate: 15 },
    { id: 'promo', name: 'Promo', rate: 10 }
];

document.addEventListener("DOMContentLoaded", () => {
    updateHeldCount();
    fetchActiveDiscounts();
});

// --- HELPER FUNCTIONS (Eliminating Repetition) ---

function toggleModal(modalId, show = true) {
    document.getElementById(modalId)?.classList[show ? 'remove' : 'add']('hidden');
}

function getTextOrValue(id, value = null) {
    const el = document.getElementById(id);
    if (!el) return '';
    if (value !== null) el.innerText = value;
    return el.value || el.innerText;
}

// Single source of truth for POS calculations
function getCartTotals() {
    const vatConfig = window.vatConfig || { rate: 12.00, is_inclusive: true, is_enabled: true };
    const isEnabled = vatConfig.is_enabled ?? vatConfig.is_active ?? true;
    const isInclusive = vatConfig.is_inclusive ?? true;
    const vatRate = parseFloat(vatConfig.rate ?? 12.00) / 100;

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    const discount = cart.reduce((sum, item) => {
        if (!item.discountType || item.discountType === 'none') return sum;
        const discountedUnits = Math.min(item.discountedQty || item.quantity, item.quantity);
        const rate = item.discountRate || 0;
        const basePrice = (isInclusive && isEnabled) ? (item.price / (1 + vatRate)) : item.price;
        return sum + (discountedUnits * (basePrice * rate));
    }, 0);

    const discountedSubtotal = subtotal - discount;
    let vatAmount = 0;

    if (isEnabled && discountedSubtotal > 0) {
        vatAmount = isInclusive 
            ? discountedSubtotal - (discountedSubtotal / (1 + vatRate))
            : discountedSubtotal * vatRate;
    }

    const grandTotal = isInclusive ? discountedSubtotal : (discountedSubtotal + vatAmount);

    return { subtotal, discount, discountedSubtotal, vatAmount, grandTotal };
}

// --- FETCH DISCOUNTS FROM BACKEND ---

async function fetchActiveDiscounts() {
    try {
        const response = await fetch('/discounts/active', { headers: { 'Accept': 'application/json' } });
        if (response.ok) {
            const data = await response.json();
            if (Array.isArray(data) && data.length > 0) {
                window.availableDiscounts = data.map(d => ({
                    id: d.id || d.slug || d.name.toLowerCase().replace(/\s+/g, '_'),
                    name: d.name,
                    rate: parseFloat(d.percentage || d.value || d.rate || 0)
                }));
                populateGlobalDiscountDropdown();
                updateCartUI();
            }
        }
    } catch (err) {
        console.warn("Could not load dynamic discounts, using fallback options:", err);
    }
}

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

    // Reset all buttons to default inactive state
    document.querySelectorAll('.cat-btn').forEach(btn => {
        btn.classList.remove('bg-brand-orange', 'bg-[#800000]', 'text-white');
        btn.classList.add('bg-brand-panel', 'text-zinc-300');
    });

    // Apply active state to clicked button
    if (btnElement) {
        btnElement.classList.remove('bg-brand-panel', 'text-zinc-300');
        btnElement.classList.add('bg-brand-orange', 'text-white'); // or your active class
    }

    filterProducts();
}

function filterProducts() {
    const query = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    const sanitize = str => (str || '').toLowerCase().replace(/[^a-z0-9]/g, '');

    document.querySelectorAll('.product-card').forEach(card => {
        const cleanName = sanitize(card.getAttribute('data-name'));
        const cleanCategory = sanitize(card.getAttribute('data-category'));
        const cleanSelectedCat = sanitize(currentCategory);

        const matchesCategory = (currentCategory === 'all') ||
            (cleanSelectedCat === 'chicken' ? (cleanCategory.includes('chicken') || cleanName.includes('chicken') || cleanName.includes('wings')) : (cleanCategory.includes(cleanSelectedCat) || cleanName.includes(cleanSelectedCat)));

        const matchesSearch = cleanName.includes(sanitize(query));
        card.classList.toggle('hidden', !(matchesCategory && matchesSearch));
    });
}

// --- CART & DISCOUNT STEPPER LOGIC ---

function addToCart(element) {
    if (!element) return;
    const id = element.getAttribute('data-id');
    const name = element.getAttribute('data-name');
    const price = parseFloat(element.getAttribute('data-price'));

    if (!id || isNaN(price)) return;

    const existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id, name, price, quantity: 1, discountType: 'none', discountRate: 0, discountedQty: 0 });
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
        const found = window.availableDiscounts.find(d => String(d.id) === String(selectedValue));
        let rate = found ? parseFloat(found.rate) : 0;
        item.discountType = selectedValue;
        item.discountRate = rate > 1 ? rate / 100 : rate;
        item.discountedQty = item.quantity;
    }
    updateCartUI();
}

function updateItemDiscountQty(index, delta) {
    const item = cart[index];
    if (!item) return;
    item.discountedQty = Math.min(Math.max(0, (item.discountedQty || 0) + delta), item.quantity);
    updateCartUI();
}

function updateQuantity(index, delta) {
    const item = cart[index];
    if (!item) return;

    item.quantity += delta;
    if (item.quantity <= 0) {
        removeFromCart(index);
        return;
    }

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
    document.getElementById(`product-card-${productId}`)?.classList.remove('opacity-50', 'pointer-events-none', 'cursor-not-allowed');
}

function updateCartUI() {
    const container = document.getElementById('cartItemsContainer');
    if (!container) return;

    if (cart.length === 0) {
        container.innerHTML = `<p class="text-zinc-500 text-center mt-10 text-sm">Cart is currently empty. Click an item to add it.</p>`;
        updateTotals();
        return;
    }

    container.innerHTML = cart.map((item, index) => {
        const itemSubtotal = (parseFloat(item.price) * item.quantity).toFixed(2);
        
        return `
            <div class="bg-[#18191c] p-3 rounded-xl border border-zinc-800 shadow-sm space-y-2 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-sm text-white">${item.name}</h4>
                        <span class="text-xs text-zinc-400">₱${parseFloat(item.price).toFixed(2)} each</span>
                    </div>
                 <span class="font-bold text-sm text-white">₱${(item.price * item.quantity).toFixed(2)}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-800/80">
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="updateQuantity(${index}, -1)" class="px-2 py-1 bg-zinc-800 hover:bg-zinc-700 rounded text-xs text-white">-</button>
                        <span class="text-sm font-bold px-1">${item.quantity}</span>
                        <button type="button" onclick="updateQuantity(${index}, 1)" class="px-2 py-1 bg-zinc-800 hover:bg-zinc-700 rounded text-xs text-white">+</button>
                    </div>
                    <button type="button" onclick="removeFromCart(${index})" class="text-zinc-400 hover:text-red-500 text-xs transition-colors">🗑️ Delete</button>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-800/80">
                    <select onchange="updateItemDiscountType(${index}, this)" class="bg-[#202226] text-xs text-zinc-300 border border-zinc-700 rounded px-2 py-1 focus:outline-none">
                        <option value="none">No Discount</option>
                        ${(window.availableDiscounts || []).map(d => `
                            <option value="${d.id}" ${item.discountType == d.id ? 'selected' : ''}>${d.name} (${d.rate}%)</option>
                        `).join('')}
                    </select>
                </div>
            </div>
        `;
    }).join('');

    updateTotals();
}

function updateTotals() {
    const { subtotal, discount, vatAmount, grandTotal } = getCartTotals();

    getTextOrValue('subtotalDisplay', '₱' + subtotal.toFixed(2));
    getTextOrValue('discountDisplay', '-₱' + discount.toFixed(2));
    getTextOrValue('vatDisplay', '₱' + vatAmount.toFixed(2));
    
    const totalEl = document.getElementById('grandTotalDisplay') || document.getElementById('totalDisplay');
    if (totalEl) totalEl.innerText = '₱' + grandTotal.toFixed(2);
}

// --- PARKED / HOLD ORDER MODAL LOGIC ---

function updateHeldCount() {
    getTextOrValue('heldCountBadge', heldOrders.length);
}

function holdCurrentOrder() {
    if (cart.length === 0) return showEmptyCartModal();
    const holdRef = document.getElementById('holdReferenceInput');
    if (holdRef) holdRef.value = '';
    toggleModal('holdOrderModal', true);
    setTimeout(() => holdRef?.focus(), 100);
}

const closeHoldModal = () => toggleModal('holdOrderModal', false);

function confirmHoldOrder() {
    const referenceInput = document.getElementById('holdReferenceInput')?.value;
    if (!referenceInput?.trim()) return alert("Please enter a Table Number or Customer Name.");

    const { grandTotal } = getCartTotals();

    heldOrders.push({
        id: 'HOLD-' + Date.now().toString().slice(-4),
        reference: referenceInput.trim(),
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        items: [...cart],
        channel: document.getElementById('orderChannel')?.value || 'Walk-in',
        total: grandTotal
    });

    localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));
    cart = [];
    updateCartUI();
    updateHeldCount();
    closeHoldModal();
}

function openHeldOrdersModal() {
    const container = document.getElementById('heldOrdersContainer');
    if (!container) return;

    container.innerHTML = heldOrders.length === 0 
        ? '<p class="text-gray-400 text-center py-8 text-sm">No orders found.</p>'
        : heldOrders.map((order, index) => `
            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="bg-red-900 text-white font-bold text-[10px] px-2 py-0.5 rounded-full uppercase">${order.reference || order.id}</span>
                        <span class="text-[10px] text-gray-400">${order.timestamp}</span>
                        <span class="bg-gray-200 text-gray-700 text-[10px] px-1.5 py-0.5 rounded uppercase font-semibold">${order.channel}</span>
                    </div>
                    <p class="text-xs text-gray-600 truncate mt-1">${order.items.map(i => `${i.quantity}x ${i.name}`).join(', ')}</p>
                    <p class="text-xs font-bold text-red-900 mt-0.5">₱${order.total.toFixed(2)}</p>
                </div>
                <div class="flex items-center space-x-1.5 shrink-0">
                    <button onclick="recallOrder(${index})" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm">Recall</button>
                    <button onclick="deleteHeldOrder(${index})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold w-7 h-7 rounded-lg transition flex items-center justify-center">✕</button>
                </div>
            </div>
        `).join('');

    toggleModal('heldOrdersModal', true);
}

function recallOrder(index) {
    if (cart.length > 0 && !confirm("Recalling this order will replace your current active cart. Continue?")) return;

    const selectedOrder = heldOrders.splice(index, 1)[0];
    cart = [...selectedOrder.items];

    const channelEl = document.getElementById('orderChannel');
    if (channelEl) channelEl.value = selectedOrder.channel || 'Walk-in';

    localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));
    updateHeldCount();
    document.querySelectorAll('.product-card').forEach(card => updateStockDisplay(card.getAttribute('data-id')));
    updateCartUI();
    closeHeldOrdersModal();
}

function deleteHeldOrder(index) {
    heldOrders.splice(index, 1);
    localStorage.setItem('pbwsis_held_orders', JSON.stringify(heldOrders));
    updateHeldCount();
    openHeldOrdersModal();
}

const closeHeldOrdersModal = () => toggleModal('heldOrdersModal', false);
const showEmptyCartModal = () => toggleModal('emptyCartModal', true);
const closeEmptyCartModal = () => toggleModal('emptyCartModal', false);

// --- MODAL CONTROLLERS & CASH CALCULATOR ---

function openReviewModal() {
    if (cart.length === 0) return showEmptyCartModal();

    const modalCartItems = document.getElementById('modalCartItems');
    if (!modalCartItems) return;
    
    getTextOrValue('modalChannel', document.getElementById('orderChannel')?.value || 'Walk-in');
    modalCartItems.innerHTML = '';

    cart.forEach(item => {
        const itemSubtotal = item.price * item.quantity;
        const discountedUnits = item.discountedQty || 0;
        const itemDiscount = discountedUnits * (item.price * (item.discountRate || 0));
        const foundDiscount = (window.availableDiscounts || []).find(d => String(d.id) === String(item.discountType));
        const discountLabel = foundDiscount ? foundDiscount.name : (item.discountType || '').toUpperCase();

        const discountBadge = (discountedUnits > 0 && item.discountType !== 'none')
          ? `<span class="text-[10px] bg-red-100 text-red-900 font-bold px-1.5 py-0.5 rounded ml-1">${discountedUnits}x ${discountLabel}</span>`
            : '';

        modalCartItems.innerHTML += `
            <div class="flex justify-between items-center text-xs py-1.5 border-b border-zinc-800/80 last:border-0 text-white">
                <div>
                    <span class="font-bold text-white">${item.name}</span>
                    <span class="text-zinc-400"> (x${item.quantity})</span>
                    ${discountBadge}
                </div>
             <div class="font-bold text-white">₱${(itemSubtotal - itemDiscount).toFixed(2)}</div>
            </div>
        `;
    });

    const { subtotal, discount, vatAmount, grandTotal } = getCartTotals();
    
    getTextOrValue('modalSubtotal', '₱' + subtotal.toFixed(2));
    getTextOrValue('modalDiscount', '-₱' + discount.toFixed(2));
    getTextOrValue('modalVatDisplay', '₱' + vatAmount.toFixed(2));
    getTextOrValue('modalTotal', '₱' + grandTotal.toFixed(2));

    const amountTendered = document.getElementById('amountTendered');
    if (amountTendered) amountTendered.value = '';
    calculateChange();
    toggleModal('reviewModal', true);
}

const closeReviewModal = () => toggleModal('reviewModal', false);

function calculateChange() {
    const changeDisplay = document.getElementById('changeDisplay');
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    const amountInput = document.getElementById('amountTendered');

    const { grandTotal } = getCartTotals();
    let amountTendered = 0;

    if (amountInput) {
        let rawVal = amountInput.value.trim().replace(/^[0.]+/g, '');
        if (rawVal.includes('.')) {
            const parts = rawVal.split('.');
            if (parts[1]?.length > 2) rawVal = `${parts[0]}.${parts[1].slice(0, 2)}`;
        }
        amountInput.value = rawVal;
        amountTendered = parseFloat(rawVal) || 0;

        if (amountTendered > 100000) {
            amountTendered = 100000;
            amountInput.value = 100000;
        }
    }

    const change = amountTendered - grandTotal;

    if (changeDisplay) {
        const isValid = change >= 0 && amountTendered > 0;
        changeDisplay.innerText = '₱' + (isValid ? change.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00');
        changeDisplay.className = isValid ? 'text-lg font-bold text-green-600 truncate' : 'text-lg font-bold text-gray-400';
    }

    if (confirmBtn) {
        const canSubmit = amountTendered >= grandTotal && grandTotal > 0;
        confirmBtn.disabled = !canSubmit;
        confirmBtn.classList.toggle('opacity-50', !canSubmit);
        confirmBtn.classList.toggle('cursor-not-allowed', !canSubmit);
    }
}

function setExactAmount() {
    const { grandTotal } = getCartTotals();
    const amountInput = document.getElementById('amountTendered');
    if (amountInput) {
        amountInput.value = grandTotal.toFixed(2);
        calculateChange();
    }
}

function addQuickCash(amount) {
    const amountInput = document.getElementById('amountTendered');
    if (amountInput) {
        amountInput.value = ((parseFloat(amountInput.value) || 0) + amount).toFixed(2);
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

    const { subtotal, discount: discountAmount, vatAmount, grandTotal: finalTotal } = getCartTotals();
    const selectedChannel = document.getElementById('orderChannel')?.value || 'Walk-in';
    const amountTendered = parseFloat(document.getElementById('amountTendered')?.value) || 0;

    if (amountTendered < finalTotal) {
        return typeof showErrorToast === 'function' ? showErrorToast('Insufficient cash tendered!') : alert('Insufficient cash tendered!');
    }

    const orderPayload = {
        subtotal,
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
            toggleModal('reviewModal', false);
            showPrintingModal(subtotal, discountAmount, finalTotal, amountTendered, amountTendered - finalTotal, cart);
        } else {
            const msg = result.error || result.message || "Failed to process order";
            typeof showErrorToast === 'function' ? showErrorToast(msg) : alert(msg);
        }
    } catch (error) {
        console.error("Fetch error:", error);
        typeof showErrorToast === 'function' ? showErrorToast("A network error occurred. Please try again.") : alert("Network error");
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
    getTextOrValue('receiptDate', new Date().toLocaleString());

    const listContainer = document.getElementById('receiptItemsList');
    if (listContainer) {
        listContainer.innerHTML = items.map(item => `
            <div class="flex justify-between">
                <span>${item.quantity}x ${item.name}</span>
                <span>₱${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `).join('');
    }

    const { vatAmount } = getCartTotals();

    getTextOrValue('receiptSubtotal', '₱' + subtotal.toFixed(2));
    getTextOrValue('receiptDiscount', '-₱' + discount.toFixed(2));
    getTextOrValue('receiptVat', '₱' + vatAmount.toFixed(2));
    getTextOrValue('receiptTotal', '₱' + total.toFixed(2));
    getTextOrValue('receiptTendered', '₱' + tendered.toFixed(2));
    getTextOrValue('receiptChange', '₱' + change.toFixed(2));

    window.lastOrderTotals = { total, tendered, change };
    toggleModal('printingModal', true);
}

const printReceipt = () => window.print();

function finishPrinting() {
    toggleModal('printingModal', false);
    if (window.lastOrderTotals) {
        showThankYouModal(window.lastOrderTotals.total, window.lastOrderTotals.tendered, window.lastOrderTotals.change);
    }
}

// --- THANK YOU / SUCCESS MODAL LOGIC ---

function showThankYouModal(total, tendered, change) {
    getTextOrValue('thankYouTotal', '₱' + total.toFixed(2));
    getTextOrValue('thankYouTendered', '₱' + tendered.toFixed(2));
    getTextOrValue('thankYouChange', '₱' + change.toFixed(2));
    toggleModal('thankYouModal', true);
}

function closeThankYouModal() {
    toggleModal('thankYouModal', false);
    cart = [];
    updateCartUI();

    const amountInput = document.getElementById('amountTendered');
    if (amountInput) amountInput.value = '';
    getTextOrValue('changeDisplay', '₱0.00');
}

function calculateTotals(subtotal) {
    return getCartTotals().vatAmount;
}

window.updateOrderChannel = function(channelValue) {
    const channelSelect = document.getElementById('orderChannel');
    if (channelSelect) channelSelect.value = channelValue;
    getTextOrValue('modalChannel', channelValue);
};

// --- EXPOSE FUNCTIONS TO WINDOW ---

Object.assign(window, {
    fetchActiveDiscounts,
    populateGlobalDiscountDropdown,
    setCategory,
    filterProducts,
    addToCart,
    updateItemDiscountType,
    updateItemDiscountQty,
    updateQuantity,
    removeFromCart,
    updateStockDisplay,
    updateCartUI,
    updateTotals,
    calculateTotals,
    updateHeldCount,
    holdCurrentOrder,
    closeHoldModal,
    confirmHoldOrder,
    openHeldOrdersModal,
    recallOrder,
    deleteHeldOrder,
    closeHeldOrdersModal,
    showEmptyCartModal,
    closeEmptyCartModal,
    openReviewModal,
    closeReviewModal,
    calculateChange,
    setExactAmount,
    addQuickCash,
    clearCash,
    confirmAndSubmitOrder,
    processOrder,
    showPrintingModal,
    printReceipt,
    finishPrinting,
    showThankYouModal,
    closeThankYouModal
});