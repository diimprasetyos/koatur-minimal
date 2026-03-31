<x-filament-panels::page>
    {{-- Page content --}}
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