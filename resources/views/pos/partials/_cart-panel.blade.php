{{-- resources/views/pos/partials/_cart-panel.blade.php --}}
{{-- Dipakai oleh: Desktop sidebar (md+) --}}

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
                    oninput="updateChange('desktop')"
                >
            </div>
        </div>

        <div class="flex justify-between text-sm bg-emerald-50 rounded-lg px-3 py-2">
            <span class="text-emerald-700 font-medium">Kembalian</span>
            <span id="change-display" class="font-mono font-bold text-emerald-700">Rp 0</span>
        </div>

        <div class="grid grid-cols-3 gap-1.5">
            <button onclick="setPayment('cash')" data-method="cash"
                class="payment-method-btn py-2 px-2 text-xs font-semibold rounded-lg border-2 border-blue-600 bg-blue-50 text-blue-700 transition-all">
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

    <div id="empty-cta" class="text-center py-2">
        <p class="text-xs text-slate-400">Tekan produk untuk menambahkan</p>
    </div>
</div>