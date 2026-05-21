<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 p-6">

    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Billing & Langganan</h1>
                <p class="text-gray-500 text-sm mt-1">Halo, {{ auth()->user()->name }}</p>
            </div>
            <a href="/admin" class="text-sm text-indigo-600 hover:underline">← Kembali ke POS</a>
        </div>

        {{-- Flash messages --}}
        @if (session('warning'))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 mb-6 text-sm">
                ⚠️ {{ session('warning') }}
            </div>
        @endif
        @if (session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6 text-sm">
                ℹ️ {{ session('info') }}
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6 text-sm">
                ✓ {{ session('success') }}
            </div>
        @endif

        {{-- Status Subscription Sekarang --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Status Langganan</h2>

            @if ($subscription && $subscription->isActive())
                <div class="flex items-center gap-3">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $subscription->isTrial() ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                        {{ $subscription->isTrial() ? '⏳ Trial' : '✓ Aktif' }}
                    </span>
                    <span class="text-gray-900 font-semibold">{{ $subscription->plan->name }}</span>
                </div>

                @if ($subscription->expires_at)
                    <p class="text-sm text-gray-500 mt-2">
                        Berakhir: <span
                            class="font-medium text-gray-700">{{ $subscription->expires_at->format('d M Y') }}</span>
                        @if ($subscription->daysRemaining() !== null)
                            <span class="text-{{ $subscription->daysRemaining() <= 3 ? 'red' : 'gray' }}-500">
                                ({{ $subscription->daysRemaining() }} hari lagi)
                            </span>
                        @endif
                    </p>
                @endif
            @else
                <div class="flex items-center gap-3">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                        ✗ Tidak aktif
                    </span>
                    <span class="text-gray-500 text-sm">Pilih plan di bawah untuk melanjutkan.</span>
                </div>
            @endif
        </div>

        {{-- Pilihan Plan --}}
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pilih Plan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            @foreach ($plans as $plan)
                <div
                    class="bg-white rounded-2xl border {{ $plan->slug === 'pro' ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-gray-200' }} p-6">

                    @if ($plan->slug === 'pro')
                        <span
                            class="inline-block bg-indigo-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full mb-3">
                            Rekomendasi
                        </span>
                    @endif

                    <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1 mb-4">{{ $plan->description }}</p>

                    <p class="text-2xl font-bold text-gray-900 mb-1">
                        {{ $plan->formattedPrice() }}
                    </p>

                    <ul class="text-sm text-gray-600 space-y-1 mb-6">
                        <li>✓ Maks. {{ $plan->max_tenants }} toko</li>
                        <li>✓ {{ $plan->max_users_per_tenant }} user per toko</li>
                        <li>✓
                            {{ $plan->max_products > 0 ? number_format($plan->max_products) . ' produk' : 'Produk unlimited' }}
                        </li>
                        @if ($plan->hasFeature('export'))
                            <li>✓ Export laporan</li>
                        @endif
                        @if ($plan->hasFeature('priority_support'))
                            <li>✓ Priority support</li>
                        @endif
                    </ul>

                    <form action="{{ route('billing.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                        <button type="submit"
                            class="w-full {{ $plan->slug === 'pro' ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}
                               font-semibold rounded-lg px-4 py-2.5 text-sm transition-colors">
                            Pilih {{ $plan->name }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        {{-- Invoice History --}}
        @if ($invoices->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Riwayat Invoice</h2>
                <div class="divide-y divide-gray-100">
                    @foreach ($invoices as $invoice)
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $invoice->invoice_number }}</p>
                                <p class="text-xs text-gray-400">{{ $invoice->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ $invoice->formattedAmount() }}</p>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $invoice->isPaid() ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</body>

</html>
