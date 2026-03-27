{{-- resources/views/pos/index.blade.php --}}
@extends('pos.layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="flex flex-col h-screen overflow-hidden">

    {{-- ═══ HEADER ═══════════════════════════════════════════════════════ --}}
    <header class="shrink-0 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-sm shadow-blue-200">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <span class="font-bold text-slate-800 text-sm leading-none">POS</span>
                @if($tenant)
                    <p class="text-xs text-slate-400 leading-none mt-0.5">{{ $tenant->name }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{-- Jam --}}
            <div class="hidden sm:block text-right">
                <p id="pos-time" class="font-mono font-semibold text-slate-700 text-sm tabular-nums">00:00:00</p>
                <p id="pos-date" class="text-xs text-slate-400"></p>
            </div>

            {{-- Kasir --}}
            <div class="flex items-center gap-2 pl-4 border-l border-slate-200">
                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-xs font-bold text-blue-700">{{ strtoupper(substr(auth('pos')->user()->name, 0, 1)) }}</span>
                </div>
                <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ auth('pos')->user()->name }}</span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('pos.logout') }}">
                @csrf
                <button class="text-xs text-slate-500 hover:text-red-600 font-medium transition-colors">Keluar</button>
            </form>
        </div>
    </header>

    {{-- ═══ BODY (2 kolom) ════════════════════════════════════════════════ --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- ─── KIRI: Produk ──────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col overflow-hidden border-r border-slate-200">

            {{-- Search & Filter --}}
            <div class="shrink-0 p-4 bg-white border-b border-slate-100 flex gap-2">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        id="search-input"
                        placeholder="Cari produk... (F2)"
                        class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-100 bg-slate-50"
                    >
                </div>

                <div class="flex gap-1 overflow-x-auto scrollbar-thin">
                    <button
                        class="category-btn px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white shrink-0"
                        data-category="">
                        Semua
                    </button>
                    @foreach($categories as $cat)
                    <button
                        class="category-btn px-3 py-2 text-xs font-medium rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-blue-400 hover:text-blue-600 shrink-0 transition-colors"
                        data-category="{{ $cat->id }}"
                        style="{{ $cat->color ? 'border-color:'.$cat->color.';' : '' }}">
                        {{ $cat->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Produk Grid --}}
            <div id="products-grid" class="flex-1 overflow-y-auto p-4 scrollbar-thin">
                <div id="products-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    {{-- Diisi via JS --}}
                </div>
                <div id="products-empty" class="hidden flex flex-col items-center justify-center h-48 text-slate-400">
                    <svg class="w-12 h-12 mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <p class="text-sm">Produk tidak ditemukan</p>
                </div>
                <div id="products-loading" class="flex items-center justify-center h-48">
                    <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        {{-- ─── KANAN: Keranjang ───────────────────────────────────────── --}}
        <div class="w-80 xl:w-96 flex flex-col bg-white">

            {{-- Cart Header --}}
            <div class="shrink-0 px-4 py-3 bg-blue-600 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="text-sm font-semibold text-white">Keranjang</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="cart-count" class="text-xs bg-blue-500 text-white px-2 py-0.5 rounded-full font-mono">0</span>
                    <button id="clear-cart" onclick="clearCart()" class="text-blue-200 hover:text-white transition-colors text-xs hidden">Kosongkan</button>
                </div>
            </div>

            {{-- Cart Items --}}
            <div id="cart-items" class="flex-1 overflow-y-auto p-3 scrollbar-thin space-y-2">
                <div id="cart-empty" class="flex flex-col items-center justify-center h-full text-slate-300 py-12">
                    <svg class="w-14 h-14 mb-3 opacity-50" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-slate-400">Keranjang kosong</p>
                    <p class="text-xs text-slate-300 mt-1">Pilih produk di sebelah kiri</p>
                </div>
            </div>

            {{-- Summary & Payment --}}
            <div class="shrink-0 border-t border-slate-200 p-4 space-y-3">
                {{-- Totals --}}
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span id="summary-subtotal" class="font-mono">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Diskon</span>
                        <span id="summary-discount" class="font-mono text-red-500">- Rp 0</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-1.5 flex justify-between font-bold text-slate-800">
                        <span>Total</span>
                        <span id="summary-total" class="font-mono text-blue-600 text-base">Rp 0</span>
                    </div>
                </div>

                {{-- Uang diterima (tampil saat ada isi keranjang) --}}
                <div id="payment-section" class="hidden space-y-2">
                    <div>
                        <label class="text-xs text-slate-500 font-medium mb-1 block">Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-mono">Rp</span>
                            <input
                                type="number"
                                id="paid-input"
                                placeholder="0"
                                class="w-full pl-9 pr-4 py-2 text-sm font-mono border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-100"
                                oninput="updateChange()"
                            >
                        </div>
                    </div>

                    <div class="flex justify-between text-sm bg-emerald-50 rounded-lg px-3 py-2">
                        <span class="text-emerald-700 font-medium">Kembalian</span>
                        <span id="change-display" class="font-mono font-bold text-emerald-700">Rp 0</span>
                    </div>

                    {{-- Metode Bayar --}}
                    <div class="grid grid-cols-3 gap-1.5">
                        <button onclick="setPayment('cash')" data-method="cash"
                            class="payment-method-btn active py-2 px-2 text-xs font-semibold rounded-lg border-2 border-blue-600 bg-blue-50 text-blue-700 transition-all">
                            💵 Tunai
                        </button>
                        <button onclick="setPayment('transfer')" data-method="transfer"
                            class="payment-method-btn py-2 px-2 text-xs font-semibold rounded-lg border-2 border-slate-200 text-slate-600 hover:border-blue-400 transition-all">
                            🏦 Transfer
                        </button>
                        <button onclick="setPayment('ewallet')" data-method="ewallet"
                            class="payment-method-btn py-2 px-2 text-xs font-semibold rounded-lg border-2 border-slate-200 text-slate-600 hover:border-blue-400 transition-all">
                            📱 E-Wallet
                        </button>
                    </div>

                    <button
                        id="pay-btn"
                        onclick="processPayment()"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-bold text-sm rounded-xl transition-all shadow-sm shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses Pembayaran (F5)
                    </button>
                    <button
                        onclick="clearCart()"
                        class="w-full py-2 border border-slate-200 hover:border-red-300 hover:text-red-600 text-slate-500 font-medium text-xs rounded-xl transition-colors">
                        Batalkan Transaksi
                    </button>
                </div>

                {{-- CTA saat kosong --}}
                <div id="empty-cta" class="text-center py-2">
                    <p class="text-xs text-slate-400">Tekan produk untuk menambahkan</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL: Struk ══════════════════════════════════════════════════════ --}}
