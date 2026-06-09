<?php

namespace App\Filament\Pages\Products;

use App\Models\Product\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class PrintBarcode extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.products.print-barcode';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::QrCode;
    protected static ?string $navigationLabel = 'Print Barcode';
    protected static string|UnitEnum|null $navigationGroup = 'Produk';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Print Barcode';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        $tenantId = filament()->getTenant()?->id;

        return [
            Section::make('Item Produk')
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->schema([

                            Select::make('product_id')
                                ->label('Produk')
                                ->searchable()
                                ->required()
                                ->live()
                                ->options(
                                    Product::query()
                                        ->where('tenant_id', $tenantId)
                                        ->where('is_active', true)
                                        ->pluck('name', 'id')
                                )
                                ->afterStateUpdated(function ($state, callable $set) use ($tenantId) {
                                    if (!$state) {
                                        $set('product_sku_display', null);
                                        return;
                                    }

                                    $product = Product::where('id', $state)
                                        ->where('tenant_id', $tenantId)
                                        ->where('is_active', true)
                                        ->select(['id', 'name', 'sku'])
                                        ->first();

                                    $set('product_sku_display', $product?->sku);
                                }),

                            TextInput::make('product_sku_display')
                                ->label('Kode Produk')
                                ->disabled()
                                ->dehydrated(false)
                                ->live(),

                            TextInput::make('barcode_qty')
                                ->label('Qty Barcode')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1),

                        ])
                        ->columns(2)
                        ->live()
                        ->addActionLabel('+ Tambah Produk')
                        ->minItems(1)
                        ->defaultItems(1),
                ]),
        ];
    }

    public function generatePdf()
    {
        $items = $this->data['items'] ?? [];

        if (empty($items)) {
            return;
        }

        $tenantId = filament()->getTenant()?->id;

        $productIds = collect($items)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($productIds)) {
            return;
        }

        $productMap = Product::whereIn('id', $productIds)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->select(['id', 'name', 'sku'])
            ->get()
            ->keyBy('id');

        $products = [];

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;

            if (!$productId || !isset($productMap[$productId])) {
                continue;
            }

            $products[] = [
                'product'     => $productMap[$productId],
                'barcode_qty' => max(1, (int) ($item['barcode_qty'] ?? 1)),
            ];
        }

        if (empty($products)) {
            return;
        }

        $pdf = Pdf::loadView(
            'filament.pages.products.pdf.barcode',
            ['products' => $products]
        )->setPaper('a4');

        $filename = 'barcode-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }
}
