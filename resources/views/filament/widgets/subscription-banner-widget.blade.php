<x-filament-widgets::widget>
    <x-filament::section :padding="false">
        <div style="padding: 0.75rem 1rem;">

            @if ($isExpired)
                {{-- Banner Expired - Merah --}}
                <div style="border-radius: 0.75rem; border: 1px solid #fecaca; background-color: #fef2f2; padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background-color: #fee2e2; display: flex; align-items: center; justify-content: center;">
                            <x-heroicon-o-x-circle style="width: 1.5rem; height: 1.5rem; color: #dc2626;" />
                        </div>
                        <div>
                            <p style="font-weight: 600; color: #991b1b; margin: 0;">Langganan Anda telah berakhir</p>
                            <p style="font-size: 0.875rem; color: #dc2626; margin: 0.125rem 0 0;">
                                Akses terbatas. Segera perpanjang untuk melanjutkan menggunakan semua fitur.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('billing') }}"
                        style="flex-shrink: 0; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background-color: #dc2626; color: #ffffff; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s;"
                        onmouseover="this.style.backgroundColor='#b91c1c'"
                        onmouseout="this.style.backgroundColor='#dc2626'">
                        <x-heroicon-o-arrow-up-circle style="width: 1rem; height: 1rem;" />
                        Perpanjang Sekarang
                    </a>
                </div>

            @elseif ($daysRemaining <= 3)
                {{-- Banner Hampir Habis - Oranye --}}
                <div style="border-radius: 0.75rem; border: 1px solid #fed7aa; background-color: #fff7ed; padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background-color: #ffedd5; display: flex; align-items: center; justify-content: center;">
                            <x-heroicon-o-fire style="width: 1.5rem; height: 1.5rem; color: #ea580c;" />
                        </div>
                        <div>
                            <p style="font-weight: 600; color: #9a3412; margin: 0;">
                                Trial berakhir dalam <span style="text-decoration: underline;">{{ $daysRemaining }} hari</span>!
                            </p>
                            <p style="font-size: 0.875rem; color: #ea580c; margin: 0.125rem 0 0;">
                                Jangan sampai kehilangan akses. Upgrade sekarang dan nikmati semua fitur tanpa batas.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('billing') }}"
                        style="flex-shrink: 0; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background-color: #f97316; color: #ffffff; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s;"
                        onmouseover="this.style.backgroundColor='#ea580c'"
                        onmouseout="this.style.backgroundColor='#f97316'">
                        <x-heroicon-o-arrow-up-circle style="width: 1rem; height: 1rem;" />
                        Upgrade Sekarang
                    </a>
                </div>

            @else
                {{-- Banner Trial Normal - Biru --}}
                <div style="border-radius: 0.75rem; border: 1px solid #bfdbfe; background-color: #eff6ff; padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                            <x-heroicon-o-clock style="width: 1.5rem; height: 1.5rem; color: #2563eb;" />
                        </div>
                        <div>
                            <p style="font-weight: 600; color: #1e40af; margin: 0;">
                                Anda sedang dalam masa trial &mdash;
                                <span style="font-weight: 700;">sisa {{ $daysRemaining }} hari</span>
                            </p>
                            <p style="font-size: 0.875rem; color: #2563eb; margin: 0.125rem 0 0;">
                                Upgrade ke paket berbayar untuk akses penuh tanpa batas waktu.
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                        {{-- Progress bar (hanya desktop) --}}
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                            <span style="font-size: 0.75rem; color: #3b82f6;">{{ $daysRemaining }}/3 hari</span>
                            <div style="width: 6rem; height: 0.5rem; background-color: #bfdbfe; border-radius: 9999px; overflow: hidden;">
                                <div style="height: 100%; background-color: #3b82f6; border-radius: 9999px; width: {{ min(100, ($daysRemaining / 3) * 100) }}%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                        <a href="{{ route('billing') }}"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background-color: #2563eb; color: #ffffff; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s;"
                            onmouseover="this.style.backgroundColor='#1d4ed8'"
                            onmouseout="this.style.backgroundColor='#2563eb'">
                            <x-heroicon-o-arrow-up-circle style="width: 1rem; height: 1rem;" />
                            Upgrade
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
