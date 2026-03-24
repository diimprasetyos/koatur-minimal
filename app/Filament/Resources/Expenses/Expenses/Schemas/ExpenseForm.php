<?php

namespace App\Filament\Resources\Expenses\Expenses\Schemas;

use App\Models\Expenses\ExpenseCategory;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(fn() => Filament::getTenant()?->id)
                    ->required(),
                Hidden::make('user_id')
                    ->default(Auth::id()),
                Select::make('expense_category_id')
                    ->label('Kategori')
                    ->options(function () {
                        return ExpenseCategory::where('tenant_id', auth()->user()->tenant_id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable(),
                Hidden::make('reference_number'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('expense_date')
                    ->required(),
                Select::make('payment_method')
                    ->options([
                        'cash'     => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet'  => '📱 E-Wallet',
                    ]),
                Textarea::make('notes')
                    ->columnSpanFull(),
                FileUpload::make('attachment')
                    ->image(),
            ]);
    }
}
