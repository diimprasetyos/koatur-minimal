<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ── Subscription plans harus ada sebelum user dibuat ──
            SubscriptionPlanSeeder::class,

            // ── Master data ───────────────────────────────────────
            TenantSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,

            // ── Data operasional ──────────────────────────────────
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class,

            // ── Transaksi ─────────────────────────────────────────
            SaleSeeder::class,
            PurchaseSeeder::class,
            SaleReturnSeeder::class,
            PurchaseReturnSeeder::class,
            StockAdjustmentSeeder::class,
            QuotationSeeder::class,
        ]);
    }
}
