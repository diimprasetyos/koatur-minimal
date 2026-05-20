<x-filament::page>

    {{ $this->form }}

    <div class="mt-6">
        <x-filament::button wire:click="generatePdf" icon="heroicon-o-printer">
            Print Barcode PDF
        </x-filament::button>
    </div>

</x-filament::page>
