<?php

namespace App\Filament\Resources\Parties\Customers\Pages;

use App\Filament\Resources\Parties\Customers\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
