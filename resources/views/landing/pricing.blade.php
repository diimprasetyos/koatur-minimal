<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harga & Paket — {{ config('app.name') }}</title>
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
            background: #f8fafc;
            color: #0f172a;
        }

        /* ── Navbar ── */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 22px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            gap: 32px;
        }

        .nav-links a {
            font-size: 14px;
            color: #475569;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s;
        }

        .nav-links a:hover {
            color: #2563eb;
        }

        .nav-cta {
            display: flex;
            gap: 10px;
        }

        .btn-outline {
            padding: 8px 18px;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }

        .btn-outline:hover {
            background: #eff6ff;
        }

        .btn-solid {
            padding: 8px 18px;
            background: #2563eb;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
            border: none;
            cursor: pointer;
        }

        .btn-solid:hover {
            background: #1d4ed8;
        }

        /* ── Hero ── */
        .hero {
            text-align: center;
            padding: 72px 24px 56px;
        }

        .hero-badge {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 99px;
            margin-bottom: 20px;
            border: 1px solid #bfdbfe;
        }

        .hero h1 {
            font-size: clamp(28px, 5vw, 48px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            font-size: 17px;
            color: #64748b;
            max-width: 540px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ── Toggle ── */
        .toggle-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 32px 0 48px;
        }

        .toggle-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .toggle-label.active {
            color: #0f172a;
        }

        .toggle {
            position: relative;
            width: 48px;
            height: 26px;
            background: #2563eb;
            border-radius: 99px;
            cursor: pointer;
            transition: background .2s;
        }

        .toggle::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            transition: transform .2s;
        }

        .toggle.yearly::after {
            transform: translateX(22px);
        }

        .badge-save {
            background: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 99px;
        }

        /* ── Cards ── */
        .plans-grid {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 24px 80px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            align-items: start;
        }

        .plan-card {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            padding: 32px 28px;
            position: relative;
            transition: box-shadow .2s, transform .2s;
        }

        .plan-card:hover {
            box-shadow: 0 12px 40px rgba(37, 99, 235, .1);
            transform: translateY(-2px);
        }

        .plan-card.featured {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px #dbeafe;
        }

        .plan-badge {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: #2563eb;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 99px;
            letter-spacing: .5px;
            white-space: nowrap;
        }

        .plan-name {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .plan-price {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -1.5px;
            color: #0f172a;
            line-height: 1;
        }

        .plan-price span {
            font-size: 16px;
            font-weight: 500;
            color: #94a3b8;
            letter-spacing: 0;
        }

        .plan-desc {
            font-size: 14px;
            color: #64748b;
            margin: 10px 0 24px;
            line-height: 1.6;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 28px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: #334155;
        }

        .plan-features li .icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 10px;
            font-weight: 700;
            margin-top: 1px;
        }

        .plan-features li .icon.muted {
            background: #f1f5f9;
            color: #94a3b8;
        }

        .btn-checkout {
            display: block;
            width: 100%;
            padding: 13px;
            text-align: center;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: background .2s, transform .1s;
        }

        .btn-checkout:active {
            transform: scale(.98);
        }

        .btn-checkout.primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-checkout.primary:hover {
            background: #1d4ed8;
        }

        .btn-checkout.secondary {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-checkout.secondary:hover {
            background: #e2e8f0;
        }

        /* ── Trial strip ── */
        .trial-strip {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: #fff;
            text-align: center;
            padding: 20px 24px;
        }

        .trial-strip p {
            font-size: 15px;
            font-weight: 600;
        }

        .trial-strip small {
            font-size: 13px;
            opacity: .8;
        }

        /* ── FAQ ── */
        .faq-section {
            max-width: 720px;
            margin: 0 auto;
            padding: 0 24px 80px;
        }

        .faq-section h2 {
            font-size: 28px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 32px;
            letter-spacing: -.5px;
        }

        .faq-item {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 10px;
            overflow: hidden;
            background: #fff;
        }

        .faq-q {
            padding: 18px 20px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
        }

        .faq-q .arrow {
            transition: transform .25s;
            font-size: 18px;
            color: #94a3b8;
        }

        .faq-item.open .faq-q .arrow {
            transform: rotate(180deg);
        }

        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
            font-size: 14px;
            color: #475569;
            line-height: 1.7;
        }

        .faq-item.open .faq-a {
            max-height: 300px;
        }

        .faq-a-inner {
            padding: 0 20px 18px;
        }

        /* ── Footer ── */
        footer {
            background: #0f172a;
            color: #94a3b8;
            text-align: center;
            padding: 28px 24px;
            font-size: 13px;
        }

        footer a {
            color: #94a3b8;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .nav-links {
                display: none;
            }

            .plans-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    {{-- ── Navbar ── --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ url('/') }}" class="logo">{{ config('app.name') }}</a>
            <div class="nav-links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/pricing') }}" style="color:#2563eb;">Harga</a>
                <a href="{{ url('/register') }}">Daftar</a>
            </div>
            <div class="nav-cta">
                <a href="{{ route('filament.admin.auth.login') }}" class="btn-outline">Masuk</a>
                <a href="{{ url('/register') }}" class="btn-solid">Coba Gratis</a>
            </div>
        </div>
    </nav>

    {{-- ── Trial strip ── --}}
    <div class="trial-strip">
        <p>🎁 Daftar sekarang dan nikmati <strong>3 hari trial gratis</strong> — tanpa kartu kredit</p>
        <small>Upgrade atau batalkan kapan saja</small>
    </div>

    {{-- ── Hero ── --}}
    <div class="hero">
        <span class="hero-badge">Harga Transparan, Tanpa Biaya Tersembunyi</span>
        <h1>Pilih Paket yang <span>Tepat</span><br>untuk Bisnis Anda</h1>
        <p>Mulai gratis, upgrade saat bisnis Anda berkembang. Semua paket sudah termasuk modul kasir lengkap.</p>

        {{-- Billing toggle --}}
        <div class="toggle-wrap">
            <span class="toggle-label active" id="label-monthly">Bulanan</span>
            <div class="toggle" id="billing-toggle" onclick="toggleBilling()"></div>
            <span class="toggle-label" id="label-yearly">
                Tahunan <span class="badge-save">Hemat 20%</span>
            </span>
        </div>
    </div>

    {{-- ── Plan Cards ── --}}
    <div class="plans-grid">

        @foreach ($plans as $plan)
        <div class="plan-card {{ $plan->slug === 'pro' ? 'featured' : '' }}">
            @if ($plan->slug === 'pro')
            <div class="plan-badge">⭐ TERPOPULER</div>
            @endif

            <div class="plan-name">{{ $plan->name }}</div>

            <div class="plan-price" id="price-{{ $plan->slug }}">
                {{ $plan->formattedPrice() }}
            </div>

            <p class="plan-desc">{{ $plan->description }}</p>

            <ul class="plan-features">
                <li>
                    <span class="icon">✓</span>
                    {{ $plan->max_tenants === 1 ? '1 Outlet / Toko' : $plan->max_tenants . ' Outlet' }}
                </li>
                <li>
                    <span class="icon">✓</span>
                    {{ $plan->max_users_per_tenant }} pengguna per toko
                </li>
                <li>
                    <span class="icon">✓</span>
                    {{ $plan->max_products > 0 ? number_format($plan->max_products) . ' produk' : 'Produk tidak terbatas' }}
                </li>
                <li>
                    <span class="icon">✓</span>
                    Modul kasir & manajemen stok
                </li>
                <li>
                    <span class="icon">✓</span>
                    Laporan harian & bulanan
                </li>
                @if ($plan->hasFeature('export'))
                <li>
                    <span class="icon">✓</span>
                    Export laporan (Excel / PDF)
                </li>
                @else
                <li>
                    <span class="icon muted">–</span>
                    <span style="color:#94a3b8;">Export laporan</span>
                </li>
                @endif
                @if ($plan->hasFeature('priority_support'))
                <li>
                    <span class="icon">✓</span>
                    Priority support
                </li>
                @endif
                <li>
                    <span class="icon">✓</span>
                    Struk & barcode
                </li>
            </ul>

            {{-- Jika sudah login, langsung ke billing checkout. Jika belum, ke register. --}}
            @auth
            <form action="{{ route('billing.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                <button type="submit" class="btn-checkout {{ $plan->slug === 'pro' ? 'primary' : 'secondary' }}">
                    Pilih {{ $plan->name }}
                </button>
            </form>
            @else
            <a href="{{ url('/checkout/' . $plan->slug) }}" class="btn-checkout {{ $plan->slug === 'pro' ? 'primary' : 'secondary' }}">
                Mulai Gratis 3 Hari
            </a>
            @endauth
        </div>
        @endforeach

    </div>

    {{-- ── FAQ ── --}}
    <div class="faq-section">
        <h2>Pertanyaan Umum</h2>

        @php
        $faqs = [
        ['q' => 'Apakah ada masa percobaan gratis?',
        'a' => 'Ya! Setiap akun baru mendapat 3 hari trial gratis tanpa perlu memasukkan data kartu kredit. Anda bisa langsung menggunakan semua fitur secara penuh selama masa trial.'],
        ['q' => 'Bagaimana cara melakukan pembayaran?',
        'a' => 'Kami mendukung berbagai metode pembayaran melalui Xendit: transfer bank (BCA, Mandiri, BNI, BRI), virtual account, QRIS, dan kartu kredit/debit.'],
        ['q' => 'Apakah bisa ganti paket kapan saja?',
        'a' => 'Ya, Anda bisa upgrade atau downgrade paket kapan saja dari halaman Billing di dashboard. Perubahan berlaku di periode berikutnya.'],
        ['q' => 'Apakah data saya aman?',
        'a' => 'Semua data disimpan di server yang terenkripsi. Kami tidak pernah menjual atau membagikan data Anda ke pihak ketiga.'],
        ['q' => 'Berapa banyak pengguna yang bisa saya tambahkan?',
        'a' => 'Tergantung paket: paket Basic mendukung 3 pengguna per toko, paket Pro mendukung lebih banyak. Setiap pengguna bisa diberikan role berbeda (owner, manager, kasir).'],
        ];
        @endphp

        @foreach ($faqs as $i => $faq)
        <div class="faq-item" id="faq-{{ $i }}">
            <div class="faq-q" onclick="toggleFaq({{ $i }})">
                <span>{{ $faq['q'] }}</span>
                <span class="arrow">⌄</span>
            </div>
            <div class="faq-a">
                <div class="faq-a-inner">{{ $faq['a'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Footer ── --}}
    <footer>
        <p>© {{ date('Y') }} {{ config('app.name') }} by Pilar Pasific Code —
            <a href="mailto:pilarpasificcode@gmail.com">pilarpasificcode@gmail.com</a> ·
            <a href="tel:+6281247758775">+62 812 4775 8775</a>
        </p>
        <p style="margin-top:8px;">
            <a href="{{ url('/') }}">Home</a> &nbsp;·&nbsp;
            <a href="{{ url('/register') }}">Daftar</a> &nbsp;·&nbsp;
            <a href="{{ route('filament.admin.auth.login') }}">Login</a>
        </p>
    </footer>

    <script>
        // ── Billing toggle ──
        let isYearly = false;

        function toggleBilling() {
            isYearly = !isYearly;
            const toggle = document.getElementById('billing-toggle');
            const labelM = document.getElementById('label-monthly');
            const labelY = document.getElementById('label-yearly');

            toggle.classList.toggle('yearly', isYearly);
            labelM.classList.toggle('active', !isYearly);
            labelY.classList.toggle('active', isYearly);

            // Update harga tiap plan (20% discount kalau tahunan)
            const prices = @json($plans -> mapWithKeys(fn($p) => [$p -> slug => $p -> price]));
            for (const [slug, monthly] of Object.entries(prices)) {
                const el = document.getElementById('price-' + slug);
                if (!el || monthly === 0) continue;

                if (isYearly) {
                    const yearly = Math.round(monthly * 12 * 0.8);
                    el.innerHTML = 'Rp ' + yearly.toLocaleString('id-ID') + '<span> / tahun</span>';
                } else {
                    el.innerHTML = 'Rp ' + monthly.toLocaleString('id-ID') + '<span> / bulan</span>';
                }
            }
        }

        // ── FAQ accordion ──
        function toggleFaq(i) {
            const item = document.getElementById('faq-' + i);
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item').forEach(el => el.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        }
    </script>

</body>

</html>