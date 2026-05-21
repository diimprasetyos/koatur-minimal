<x-filament-widgets::widget>
    <x-filament::section>

        {{-- Filter Bar --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-white/10">

            {{-- Label --}}
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mr-1">Filter:</span>

            {{-- Periode --}}
            <div class="flex rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden text-xs font-medium">
                @foreach (['today' => 'Hari Ini', 'week' => 'Minggu', 'month' => 'Bulan', 'year' => 'Tahun'] as $val => $label)
                    <button wire:click="$set('period', '{{ $val }}')"
                        class="px-3 py-1.5 transition-colors
                            {{ $this->period === $val
                                ? 'bg-blue-600 text-white'
                                : 'bg-white dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/10' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Status --}}
            <select wire:model.live="saleStatus"
                class="text-xs border border-gray-200 dark:border-white/10 rounded-lg px-3 py-1.5 bg-white dark:bg-white/5 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="all">Semua Status</option>
                <option value="paid">Lunas</option>
                <option value="pending">Pending</option>
                <option value="cancelled">Dibatalkan</option>
            </select>

            {{-- Metode Pembayaran --}}
            <select wire:model.live="paymentMethod"
                class="text-xs border border-gray-200 dark:border-white/10 rounded-lg px-3 py-1.5 bg-white dark:bg-white/5 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="all">Semua Pembayaran</option>
                <option value="cash">Tunai</option>
                <option value="transfer">Transfer</option>
                <option value="ewallet">E-Wallet</option>
            </select>

        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach ($this->getStats() as $stat)
                <div
                    class="rounded-xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-5 py-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">{{ $stat->getLabel() }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $stat->getValue() }}</p>
                    @if ($stat->getDescription())
                        <div class="flex items-center gap-1.5 text-xs">
                            @if ($stat->getDescriptionIcon())
                                <x-dynamic-component :component="$stat->getDescriptionIcon()"
                                    class="w-3.5 h-3.5
                                        {{ $stat->getColor() === 'success' ? 'text-green-500' : '' }}
                                        {{ $stat->getColor() === 'danger' ? 'text-red-500' : '' }}
                                        {{ $stat->getColor() === 'warning' ? 'text-amber-500' : '' }}
                                        {{ $stat->getColor() === 'info' ? 'text-blue-500' : '' }}
                                        {{ $stat->getColor() === 'gray' ? 'text-gray-400' : '' }}" />
                            @endif
                            <span
                                class="
                                {{ $stat->getColor() === 'success' ? 'text-green-600' : '' }}
                                {{ $stat->getColor() === 'danger' ? 'text-red-600' : '' }}
                                {{ $stat->getColor() === 'warning' ? 'text-amber-600' : '' }}
                                {{ $stat->getColor() === 'info' ? 'text-blue-600' : '' }}
                                {{ $stat->getColor() === 'gray' ? 'text-gray-500' : '' }}">
                                {{ $stat->getDescription() }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
