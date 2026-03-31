<x-filament-panels::page>

    {{-- Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">🛒 Total Pembelian</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $this->getTotalPurchase() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">📋 Total Transaksi</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $this->getTotalTransactions() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">✅ Total Terbayar</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ $this->getTotalPaid() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">⚠️ Total Hutang</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">{{ $this->getTotalUnpaid() }}</span>
            </div>
        </x-filament::section>

    </div>

    {{ $this->table }}

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Livewire.on('print-report', () => {
                window.print();
            });
        });
    </script>

    <style>
        @media print {
            nav,
            aside,
            header,
            .fi-topbar,
            .fi-sidebar,
            .fi-header-actions,
            [data-testid="page-header"] {
                display: none !important;
            }

            .fi-main {
                padding: 0 !important;
                margin: 0 !important;
            }

            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>

</x-filament-panels::page>