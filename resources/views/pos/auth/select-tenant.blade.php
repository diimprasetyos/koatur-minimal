{{-- resources/views/pos/auth/select-tenant.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Toko — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }</style>
</head>
<body class="h-full bg-gradient-to-br from-slate-100 via-blue-50 to-slate-100">

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 mb-4 shadow-lg shadow-blue-200">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Pilih Toko</h1>
            <p class="text-sm text-slate-500 mt-1">Halo, <strong>{{ auth('pos')->user()->name }}</strong>. Anda terdaftar di beberapa toko.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 p-6">

            @error('tenant_id')
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('pos.select-tenant.post') }}" class="space-y-3">
                @csrf

                @foreach($tenants as $tenant)
                <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-slate-200 hover:border-blue-400 cursor-pointer transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                    <input type="radio" name="tenant_id" value="{{ $tenant->id }}" class="w-4 h-4 accent-blue-600"
                        {{ old('tenant_id') == $tenant->id ? 'checked' : '' }}>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm">{{ $tenant->name }}</p>
                        @if($tenant->slug)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $tenant->slug }}</p>
                        @endif
                    </div>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </label>
                @endforeach

                <button type="submit"
                    class="w-full mt-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm shadow-blue-200">
                    Masuk ke Kasir
                </button>
            </form>
        </div>

        <div class="text-center mt-5">
            <form method="POST" action="{{ route('pos.logout') }}">
                @csrf
                <button class="text-sm text-slate-400 hover:text-slate-600 transition-colors">
                    ← Ganti akun
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>