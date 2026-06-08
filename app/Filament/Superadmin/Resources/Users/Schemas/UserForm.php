<?php

namespace App\Filament\Superadmin\Resources\Users\Schemas;

use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionPlan;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // Data User
            Section::make('Data User')->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(100),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),

                Select::make('tenants')
                    ->label('Tenant')
                    ->relationship('tenants', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->nullable()
                    ->helperText('Kosongkan untuk Super Admin'),

                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),

            // Subscription
            Section::make('Subscription')
                ->description('Edit subscription user secara manual. Perubahan langsung disimpan ke tabel subscriptions.')
                ->schema([

                    // Info subscription aktif saat ini (hanya di halaman edit)
                    Placeholder::make('subscription_info')
                        ->label('Status Saat Ini')
                        ->content(function ($record): HtmlString {
                            if (!$record) {
                                return new HtmlString('<span class="text-gray-400 text-sm">—</span>');
                            }

                            $sub = $record->subscriptions()
                                ->with('plan')
                                ->latest()
                                ->first();

                            if (!$sub) {
                                return new HtmlString('<span class="text-gray-400 text-sm">Belum ada subscription</span>');
                            }

                            $statusColor = match ($sub->status) {
                                'active'    => 'green',
                                'trial'     => 'blue',
                                'expired'   => 'red',
                                'cancelled' => 'gray',
                                default     => 'gray',
                            };

                            $statusLabel = match ($sub->status) {
                                'active'    => 'Aktif',
                                'trial'     => 'Trial',
                                'expired'   => 'Expired',
                                'cancelled' => 'Dibatalkan',
                                default     => $sub->status,
                            };

                            $expiry = $sub->expires_at
                                ? $sub->expires_at->format('d M Y H:i')
                                : 'Tidak ada batas';

                            $days = $sub->daysRemaining();
                            $daysText = $days === null
                                ? 'Lifetime'
                                : ($days > 0 ? "{$days} hari lagi" : 'Sudah expired');

                            return new HtmlString("
                                <div class='text-sm space-y-1'>
                                    <div><span class='font-medium'>Plan:</span> {$sub->plan?->name}</div>
                                    <div>
                                        <span class='font-medium'>Status:</span>
                                        <span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{$statusColor}-100 text-{$statusColor}-800'>
                                            {$statusLabel}
                                        </span>
                                    </div>
                                    <div><span class='font-medium'>Expired:</span> {$expiry}</div>
                                    <div><span class='font-medium'>Sisa:</span> {$daysText}</div>
                                </div>
                            ");
                        })
                        ->hidden(fn($operation) => $operation === 'create')
                        ->columnSpanFull(),

                    // Plan
                    Select::make('subscription_plan_id')
                        ->label('Plan')
                        ->options(fn() => SubscriptionPlan::active()->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('Pilih plan untuk membuat / mengganti subscription aktif')
                        ->live(),

                    // Status
                    Select::make('subscription_status')
                        ->label('Status')
                        ->options([
                            Subscription::STATUS_TRIAL     => 'Trial',
                            Subscription::STATUS_ACTIVE    => 'Aktif',
                            Subscription::STATUS_EXPIRED   => 'Expired',
                            Subscription::STATUS_CANCELLED => 'Dibatalkan',
                        ])
                        ->nullable()
                        ->hidden(fn(\Filament\Schemas\Components\Utilities\Get $get) => !$get('subscription_plan_id')),

                    // Tanggal mulai
                    DateTimePicker::make('subscription_started_at')
                        ->label('Mulai')
                        ->displayFormat('d/m/Y H:i')
                        ->nullable()
                        ->hidden(fn(\Filament\Schemas\Components\Utilities\Get $get) => !$get('subscription_plan_id')),

                    // Tanggal expired
                    DateTimePicker::make('subscription_expires_at')
                        ->label('Expired')
                        ->displayFormat('d/m/Y H:i')
                        ->nullable()
                        ->helperText('Kosongkan untuk lifetime / tidak ada batas')
                        ->hidden(fn(\Filament\Schemas\Components\Utilities\Get $get) => !$get('subscription_plan_id')),

                ])
                ->columns(2)
                ->hidden(fn($operation) => $operation === 'create'), // Subscription diatur setelah user dibuat
        ]);
    }
}
