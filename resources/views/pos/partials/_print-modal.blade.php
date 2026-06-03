{{-- resources/views/pos/partials/_print-modal.blade.php --}}
{{--
    Modal cetak struk via PDF.

    ── Cara pasang di index.blade.php ──────────────────────────────────────
    1. Tambahkan sebelum @push('scripts'):
           @include('pos.partials._print-modal')

    2. Ganti tombol cetak di receipt-modal:
           SEBELUM: onclick="window.print()"
           SESUDAH: onclick="openPrintModal()"

    3. Tambahkan di akhir fungsi showReceipt(payData):
           window._lastPayData  = payData;
           window._lastCartSnap = state.cart.map(c => ({...c}));
    ────────────────────────────────────────────────────────────────────────
--}}

{{-- ── Modal backdrop ──────────────────────────────────────────────────── --}}
<div id="print-modal" class="pm-bg" role="dialog" aria-modal="true" aria-labelledby="pm-title">
    <div class="pm-card">

        {{-- Header --}}
        <div class="pm-header">
            <div class="pm-title" id="pm-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </div>
            <button class="pm-close" onclick="closePrintModal()" aria-label="Tutup">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="pm-body">

            {{-- Paper size selector --}}
            <div class="pm-field">
                <label class="pm-label">Ukuran Kertas</label>
                <div class="pm-seg" id="pm-paper-seg">
                    <button class="pm-seg-btn" data-val="58" onclick="setPaper(58)">
                        <span class="pm-seg-icon">📄</span>
                        <span class="pm-seg-main">58 mm</span>
                        <span class="pm-seg-sub">Mini thermal</span>
                    </button>
                    <button class="pm-seg-btn active" data-val="80" onclick="setPaper(80)">
                        <span class="pm-seg-icon">🧾</span>
                        <span class="pm-seg-main">80 mm</span>
                        <span class="pm-seg-sub">Thermal standar</span>
                    </button>
                </div>
            </div>

            {{-- Cashier name --}}
            <div class="pm-field">
                <label class="pm-label" for="pm-cashier">Nama Kasir</label>
                <input type="text" id="pm-cashier"
                    value="{{ auth('pos')->user()->name ?? '' }}"
                    class="pm-input" placeholder="Nama kasir di struk">
            </div>

            {{-- Footer note --}}
            <div class="pm-field">
                <label class="pm-label" for="pm-note">Pesan di Struk</label>
                <input type="text" id="pm-note"
                    value="Terima kasih telah berbelanja!"
                    class="pm-input" placeholder="Pesan penutup struk">
            </div>

            {{-- Status --}}
            <div id="pm-status" class="pm-status hidden"></div>

            {{-- CTA --}}
            <button id="pm-generate-btn" class="pm-btn-generate" onclick="generatePdf()">
                <svg id="pm-btn-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span id="pm-btn-label">Buat PDF & Cetak</span>
            </button>

        </div>

        {{-- Skip --}}
        <div class="pm-footer">
            <button class="pm-skip" onclick="closePrintModal()">Lewati, tidak perlu cetak</button>
        </div>

    </div>
</div>

{{-- ══ Styles ══════════════════════════════════════════════════════════════ --}}
<style>
/* Backdrop */
.pm-bg {
    display: none; position: fixed; inset: 0; z-index: 70;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    align-items: flex-end; justify-content: center;
}
@media (min-width: 600px) { .pm-bg { align-items: center; } }
.pm-bg.open { display: flex; }

/* Card */
.pm-card {
    background: var(--white);
    border-radius: 20px 20px 0 0;
    width: 100%; max-width: 420px;
    padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px));
    animation: sheetIn .26s cubic-bezier(.32,1,.36,1) both;
    max-height: 94dvh; overflow-y: auto;
}
@media (min-width: 600px) {
    .pm-card { border-radius: 20px; padding-bottom: 8px; animation: fadeUp .2s ease both; }
}

/* Header */
.pm-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 18px 0;
}
.pm-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 15px; font-weight: 700; color: var(--text);
}
.pm-close {
    width: 30px; height: 30px; border-radius: 50%;
    background: var(--bg); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); -webkit-tap-highlight-color: transparent;
    flex-shrink: 0;
}
.pm-close:hover { background: var(--border); }
.pm-close svg { width: 14px; height: 14px; }

/* Body */
.pm-body { padding: 16px 18px 4px; display: flex; flex-direction: column; gap: 14px; }

/* Field */
.pm-field { display: flex; flex-direction: column; gap: 6px; }
.pm-label { font-size: 12px; font-weight: 600; color: var(--text-muted); letter-spacing: .3px; text-transform: uppercase; }

