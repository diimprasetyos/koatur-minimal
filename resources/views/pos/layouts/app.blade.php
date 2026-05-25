{{-- resources/views/pos/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --blue: #2563EB; --blue-dark: #1D4ED8;
            --blue-light: #EFF6FF; --blue-mid: #BFDBFE;
            --text: #0F172A; --text-muted: #64748B; --text-faint: #94A3B8;
            --border: #E2E8F0; --bg: #F1F5F9; --white: #FFFFFF;
            --green: #10B981; --green-bg: #ECFDF5; --green-text: #065F46;
            --red: #EF4444; --red-bg: #FEF2F2;
            --radius: 14px; --radius-sm: 10px; --radius-xs: 7px;
            --header-h: 58px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        html, body { height: 100%; margin: 0; overflow: hidden; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 3px; height: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

        [x-cloak] { display: none !important; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .2s ease both; }

        @keyframes sheetIn {
            from { transform: translateY(100%); }
            to   { transform: translateY(0); }
        }
        .sheet-in { animation: sheetIn .28s cubic-bezier(.32,1,.36,1) both; }
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
