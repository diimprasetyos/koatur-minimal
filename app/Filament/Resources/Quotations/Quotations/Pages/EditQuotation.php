<?php

namespace App\Filament\Resources\Quotations\Quotations\Pages;

use App\Filament\Resources\Quotations\Quotations\QuotationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn() => !$this->record->isDraft()),
        ];
    }

    // Redirect ke list setelah save
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $this->record->recalculate();
    }

}