/* Segmented paper picker */
.pm-seg { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.pm-seg-btn {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 2px; padding: 12px 8px;
    border: 2px solid var(--border); border-radius: 12px;
    background: var(--white); cursor: pointer;
    transition: all .15s; font-family: inherit;
    -webkit-tap-highlight-color: transparent;
}
.pm-seg-btn:hover { border-color: var(--blue-mid); background: var(--blue-light); }
.pm-seg-btn.active { border-color: var(--blue); background: var(--blue-light); }
.pm-seg-icon { font-size: 20px; line-height: 1; }
.pm-seg-main { font-size: 14px; font-weight: 700; color: var(--text); margin-top: 2px; }
.pm-seg-sub  { font-size: 11px; color: var(--text-muted); }
.pm-seg-btn.active .pm-seg-main { color: var(--blue); }

/* Input */
.pm-input {
    height: 40px; padding: 0 12px;
    font-family: inherit; font-size: 13px; color: var(--text);
    background: var(--bg); border: 1.5px solid var(--border);
    border-radius: 10px; outline: none; transition: border-color .15s;
    -webkit-appearance: none; width: 100%;
}
.pm-input:focus { border-color: var(--blue); background: var(--white); }

/* Status */
.pm-status {
    padding: 10px 14px; border-radius: 10px;
    font-size: 13px; font-weight: 500; line-height: 1.4;
}
.pm-status.hidden { display: none; }
.pm-status.loading { background: var(--bg); color: var(--text-muted); }
.pm-status.error   { background: var(--red-bg); color: var(--red); }
.pm-status.success { background: var(--green-bg); color: var(--green-text, #065F46); }

/* Generate button */
.pm-btn-generate {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; height: 48px;
    background: var(--blue); color: #fff;
    font-family: inherit; font-size: 14px; font-weight: 700;
    border: none; border-radius: 12px;
    cursor: pointer; transition: background .15s, transform .1s, opacity .15s;
    -webkit-tap-highlight-color: transparent;
}
.pm-btn-generate:hover   { background: var(--blue-dark); }
.pm-btn-generate:active  { transform: scale(.98); }
.pm-btn-generate:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.pm-btn-generate svg { width: 17px; height: 17px; }

/* Spin animation for loading */
@keyframes pm-spin { to { transform: rotate(360deg); } }
.pm-spin { animation: pm-spin .7s linear infinite; }

/* Footer */
.pm-footer { display: flex; justify-content: center; padding: 8px 18px 12px; }
.pm-skip {
    background: none; border: none;
    font-family: inherit; font-size: 13px; color: var(--text-faint);
    cursor: pointer; padding: 6px 12px;
    transition: color .15s; -webkit-tap-highlight-color: transparent;
}
.pm-skip:hover { color: var(--text-muted); }
</style>

{{-- ══ Script ═══════════════════════════════════════════════════════════════ --}}
<script>
// ── State ────────────────────────────────────────────────────────────────────
let _pm_paperWidth = 80;

function openPrintModal() {
    // Reset state
    _pmSetStatus('', '');
    _pmSetBtnLoading(false);
    document.getElementById('print-modal').classList.add('open');
}

function closePrintModal() {
    document.getElementById('print-modal').classList.remove('open');
}

function setPaper(mm) {
    _pm_paperWidth = mm;
    document.querySelectorAll('.pm-seg-btn').forEach(b => {
        b.classList.toggle('active', parseInt(b.dataset.val) === mm);
    });
}

// ── Generate PDF ─────────────────────────────────────────────────────────────
async function generatePdf() {
    const payData = window._lastPayData;
    const cart    = window._lastCartSnap ?? [];

    if (!payData) {
        _pmSetStatus('Data transaksi tidak ditemukan.', 'error');
        return;
    }

    const cashier = document.getElementById('pm-cashier').value.trim();
    const note    = document.getElementById('pm-note').value.trim();

    _pmSetBtnLoading(true);
    _pmSetStatus('Membuat PDF struk…', 'loading');

    const payload = {
        invoice_number : payData.invoice_number,
        total          : payData.total,
        paid           : payData.paid,
        change         : payData.change,
        payment_method : payData.payment_method ?? 'cash',
        paper_width    : _pm_paperWidth,
        cashier_name   : cashier,
        note           : note,
        sale_id        : payData.sale_id ?? null,
        items          : cart.map(c => ({
            name  : c.product.name,
            price : c.product.price,
            qty   : c.qty,
            total : c.product.price * c.qty,
        })),
    };

    try {
        const resp = await fetch('{{ route("pos.print.pdf") }}', {
            method  : 'POST',
            headers : {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await resp.json();

        if (!resp.ok || !data.success) {
            throw new Error(data.message || 'Gagal membuat PDF.');
        }

        // ── Buka PDF di tab baru ─────────────────────────────────────────
        // Konversi base64 → Blob → Object URL
        // Cara ini bekerja di semua browser + mobile (iOS Safari, Android Chrome)
        const binary   = atob(data.pdf_b64);
        const bytes    = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);

        const blob    = new Blob([bytes], { type: 'application/pdf' });
        const blobUrl = URL.createObjectURL(blob);

        // Buka tab baru
        const tab = window.open(blobUrl, '_blank');

        if (!tab) {
            // Popup diblokir → fallback: link download
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = data.filename ?? 'struk.pdf';
            a.click();
            _pmSetStatus('PDF diunduh (popup diblokir browser).', 'success');
        } else {
            _pmSetStatus('PDF berhasil dibuat! Gunakan Ctrl+P / tombol print di browser.', 'success');
        }

        // Cleanup blob URL setelah 5 menit
        setTimeout(() => URL.revokeObjectURL(blobUrl), 300_000);

        // Tutup modal setelah 2 detik
        setTimeout(() => closePrintModal(), 2000);

    } catch (err) {
        _pmSetStatus('❌ ' + err.message, 'error');
    } finally {
        _pmSetBtnLoading(false);
    }
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function _pmSetStatus(msg, type) {
    const el = document.getElementById('pm-status');
    el.textContent = msg;
    el.className = 'pm-status' + (msg ? ' ' + type : ' hidden');
}

function _pmSetBtnLoading(loading) {
    const btn   = document.getElementById('pm-generate-btn');
    const icon  = document.getElementById('pm-btn-icon');
    const label = document.getElementById('pm-btn-label');
    btn.disabled = loading;
    label.textContent = loading ? 'Membuat PDF…' : 'Buat PDF & Cetak';
    icon.className = loading ? 'pm-spin' : '';
    if (loading) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>';
    }
}
</script>