<div id="receipt-modal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm max-h-[90vh] flex flex-col animate-slide-up">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-800">Struk Pembayaran</h3>
            <button onclick="closeReceipt()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div id="receipt-content" class="flex-1 overflow-y-auto p-5 text-sm font-mono space-y-4">
            {{-- Diisi via JS --}}
        </div>

        <div class="px-5 py-4 border-t border-slate-200 flex gap-2">
            <button onclick="printReceipt()" class="flex-1 py-2.5 bg-slate-800 text-white font-semibold text-sm rounded-xl hover:bg-slate-900 transition-colors">
                🖨 Print Struk
            </button>
            <button onclick="closeReceipt()" class="flex-1 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 transition-colors">
                Transaksi Baru
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── State ────────────────────────────────────────────────────────────────
const state = {
    cart: [],           // [{product, qty}]
    products: [],
    activeCategory: '',
    searchQuery: '',
    paymentMethod: 'cash',
    currentSaleId: null,
};

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ─── Clock ────────────────────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('pos-time').textContent =
        now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('pos-date').textContent =
        now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

// ─── Fetch Products ───────────────────────────────────────────────────────
async function loadProducts() {
    document.getElementById('products-loading').classList.remove('hidden');
    document.getElementById('products-container').classList.add('hidden');
    document.getElementById('products-empty').classList.add('hidden');

    const params = new URLSearchParams();
    if (state.activeCategory) params.set('category', state.activeCategory);
    if (state.searchQuery)    params.set('search',   state.searchQuery);

    const res = await fetch(`{{ route('pos.api.products') }}?${params}`, {
        headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' }
    });
    state.products = await res.json();

    document.getElementById('products-loading').classList.add('hidden');

    if (state.products.length === 0) {
        document.getElementById('products-empty').classList.remove('hidden');
    } else {
        document.getElementById('products-container').classList.remove('hidden');
        renderProducts();
    }
}

