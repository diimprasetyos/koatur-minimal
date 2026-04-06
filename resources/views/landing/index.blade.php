<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koatur - Kelola Toko Lebih Mudah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

    {{-- ======================== HEADER ======================== --}}
    <header class="border-b border-gray-200 sticky top-0 bg-white z-50">
        <div class="max-w-7xl mx-auto px-4 md:px-8 py-4 flex items-center justify-between">

            {{-- Logo --}}
            <div class="text-3xl font-bold text-blue-600">Koatur</div>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ url('/') }}" class="text-gray-900 font-medium hover:text-blue-600 transition">Home</a>
                <a href="#layanan" class="text-gray-600 hover:text-gray-900 transition">Layanan</a>
                <a href="#fitur" class="text-gray-600 hover:text-gray-900 transition">Fitur</a>
            </nav>

            {{-- Desktop CTA --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('filament.admin.auth.login') }}"
                    class="px-6 py-2 border border-blue-600 text-blue-600 rounded-md hover:bg-gray-50 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Login
                </a>
                <a href="#"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Sign up
                </a>
            </div>

            {{-- Mobile Hamburger Button --}}
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100 transition"
                aria-label="Toggle menu" aria-expanded="false">
                <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu"
            class="md:hidden hidden border-t border-gray-100 bg-white px-4 pb-4 space-y-1 shadow-md">
            <a href="{{ url('/') }}"
                class="block py-3 px-2 text-gray-900 font-medium hover:text-blue-600 hover:bg-gray-50 rounded-md transition">
                Home
            </a>
            <a href="#layanan"
                class="block py-3 px-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition">
                Layanan
            </a>
            <a href="#fitur"
                class="block py-3 px-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition">
                Fitur
            </a>
            <div class="pt-3 flex flex-col gap-2">
                {{-- Mobile Login: pilihan role --}}
                <div class="space-y-2">
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="flex items-center gap-2 w-full py-2.5 px-4 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-50 transition font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Login
                    </a>
                </div>
                <a href="#"
                    class="w-full text-center py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                    Sign up
                </a>
            </div>
        </div>
    </header>

    {{-- ======================== HERO ======================== --}}
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-12 md:py-24">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div class="space-y-6 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">
                    Kelola Toko Lebih <span class="text-blue-600">Mudah</span>, Kasir Lebih <span
                        class="text-blue-600">Cepat</span>
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Atur stok, catat penjualan, dan pantau omzet setiap hari. Semua dalam satu aplikasi POS yang
                    praktis untuk toko kelontong dan frozen food.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium text-center">
                        Coba Sekarang
                    </a>
                    <a href="#fitur"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition font-medium text-center">
                        Lihat Fitur
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('img/hero.jpg') }}" alt="POS System in action"
                        class="w-full h-auto object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- ======================== FEATURES GRID ======================== --}}
    <section id="fitur" class="bg-slate-50 py-12 md:py-24">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">

                <div class="flex flex-col items-center text-center gap-3 p-5 md:p-6 bg-white rounded-xl shadow-sm">
                    <div class="bg-blue-600 text-white p-3 rounded-xl flex items-center justify-center">
                        <img src="{{ asset('icon/calculator.svg') }}" alt="Kasir" class="w-10 h-10 md:w-12 md:h-12">
                    </div>
                    <h2 class="text-sm md:text-base font-semibold text-gray-900">Kasir Cepat dan Mudah</h2>
                </div>

                <div class="flex flex-col items-center text-center gap-3 p-5 md:p-6 bg-white rounded-xl shadow-sm">
                    <div class="bg-blue-600 text-white p-3 rounded-xl flex items-center justify-center">
                        <img src="{{ asset('icon/rectangle-stack.svg') }}" alt="Stok" class="w-10 h-10 md:w-12 md:h-12">
                    </div>
                    <h2 class="text-sm md:text-base font-semibold text-gray-900">Atur Stok</h2>
                </div>

                <div class="flex flex-col items-center text-center gap-3 p-5 md:p-6 bg-white rounded-xl shadow-sm">
                    <div class="bg-blue-600 text-white p-3 rounded-xl flex items-center justify-center">
                        <img src="{{ asset('icon/newspaper.svg') }}" alt="Laporan" class="w-10 h-10 md:w-12 md:h-12">
                    </div>
                    <h2 class="text-sm md:text-base font-semibold text-gray-900">Laporan Jelas</h2>
                </div>

                <div class="flex flex-col items-center text-center gap-3 p-5 md:p-6 bg-white rounded-xl shadow-sm">
                    <div class="bg-blue-600 text-white p-3 rounded-xl flex items-center justify-center">
                        <img src="{{ asset('icon/device.svg') }}" alt="Device" class="w-10 h-10 md:w-12 md:h-12">
                    </div>
                    <h2 class="text-sm md:text-base font-semibold text-gray-900">Support HP / Tablet / PC</h2>
                </div>

            </div>
        </div>
    </section>

    {{-- ======================== FEATURE PREVIEW / CAROUSEL ======================== --}}
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-12 md:py-24">
        <div class="text-center mb-10 md:mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Pratinjau Fitur</h2>
            <p class="text-gray-600 text-lg">Lihat bagaimana fitur-fitur kami bekerja</p>
        </div>

        <div class="relative">
            {{-- Carousel Container --}}
            <div class="card overflow-hidden shadow-xl bg-gray-100 relative h-64 md:h-96">

                @php
                    $slides = [
                        [
                            'img'   => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&h=400&fit=crop',
                            'alt'   => 'Laporan Penjualan Dashboard',
                            'title' => 'Laporan Penjualan',
                            'desc'  => 'Analitik lengkap dan real-time untuk setiap transaksi',
                        ],
                        [
                            'img'   => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&h=400&fit=crop',
                            'alt'   => 'Manajemen Stok',
                            'title' => 'Manajemen Stok',
                            'desc'  => 'Kelola inventori dengan mudah dan pantau stok secara real-time',
                        ],
                        [
                            'img'   => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&h=400&fit=crop',
                            'alt'   => 'Sistem Kasir',
                            'title' => 'Sistem Kasir Cepat',
                            'desc'  => 'Interface kasir intuitif untuk transaksi yang lebih cepat dan akurat',
                        ],
                        [
                            'img'   => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=1200&h=400&fit=crop',
                            'alt'   => 'Multi Device Support',
                            'title' => 'Multi Device Support',
                            'desc'  => 'Akses dari mobile, tablet, atau desktop dengan sinkronisasi otomatis',
                        ],
                    ];
                @endphp

                @foreach ($slides as $index => $slide)
                    <div class="carousel-slide {{ $index === 0 ? 'active' : '' }} absolute w-full h-full transition-opacity duration-500 ease-in-out">
                        <img src="{{ $slide['img'] }}" alt="{{ $slide['alt'] }}" class="w-full h-full object-cover">
                        <div
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4 md:p-6">
                            <h3 class="text-white text-lg md:text-xl font-bold">{{ $slide['title'] }}</h3>
                            <p class="text-white/80 text-xs md:text-sm">{{ $slide['desc'] }}</p>
                        </div>
                    </div>
                @endforeach

                {{-- Prev Button --}}
                <button onclick="changeSlide(-1)"
                    class="absolute left-3 md:left-4 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white text-blue-600 rounded-full p-2 md:p-3 transition shadow-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Next Button --}}
                <button onclick="changeSlide(1)"
                    class="absolute right-3 md:right-4 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white text-blue-600 rounded-full p-2 md:p-3 transition shadow-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            {{-- Carousel Dots --}}
            <div class="flex justify-center gap-2 mt-4 md:mt-6">
                @foreach ($slides as $index => $slide)
                    <button onclick="goToSlide({{ $index }})"
                        class="carousel-dot {{ $index === 0 ? 'active' : '' }} w-3 h-3 rounded-full bg-gray-300 transition-all cursor-pointer hover:bg-gray-400">
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================== PRICING ======================== --}}
    <section id="layanan" class="bg-slate-50 py-12 md:py-24">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Layanan Kami</h2>
                <p class="text-gray-600 text-lg">Pilih paket yang sesuai kebutuhan bisnis Anda</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 items-stretch">

                {{-- Basic Plan --}}
                <div
                    class="p-6 md:p-8 bg-white rounded-2xl shadow-md border flex flex-col justify-between hover:shadow-xl transition">
                    <div>
                        <h3 class="text-xl font-bold">Basic Plan</h3>
                        <p class="text-sm text-gray-600 mt-1 mb-6">Untuk toko kecil yang baru mulai</p>
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-blue-600">Rp 79.000</span>
                            <span class="text-gray-600">/ Bulan</span>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> 1 Outlet</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Maks 2 Perangkat</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Modul Kasir Lengkap</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Manajemen Stok & Harga</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Laporan Harian</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Support Print Struk</li>
                        </ul>
                    </div>
                    <a href="#"
                        class="mt-8 w-full block text-center py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                        Mulai dengan Basic Plan
                    </a>
                </div>

                {{-- Advance Plan (Recommended) --}}
                <div
                    class="p-6 md:p-8 bg-white rounded-2xl shadow-lg border-2 border-blue-600 flex flex-col justify-between scale-100 sm:scale-[1.02]">
                    <div>
                        <span
                            class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full mb-4 inline-block">
                            RECOMMENDED
                        </span>
                        <h3 class="text-xl font-bold">Advance Plan</h3>
                        <p class="text-sm text-gray-600 mt-1 mb-6">Untuk toko dengan banyak transaksi</p>
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-blue-600">Rp 204.000</span>
                            <span class="text-gray-600">/ Bulan</span>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> 5 Outlet</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Maks 15 Perangkat</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Modul Kasir Lengkap</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Manajemen Stok & Harga</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Laporan Harian</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Support Print Struk & Barcode</li>
                        </ul>
                    </div>
                    <a href="#"
                        class="mt-8 w-full block text-center py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                        Mulai dengan Advance Plan
                    </a>
                </div>

                {{-- Pro Plan --}}
                <div
                    class="p-6 md:p-8 bg-white rounded-2xl shadow-md border flex flex-col justify-between hover:shadow-xl transition sm:col-span-2 lg:col-span-1">
                    <div>
                        <h3 class="text-xl font-bold">Pro Plan</h3>
                        <p class="text-sm text-gray-600 mt-1 mb-6">Untuk enterprise dan multi-outlet</p>
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-blue-600">Rp 899.000</span>
                            <span class="text-gray-600">/ Bulan</span>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Unlimited Outlet</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Unlimited Perangkat</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Modul Kasir Lengkap</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Multi-user (Kasir & Admin)</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Laporan Profit & Analisa Produk</li>
                            <li class="flex gap-2"><span class="text-blue-600 font-bold">✓</span> Support Print Struk & Barcode</li>
                        </ul>
                    </div>
                    <a href="#"
                        class="mt-8 w-full block text-center py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                        Mulai dengan Pro Plan
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ======================== FOOTER ======================== --}}
    <footer class="bg-blue-600 text-white py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10 md:mb-12">
                <div class="col-span-2 md:col-span-1">
                    <div class="text-3xl font-bold mb-2">Koatur </div>
                    <p class="text-white/70 text-sm mt-2">Solusi kasir modern untuk toko Anda.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="hover:opacity-80 transition">Home</a></li>
                        <li><a href="#layanan" class="hover:opacity-80 transition">Layanan Kami</a></li>
                        <li><a href="#fitur" class="hover:opacity-80 transition">Pratinjau Fitur</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/cdn-cgi/l/email-protection#a0d3d5d0d0cfd2d4e0c5d8c1cdd0ccc58ec3cfcd" class="hover:opacity-80 transition"><span class="__cf_email__" data-cfemail="f2818782829d8086b2978a939f829e97dc919d9f">[email&#160;protected]</span></a></li>
                        <li><a href="tel:+1234567890" class="hover:opacity-80 transition">+123.XXXX.XXXX</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Sosial Media</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:opacity-80 transition">Instagram</a></li>
                        <li><a href="#" class="hover:opacity-80 transition">Facebook</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 pt-8">
                <p class="text-sm text-center opacity-80">© {{ date('Y') }} Your Company. All rights reserved.</p>
            </div>
        </div>
    </footer>

 <script>
    // ---- Carousel ----
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    let autoplayInterval;

    function showSlide(index) {
        slides.forEach(s => {
            s.classList.remove('active');
            s.style.opacity = '0';
            s.style.zIndex = '0';
        });
        dots.forEach(d => d.classList.remove('active', 'bg-blue-600'));

        slides[index].classList.add('active');
        slides[index].style.opacity = '1';
        slides[index].style.zIndex = '10';
        dots[index].classList.add('active', 'bg-blue-600');
        currentSlide = index;
    }

    function changeSlide(direction) {
        let next = (currentSlide + direction + slides.length) % slides.length;
        showSlide(next);
        resetAutoplay();
    }

    function goToSlide(index) {
        showSlide(index);
        resetAutoplay();
    }

    function startAutoplay() {
        autoplayInterval = setInterval(() => changeSlide(1), 4000);
    }

    function resetAutoplay() {
        clearInterval(autoplayInterval);
        startAutoplay();
    }

    showSlide(0);
    startAutoplay();

    // ---- Mobile Menu ----
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    btn.addEventListener('click', () => {
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        iconOpen.classList.toggle('hidden', !isOpen);
        iconClose.classList.toggle('hidden', isOpen);
        btn.setAttribute('aria-expanded', String(!isOpen));
    });

    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menu.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        });
    });
</script>

</body>

</html>