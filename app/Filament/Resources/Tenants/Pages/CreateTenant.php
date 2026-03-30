<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Tenant;
use App\Utils\PlanLimit;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Block akses ke halaman create jika limit tercapai.
     * Antisipasi bypass lewat URL langsung.
     */
    public function mount(): void
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !PlanLimit::canCreateTenant($user)) {
            $plan = $user->subscription_plan ?? 'basic';

            Notification::make()
                ->title('Batas kuota tercapai')
                ->body(
                    'Paket ' . PlanLimit::label($plan) .
                    ' hanya dapat membuat ' .
                    PlanLimit::maxTenants($plan) .
                    ' toko.'
                )
                ->warning()
                ->persistent()
                ->send();

            $this->redirect(TenantResource::getUrl('index'));
            return;
        }

        parent::mount();
    }

    protected function afterCreate(): void
    {
        $user = auth()->user();

        // Super admin tidak perlu di-attach (dia bisa lihat semua).
        if ($user->is_super_admin ?? false) {
            return;
        }

        /** @var Tenant $tenant */
        $tenant = $this->record;

        // Attach user ke tenant yang baru dibuat (hindari duplikat dengan syncWithoutDetaching).
        $tenant->users()->syncWithoutDetaching([$user->id]);
    }
}