function renderProducts() {
    const container = document.getElementById('products-container');
    container.innerHTML = state.products.map(p => {
        const inCart = state.cart.find(c => c.product.id === p.id);
        const qty = inCart ? inCart.qty : 0;
        const outOfStock = !p.in_stock;

        return `<div
            class="relative bg-white rounded-xl border-2 ${outOfStock ? 'opacity-50 cursor-not-allowed border-slate-100' : 'border-slate-200 hover:border-blue-400 cursor-pointer hover:shadow-sm hover:shadow-blue-50 active:scale-[0.97]'} transition-all p-3 flex flex-col gap-1.5 select-none"
            onclick="${outOfStock ? '' : `addToCart(${p.id})`}">

            ${p.image
                ? `<img src="${p.image}" alt="" class="w-full h-20 object-cover rounded-lg mb-1" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`
                : ''}
            <div class="w-full h-20 bg-slate-100 rounded-lg mb-1 flex items-center justify-center text-slate-300" ${p.image ? 'style="display:none"' : ''}>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
            </div>

            <p class="font-semibold text-slate-800 text-xs leading-tight line-clamp-2">${p.name}</p>
            <p class="text-blue-600 font-bold text-sm font-mono">${formatRp(p.price)}</p>

            ${p.stock !== null
                ? `<p class="text-xs ${p.stock <= 5 ? 'text-amber-500' : 'text-slate-400'}">${outOfStock ? 'Habis' : `${p.stock} stok`}</p>`
                : ''}

            ${qty > 0 ? `<div class="absolute top-2 right-2 w-5 h-5 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">${qty}</div>` : ''}
        </div>`;
    }).join('');
}

// ─── Cart ─────────────────────────────────────────────────────────────────
function addToCart(productId) {
    const product = state.products.find(p => p.id === productId);
    if (!product || !product.in_stock) return;

    const existing = state.cart.find(c => c.product.id === productId);
    if (existing) {
        if (product.stock !== null && existing.qty >= product.stock) {
            alert('Stok tidak mencukupi!');
            return;
        }
        existing.qty++;
    } else {
        state.cart.push({ product, qty: 1 });
    }
    updateCartUI();
    renderProducts(); // refresh badge
}

function updateQty(productId, delta) {
    const idx = state.cart.findIndex(c => c.product.id === productId);
    if (idx === -1) return;
    state.cart[idx].qty += delta;
    if (state.cart[idx].qty <= 0) state.cart.splice(idx, 1);
    updateCartUI();
    renderProducts();
}

function removeItem(productId) {
    state.cart = state.cart.filter(c => c.product.id !== productId);
    updateCartUI();
    renderProducts();
}

function clearCart() {
    state.cart = [];
    state.currentSaleId = null;
    updateCartUI();
    renderProducts();
}

