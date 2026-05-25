{{-- resources/views/pos/partials/_cart-panel.blade.php --}}
{{-- Desktop sidebar cart (md+) — Mobile uses bottom sheet in index.blade.php --}}

{{-- Header --}}
<div class="cart-header">
    <div class="cart-title">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <span>Keranjang</span>
        <span id="cart-badge" class="cart-badge">0</span>
    </div>
    <button id="clear-cart" onclick="clearCart()" class="cart-clear hidden">Hapus semua</button>
</div>

{{-- Items --}}
<div id="cart-items" class="cart-body">
    <div id="cart-empty" class="cart-empty">
        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" opacity=".3" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="cart-empty-title">Keranjang kosong</p>
        <p class="cart-empty-sub">Ketuk produk untuk menambahkan</p>
    </div>
</div>

{{-- Summary + Payment --}}
<div class="cart-footer">
    <div class="summary">
        <div class="summary-row">
            <span>Subtotal</span>
            <span id="summary-subtotal" class="mono">Rp 0</span>
        </div>
        <div class="summary-row">
            <span>Diskon</span>
            <span id="summary-discount" class="mono disc">- Rp 0</span>
        </div>
        <div class="summary-row total">
            <span>Total</span>
            <span id="summary-total" class="mono total-val">Rp 0</span>
        </div>
    </div>

    <div id="payment-section" class="pay-section hidden">
        {{-- Cash input --}}
        <div class="cash-row">
            <label class="cash-label">Uang Diterima</label>
            <div class="cash-input-wrap">
                <span class="cash-prefix">Rp</span>
                <input type="number" id="paid-input" placeholder="0"
                    class="cash-input" oninput="updateChange('desktop')">
            </div>
        </div>

        {{-- Change --}}
        <div class="change-row">
            <span>Kembalian</span>
            <span id="change-display" class="mono change-val">Rp 0</span>
        </div>

        {{-- Method --}}
        <div class="method-grid">
            <button onclick="setPayment('cash')" data-method="cash"
                class="method-btn active">
                <span class="method-icon">💵</span> Tunai
            </button>
            <button onclick="setPayment('transfer')" data-method="transfer"
                class="method-btn">
                <span class="method-icon">🏦</span> Transfer
            </button>
            <button onclick="setPayment('ewallet')" data-method="ewallet"
                class="method-btn">
                <span class="method-icon">📱</span> E-Wallet
            </button>
        </div>

        <button id="pay-btn" onclick="processPayment()" class="btn-pay">
            Bayar <kbd>F5</kbd>
        </button>
        <button onclick="clearCart()" class="btn-cancel">
            Batalkan Transaksi
        </button>
    </div>

    <div id="empty-cta" class="empty-cta">
        <p>Ketuk produk untuk menambahkan ke keranjang</p>
    </div>
</div>

<style>
.cart-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px;
    background: var(--blue);
    flex-shrink: 0;
}
.cart-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 600; color: #fff;
}
.cart-badge {
    background: rgba(255,255,255,.25);
    color: #fff; font-size: 11px;
    font-family: 'DM Mono', monospace;
    padding: 1px 7px; border-radius: 99px;
}
.cart-clear {
    background: none; border: none;
    font-family: inherit; font-size: 12px;
    color: rgba(255,255,255,.7); cursor: pointer;
    transition: color .15s;
    padding: 0;
}
.cart-clear:hover { color: #fff; }

.cart-body {
    flex: 1; overflow-y: auto;
    padding: 10px;
    display: flex; flex-direction: column;
    gap: 6px;
}
.cart-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 40px 20px; gap: 6px;
}
.cart-empty-title { font-size: 14px; font-weight: 500; color: var(--text-muted); margin: 6px 0 0; }
.cart-empty-sub { font-size: 12px; color: var(--text-faint); margin: 0; }

.cart-footer {
    flex-shrink: 0;
    border-top: 1px solid var(--border);
    padding: 14px 16px;
    display: flex; flex-direction: column; gap: 12px;
    background: var(--white);
}

.summary { display: flex; flex-direction: column; gap: 6px; }
.summary-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; color: var(--text-muted);
}
.summary-row.total {
    border-top: 1px dashed var(--border);
    padding-top: 8px;
    font-size: 15px; font-weight: 600; color: var(--text);
}
.total-val { color: var(--blue); }
.disc { color: var(--red); }
.mono { font-family: 'DM Mono', monospace; }

.pay-section { display: flex; flex-direction: column; gap: 10px; }
.cash-label { font-size: 12px; font-weight: 500; color: var(--text-muted); display: block; margin-bottom: 5px; }
.cash-input-wrap { position: relative; }
.cash-prefix {
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    font-size: 13px; color: var(--text-muted); font-family: 'DM Mono', monospace;
    pointer-events: none;
}
.cash-input {
    width: 100%; height: 40px;
    padding: 0 12px 0 32px;
    font-family: 'DM Mono', monospace; font-size: 14px;
    border: 1.5px solid var(--border); border-radius: var(--radius-xs);
    outline: none; color: var(--text); background: var(--bg);
    transition: border-color .15s;
    -webkit-appearance: none;
}
.cash-input:focus { border-color: var(--blue); }

.change-row {
    display: flex; justify-content: space-between; align-items: center;
    background: var(--green-bg); border-radius: var(--radius-xs);
    padding: 9px 12px; font-size: 13px; font-weight: 500;
    color: var(--green-text);
}
.change-val { font-family: 'DM Mono', monospace; font-weight: 600; }

.method-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
.method-btn {
    display: flex; align-items: center; justify-content: center; flex-direction: column;
    gap: 3px; padding: 8px 4px;
    font-family: inherit; font-size: 11px; font-weight: 600;
    border: 1.5px solid var(--border); border-radius: var(--radius-xs);
    background: var(--white); color: var(--text-muted);
    cursor: pointer; transition: all .15s;
    -webkit-tap-highlight-color: transparent;
}
.method-btn:hover { border-color: var(--blue-mid); color: var(--blue); }
.method-btn.active { border-color: var(--blue); background: var(--blue-light); color: var(--blue); }
.method-icon { font-size: 16px; }

.btn-pay {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; height: 46px;
    background: var(--blue); color: #fff;
    font-family: inherit; font-size: 14px; font-weight: 600;
    border: none; border-radius: var(--radius-xs);
    cursor: pointer; transition: background .15s, transform .1s;
    -webkit-tap-highlight-color: transparent;
}
.btn-pay:hover { background: var(--blue-dark); }
.btn-pay:active { transform: scale(.98); }
.btn-pay:disabled { opacity: .5; cursor: not-allowed; transform: none; }
.btn-pay kbd {
    background: rgba(255,255,255,.2); color: rgba(255,255,255,.9);
    font-family: 'DM Mono', monospace; font-size: 11px;
    padding: 1px 6px; border-radius: 4px;
}

.btn-cancel {
    display: flex; align-items: center; justify-content: center;
    width: 100%; height: 38px;
    background: none; color: var(--text-muted);
    font-family: inherit; font-size: 13px; font-weight: 500;
    border: 1px solid var(--border); border-radius: var(--radius-xs);
    cursor: pointer; transition: all .15s;
    -webkit-tap-highlight-color: transparent;
}
.btn-cancel:hover { border-color: var(--red); color: var(--red); }

.empty-cta { text-align: center; font-size: 12px; color: var(--text-faint); padding: 4px 0; }
.hidden { display: none !important; }
</style>
