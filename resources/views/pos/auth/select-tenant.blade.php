{{-- resources/views/pos/auth/select-tenant.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Pilih Toko — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --blue: #2563EB; --blue-dark: #1D4ED8;
            --blue-light: #EFF6FF; --blue-mid: #BFDBFE;
            --text: #0F172A; --text-muted: #64748B;
            --border: #E2E8F0; --bg: #F8FAFC; --white: #FFFFFF;
            --red: #EF4444; --red-bg: #FEF2F2; --red-border: #FECACA;
            --radius: 14px; --radius-sm: 10px;
            --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--text);
            -webkit-font-smoothing: antialiased;
        }
        .page {
            min-height: 100dvh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 24px 20px;
            padding-bottom: calc(24px + env(safe-area-inset-bottom));
        }
        .wrap { width: 100%; max-width: 400px; }
        .brand {
            display: flex; flex-direction: column; align-items: center;
            gap: 10px; margin-bottom: 28px; text-align: center;
        }
        .brand-icon {
            width: 56px; height: 56px; background: var(--blue);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon svg { width: 26px; height: 26px; color: #fff; }
        .brand-name { font-size: 20px; font-weight: 600; letter-spacing: -.3px; }
        .brand-sub { font-size: 14px; color: var(--text-muted); margin-top: -4px; }
        .brand-sub strong { color: var(--text); font-weight: 600; }

        .card {
            background: var(--white); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 20px;
        }
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            background: var(--red-bg); border: 1px solid var(--red-border);
            border-radius: var(--radius-sm); padding: 12px 14px;
            margin-bottom: 16px; font-size: 14px; color: var(--red);
        }

        /* Tenant list */
        .tenant-list { display: flex; flex-direction: column; gap: 10px; }
        .tenant-label {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: border-color .15s, background .15s;
            -webkit-tap-highlight-color: transparent;
        }
        .tenant-label:hover { border-color: var(--blue-mid); background: var(--blue-light); }
        .tenant-label:has(input:checked) {
            border-color: var(--blue);
            background: var(--blue-light);
        }
        .tenant-radio {
            width: 20px; height: 20px;
            accent-color: var(--blue);
            flex-shrink: 0; cursor: pointer;
        }
        .tenant-info { flex: 1; min-width: 0; }
        .tenant-name { font-size: 15px; font-weight: 600; color: var(--text); }
        .tenant-slug { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .tenant-arrow { color: var(--border); flex-shrink: 0; }
        .tenant-arrow svg { width: 16px; height: 16px; }

        .btn-primary {
            display: flex; align-items: center; justify-content: center;
            width: 100%; height: 50px; margin-top: 16px;
            background: var(--blue); color: #fff;
            font-family: inherit; font-size: 15px; font-weight: 600;
            border: none; border-radius: var(--radius-sm);
            cursor: pointer; transition: background .15s, transform .1s;
            -webkit-tap-highlight-color: transparent;
        }
        .btn-primary:hover { background: var(--blue-dark); }
        .btn-primary:active { transform: scale(.98); }

        .switch-account {
            text-align: center; margin-top: 20px;
        }
        .switch-btn {
            background: none; border: none;
            font-family: inherit; font-size: 14px;
            color: var(--text-muted); cursor: pointer;
            padding: 8px 16px;
            transition: color .15s;
            -webkit-tap-highlight-color: transparent;
        }
        .switch-btn:hover { color: var(--text); }
    </style>
</head>
<body>
<div class="page">
    <div class="wrap">
        <div class="brand">
            <div class="brand-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="brand-name">Pilih Toko</div>
                <div class="brand-sub">Halo, <strong>{{ auth('pos')->user()->name }}</strong>. Pilih toko untuk masuk.</div>
            </div>
        </div>

        <div class="card">
            @error('tenant_id')
                <div class="alert">{{ $message }}</div>
            @enderror

            <form method="POST" action="{{ route('pos.select-tenant.post') }}">
                @csrf
                <div class="tenant-list">
                    @foreach($tenants as $tenant)
                    <label class="tenant-label">
                        <input type="radio" name="tenant_id" value="{{ $tenant->id }}"
                            class="tenant-radio"
                            {{ old('tenant_id') == $tenant->id ? 'checked' : '' }}>
                        <div class="tenant-info">
                            <div class="tenant-name">{{ $tenant->name }}</div>
                            @if($tenant->slug)
                                <div class="tenant-slug">{{ $tenant->slug }}</div>
                            @endif
                        </div>
                        <div class="tenant-arrow">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </label>
                    @endforeach
                </div>

                <button type="submit" class="btn-primary">Masuk ke Kasir</button>
            </form>
        </div>

        <div class="switch-account">
            <form method="POST" action="{{ route('pos.logout') }}">
                @csrf
                <button type="submit" class="switch-btn">← Ganti akun</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