function updateCartUI() {
    const cartEl   = document.getElementById('cart-items');
    const emptyEl  = document.getElementById('cart-empty');
    const countEl  = document.getElementById('cart-count');
    const clearBtn = document.getElementById('clear-cart');
    const paymentSection = document.getElementById('payment-section');
    const emptyCta       = document.getElementById('empty-cta');

    const totalItems = state.cart.reduce((s, c) => s + c.qty, 0);
    countEl.textContent = totalItems;

    if (state.cart.length === 0) {
        emptyEl.classList.remove('hidden');
        clearBtn.classList.add('hidden');
        paymentSection.classList.add('hidden');
        emptyCta.classList.remove('hidden');
        document.getElementById('summary-subtotal').textContent = 'Rp 0';
        document.getElementById('summary-discount').textContent = '- Rp 0';
        document.getElementById('summary-total').textContent = 'Rp 0';
        return;
    }

    emptyEl.classList.add('hidden');
    clearBtn.classList.remove('hidden');
    paymentSection.classList.remove('hidden');
    emptyCta.classList.add('hidden');

    // Render items
    // Keep cart-empty in DOM, insert items before it
    const existingItems = cartEl.querySelectorAll('.cart-item-row');
    existingItems.forEach(el => el.remove());

    const fragment = document.createDocumentFragment();
    state.cart.forEach(({ product, qty }) => {
        const div = document.createElement('div');
        div.className = 'cart-item-row flex items-center gap-2 bg-slate-50 rounded-xl p-2.5 group animate-slide-up';
        div.innerHTML = `
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-700 truncate">${product.name}</p>
                <p class="text-xs text-blue-600 font-mono font-bold">${formatRp(product.price)}</p>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                <button onclick="updateQty(${product.id}, -1)"
                    class="w-6 h-6 rounded-md border border-slate-300 text-slate-600 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition-colors text-sm font-bold flex items-center justify-center">−</button>
                <span class="w-6 text-center text-xs font-bold text-slate-800 tabular-nums">${qty}</span>
                <button onclick="updateQty(${product.id}, +1)"
                    class="w-6 h-6 rounded-md border border-slate-300 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors text-sm font-bold flex items-center justify-center">+</button>
            </div>
            <p class="text-xs font-mono font-semibold text-slate-700 shrink-0 w-20 text-right">${formatRp(product.price * qty)}</p>
            <button onclick="removeItem(${product.id})" class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition-all ml-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
        fragment.appendChild(div);
    });
    cartEl.insertBefore(fragment, emptyEl);

    // Totals
    const subtotal = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
    document.getElementById('summary-subtotal').textContent = formatRp(subtotal);
    document.getElementById('summary-discount').textContent = '- Rp 0';
    document.getElementById('summary-total').textContent    = formatRp(subtotal);

    updateChange();
}

// ─── Payment ──────────────────────────────────────────────────────────────
function setPayment(method) {
    state.paymentMethod = method;
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
        const isActive = btn.dataset.method === method;
        btn.classList.toggle('border-blue-600',  isActive);
        btn.classList.toggle('bg-blue-50',       isActive);
        btn.classList.toggle('text-blue-700',    isActive);
        btn.classList.toggle('border-slate-200', !isActive);
        btn.classList.toggle('text-slate-600',   !isActive);
    });
}

function updateChange() {
    const total = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
    const paid  = parseFloat(document.getElementById('paid-input').value) || 0;
    const change = paid - total;
    document.getElementById('change-display').textContent = formatRp(Math.max(0, change));
    document.getElementById('change-display').className =
        `font-mono font-bold ${change < 0 ? 'text-red-600' : 'text-emerald-700'}`;
}

async function processPayment() {
    if (state.cart.length === 0) return;

    const total = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
    const paid  = parseFloat(document.getElementById('paid-input').value) || 0;

    if (state.paymentMethod === 'cash' && paid < total) {
        alert('Uang diterima kurang dari total!');
        document.getElementById('paid-input').focus();
        return;
    }

    const payBtn = document.getElementById('pay-btn');
    payBtn.disabled = true;
    payBtn.textContent = 'Memproses...';

    try {
        // 1. Buat sale
        const createRes = await fetch('{{ route('pos.api.sale.create') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            body: JSON.stringify({
                items: state.cart.map(c => ({ product_id: c.product.id, qty: c.qty })),
            }),
        });
        const saleData = await createRes.json();
        if (!createRes.ok) throw new Error(saleData.message || 'Gagal membuat transaksi.');

        // 2. Proses bayar
        const payRes = await fetch(`/pos/api/sale/${saleData.sale_id}/pay`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            body: JSON.stringify({
                payment_method: state.paymentMethod,
                paid: state.paymentMethod === 'cash' ? paid : total,
            }),
        });
        const payData = await payRes.json();
        if (!payRes.ok) throw new Error(payData.message || 'Gagal memproses pembayaran.');

        state.currentSaleId = saleData.sale_id;
        showReceiptModal(payData);

    } catch (err) {
        alert('Error: ' + err.message);
    } finally {
        payBtn.disabled = false;
        payBtn.textContent = 'Proses Pembayaran (F5)';
    }
}

// ─── Receipt Modal ────────────────────────────────────────────────────────
function showReceiptModal(payData) {
    const content = document.getElementById('receipt-content');
    const subtotal = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);

    content.innerHTML = `
        <div class="text-center mb-4">
            <p class="font-bold text-slate-800 text-base">{{ config('app.name') }}</p>
            <p class="text-xs text-slate-500">${new Date().toLocaleString('id-ID')}</p>
            <p class="text-xs text-slate-500 mt-1">No: ${payData.invoice_number}</p>
        </div>
        <div class="border-t border-dashed border-slate-300 pt-3 space-y-1">
            ${state.cart.map(c => `
                <div class="flex justify-between text-xs">
                    <span class="flex-1 truncate">${c.product.name}</span>
                    <span class="ml-2 shrink-0">${c.qty}x ${formatRp(c.product.price)}</span>
                </div>
            `).join('')}
        </div>
        <div class="border-t border-dashed border-slate-300 pt-3 space-y-1 text-xs">
            <div class="flex justify-between"><span>Subtotal</span><span>${formatRp(subtotal)}</span></div>
            <div class="flex justify-between font-bold text-sm text-slate-800"><span>Total</span><span>${formatRp(payData.total)}</span></div>
            <div class="flex justify-between text-emerald-600"><span>Bayar</span><span>${formatRp(payData.paid)}</span></div>
            <div class="flex justify-between font-bold text-emerald-700"><span>Kembalian</span><span>${formatRp(payData.change)}</span></div>
        </div>
        <div class="text-center text-xs text-slate-400 mt-4 border-t border-dashed border-slate-300 pt-3">
            Terima kasih telah berbelanja!
        </div>`;

    document.getElementById('receipt-modal').classList.remove('hidden');
}

function closeReceipt() {
    document.getElementById('receipt-modal').classList.add('hidden');
    clearCart();
    document.getElementById('paid-input').value = '';
}

function printReceipt() {
    window.print();
}

// ─── Category Filter ──────────────────────────────────────────────────────
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.category-btn').forEach(b => {
            b.classList.remove('bg-blue-600', 'text-white');
            b.classList.add('bg-white', 'text-slate-600');
        });
        btn.classList.add('bg-blue-600', 'text-white');
        btn.classList.remove('bg-white', 'text-slate-600');
        state.activeCategory = btn.dataset.category;
        loadProducts();
    });
});

// ─── Search Debounce ──────────────────────────────────────────────────────
let searchTimer;
document.getElementById('search-input').addEventListener('input', e => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        state.searchQuery = e.target.value;
        loadProducts();
    }, 300);
});

// ─── Keyboard Shortcuts ───────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'F2') { e.preventDefault(); document.getElementById('search-input').focus(); }
    if (e.key === 'F5') { e.preventDefault(); processPayment(); }
    if (e.key === 'Escape') { closeReceipt(); }
});

// ─── Helpers ──────────────────────────────────────────────────────────────
function formatRp(amount) {
    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
}

// ─── Init ─────────────────────────────────────────────────────────────────
loadProducts();
</script>
@endpush