<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class,
            SaleSeeder::class,
            PurchaseSeeder::class,
            SaleReturnSeeder::class,
            PurchaseReturnSeeder::class,
            StockAdjustmentSeeder::class,
            QuotationSeeder::class,
        ]);
    }
}