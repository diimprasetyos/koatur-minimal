<?php

namespace App\Filament\Superadmin\Resources\Users\Pages;

use App\Filament\Superadmin\Resources\Users\UserResource;
use App\Filament\Superadmin\Resources\Users\Utils\HandleSubscription;
use App\Models\Tenant;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use HandleSubscription;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillSubscriptionData($data, $this->getRecord());
    }

    protected function afterSave(): void
    {
        $this->saveSubscription($this->data, $this->getRecord());
    }
}
