<x-filament-panels::page>

    {{-- Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">↩️ Total Retur</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $this->getTotalTransactions() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">💸 Total Nilai Retur</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">{{ $this->getTotalReturn() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">✅ Approved</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ $this->getTotalApproved() }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">⏳ Pending</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #d97706;">{{ $this->getTotalPending() }}</span>
            </div>
        </x-filament::section>

    </div>

    {{-- Table --}}
    {{ $this->table }}

</x-filament-panels::page>