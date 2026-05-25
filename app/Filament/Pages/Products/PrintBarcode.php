<?php

namespace App\Filament\Pages\Products;

use App\Models\Product\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                                        ->where('is_active', true)
                                        ->pluck('name', 'id')
                                )
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (!$state) {
                                        return;
                                    }

                                    $product = Product::find($state);

                                    if (!$product) {
                                        return;
                                    }

                                    $set('product_name', $product->name);
                                    $set('product_code', $product->sku);
                                }),

                            Hidden::make('product_name'),

                            Hidden::make('product_code'),

                            TextInput::make('product_code')
                                ->label('Kode Produk')
                                ->disabled()
                                ->dehydrated(false)
                                ->reactive()
                                ->formatStateUsing(function ($state, callable $get) {
                                    return $get('product_code');
                                }),
                                
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

        $products = [];

        foreach ($items as $item) {

            $product = Product::find($item['product_id']);

            if (!$product) {
                continue;
            }

            $products[] = [
                'product' => $product,
                'barcode_qty' => max(1, (int) $item['barcode_qty']),
            ];
        }

        $pdf = Pdf::loadView(
            'filament.pages.products.pdf.barcode',
            [
                'products' => $products,
            ]
        )->setPaper('a4');

        $filename = 'barcode-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }
}