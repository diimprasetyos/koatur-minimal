<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout {{ $plan->name }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 20px;
            font-weight: 800;
            color: #2563eb;
            text-decoration: none;
        }

        .topbar-secure {
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-secure svg {
            color: #22c55e;
        }

        .page {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .checkout-wrap {
            width: 100%;
            max-width: 840px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 32px;
        }

        /* Order summary */
        .summary-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .plan-name-big {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .plan-desc {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-top: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .price-row:last-child {
            border-top: 2px solid #e2e8f0;
            font-weight: 700;
            font-size: 16px;
        }

        .price-row .label {
            color: #64748b;
        }

        .price-row .val {
            color: #0f172a;
        }

        .feature-list {
            list-style: none;
            margin: 20px 0 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .feature-list li {
            font-size: 13px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-list li::before {
            content: '✓';
            color: #2563eb;
            font-weight: 700;
            font-size: 12px;
        }

        .trial-badge {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #1d4ed8;
            font-weight: 500;
            margin-top: 20px;
        }

        /* Form */
        .form-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .field input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            outline: none;
            font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
            background: #f8fafc;
        }

        .field input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
            background: #fff;
        }

        .field input.error {
            border-color: #ef4444;
        }

        .field .err-msg {
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
        }

        .btn-pay {
            width: 100%;
            padding: 14px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background .2s;
            margin-top: 6px;
        }

        .btn-pay:hover {
            background: #1d4ed8;
        }

        .btn-pay:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 14px;
        }

        .back-link {
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 20px;
        }

        .back-link:hover {
            color: #2563eb;
        }

        .global-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        @media (max-width: 680px) {
            .checkout-wrap {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <a href="{{ url('/') }}" class="logo">{{ config('app.name') }}</a>
        <div class="topbar-secure">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2" />
                <path d="M7 11V7a5 5 0 0110 0v4" />
            </svg>
            Pembayaran aman via Xendit
        </div>
    </div>

    <div class="page">
        <div class="checkout-wrap">

            {{-- ── Kiri: Form ── --}}
            <div>
                <a href="{{ url('/pricing') }}" class="back-link">
                    ← Kembali ke halaman harga
                </a>

                <div class="card">
                    <div class="form-title">Detail Pemesanan</div>
                    <p class="form-sub">Isi data di bawah untuk melanjutkan ke halaman pembayaran.</p>

                    @if ($errors->has('checkout'))
                    <div class="global-err">{{ $errors->first('checkout') }}</div>
                    @endif

                    <form action="{{ route('checkout.process', $plan->slug) }}" method="POST" id="checkoutForm">
                        @csrf

                        <div class="field">
                            <label for="name">Nama Lengkap <span style="color:#ef4444">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Nama Anda"
                                class="{{ $errors->has('name') ? 'error' : '' }}" required>
                            @error('name')<p class="err-msg">{{ $message }}</p>@enderror
                        </div>

                        <div class="field">
                            <label for="email">Alamat Email <span style="color:#ef4444">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                class="{{ $errors->has('email') ? 'error' : '' }}" required>
                            @error('email')<p class="err-msg">{{ $message }}</p>@enderror
                        </div>

                        <div class="field">
                            <label for="phone">Nomor WhatsApp <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                placeholder="+62 812-XXXX-XXXX">
                        </div>

                        <button type="submit" class="btn-pay" id="payBtn">
                            Lanjut ke Pembayaran →
                        </button>

                        <p class="secure-note">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0110 0v4" />
                            </svg>
                            SSL 256-bit · Diproses aman oleh Xendit
                        </p>
                    </form>
                </div>
            </div>

            {{-- ── Kanan: Order Summary ── --}}
            <div class="card">
                <div class="summary-label">Ringkasan Pesanan</div>

                <div class="plan-name-big">{{ $plan->name }}</div>
                <p class="plan-desc">{{ $plan->description }}</p>

                <div class="price-row">
                    <span class="label">Harga langganan</span>
                    <span class="val">{{ $plan->formattedPrice() }}</span>
                </div>
                <div class="price-row">
                    <span class="label">Durasi</span>
                    <span class="val">30 hari</span>
                </div>
                <div class="price-row">
                    <span class="label">Total</span>
                    <span class="val" style="color:#2563eb;">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                </div>

                <ul class="feature-list">
                    <li>{{ $plan->max_tenants }} outlet / toko</li>
                    <li>{{ $plan->max_users_per_tenant }} pengguna per toko</li>
                    <li>{{ $plan->max_products > 0 ? number_format($plan->max_products).' produk' : 'Produk tidak terbatas' }}</li>
                    <li>Modul kasir & manajemen stok</li>
                    <li>Laporan harian & bulanan</li>
                    @if ($plan->hasFeature('export'))
                    <li>Export laporan (Excel/PDF)</li>
                    @endif
                </ul>

                <div class="trial-badge">
                    🎁 Atau <a href="{{ url('/register') }}" style="color:#2563eb;font-weight:700;">daftar dulu</a>
                    dan nikmati <strong>10 hari trial gratis</strong> sebelum berlangganan.
                </div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function() {
            const btn = document.getElementById('payBtn');
            btn.disabled = true;
            btn.textContent = 'Menghubungkan ke Xendit...';
        });
    </script>
</body>

</html>