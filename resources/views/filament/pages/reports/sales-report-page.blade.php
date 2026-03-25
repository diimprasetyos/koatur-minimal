<x-filament-panels::page>

    {{-- Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">💰 Total Omzet</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $this->getTotalRevenue() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">🛒 Total Transaksi</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $this->getTotalTransactions() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">📊 Rata-rata Transaksi</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $this->getAverageTransaction() }}</span>
            </div>
        </x-filament::section>

    </div>

    {{-- Table --}}
    {{ $this->table }}

</x-filament-panels::page>