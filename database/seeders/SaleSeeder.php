<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();

        $user1 = DB::table('users')->where('email', 'admin@example.com')->first();
        $user2 = DB::table('users')->where('email', 'budi@example.com')->first();
        $user3 = DB::table('users')->where('email', 'siti@example.com')->first();

        // Customers
        $custAgus = DB::table('customers')->where('tenant_id', $tenant1->id)->where('name', 'Agus Prasetyo')->first();
        $custDewi = DB::table('customers')->where('tenant_id', $tenant1->id)->where('name', 'Dewi Rahayu')->first();
        $custToko = DB::table('customers')->where('tenant_id', $tenant1->id)->where('name', 'Toko Sinar Mas')->first();
        $custRina = DB::table('customers')->where('tenant_id', $tenant2->id)->where('name', 'Rina Marlina')->first();
        $custKantin = DB::table('customers')->where('tenant_id', $tenant2->id)->where('name', 'Kantin Sekolah SMK 1')->first();

        // Products
        $prodIndomie = DB::table('products')->where('sku', 'IMG-001')->first();
        $prodAqua = DB::table('products')->where('sku', 'AQU-001')->first();
        $prodCharger = DB::table('products')->where('sku', 'CHG-065')->first();
        $prodKaosW = DB::table('products')->where('sku', 'KPS-W-L')->first();
        $prodPulpen = DB::table('products')->where('sku', 'PEN-BLK')->first();
        $prodBuku = DB::table('products')->where('sku', 'BUK-058')->first();
        $prodBeras = DB::table('products')->where('sku', 'BRS-5KG')->first();
        $prodMinyak = DB::table('products')->where('sku', 'MNY-1LT')->first();
        $prodGula = DB::table('products')->where('sku', 'GUL-1KG')->first();
        $prodTeh = DB::table('products')->where('sku', 'TEH-350')->first();
        $prodChitato = DB::table('products')->where('sku', 'CHT-068')->first();

        $now = now();

        // =====================
        // SALES - TENANT 1
        // =====================

        // Sale 1: Agus beli indomie + aqua (cash, lunas)
        $sale1Id = DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user2->id,
            'customer_id' => $custAgus->id,
            'invoice_number' => 'INV-T1-20260310-001',
            'sale_date' => '2026-03-10',
            'payment_method' => 'cash',
            'subtotal' => 37500,
            'discount' => 0,
            'tax' => 0,
            'total' => 37500,
            'paid' => 40000,
            'change' => 2500,
            'status' => 'paid',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            ['sale_id' => $sale1Id, 'product_id' => $prodIndomie->id, 'product_name' => $prodIndomie->name, 'qty' => 5, 'price' => 3500, 'cost_price' => 2800, 'discount' => 0, 'subtotal' => 17500],
            ['sale_id' => $sale1Id, 'product_id' => $prodAqua->id, 'product_name' => $prodAqua->name, 'qty' => 5, 'price' => 4000, 'cost_price' => 3000, 'discount' => 0, 'subtotal' => 20000],
        ]);

        $this->recordStockMovement($tenant1->id, $user2->id, $prodIndomie->id, 'sale', $sale1Id, 'out', 5, $now);
        $this->recordStockMovement($tenant1->id, $user2->id, $prodAqua->id, 'sale', $sale1Id, 'out', 5, $now);

        // Sale 2: Dewi beli charger + kaos (QRIS, lunas)
        $sale2Id = DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user2->id,
            'customer_id' => $custDewi->id,
            'invoice_number' => 'INV-T1-20260315-001',
            'sale_date' => '2026-03-15',
            'payment_method' => 'qris',
            'subtotal' => 215000,
            'discount' => 15000,
            'tax' => 0,
            'total' => 200000,
            'paid' => 200000,
            'change' => 0,
            'status' => 'paid',
            'notes' => 'Diskon member 7%',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            ['sale_id' => $sale2Id, 'product_id' => $prodCharger->id, 'product_name' => $prodCharger->name, 'qty' => 1, 'price' => 150000, 'cost_price' => 95000, 'discount' => 10000, 'subtotal' => 140000],
            ['sale_id' => $sale2Id, 'product_id' => $prodKaosW->id, 'product_name' => $prodKaosW->name, 'qty' => 1, 'price' => 65000, 'cost_price' => 40000, 'discount' => 5000, 'subtotal' => 60000],
        ]);

        $this->recordStockMovement($tenant1->id, $user2->id, $prodCharger->id, 'sale', $sale2Id, 'out', 1, $now);
        $this->recordStockMovement($tenant1->id, $user2->id, $prodKaosW->id, 'sale', $sale2Id, 'out', 1, $now);

        // Sale 3: Toko Sinar Mas beli ATK grosir (transfer, partial/piutang)
        $sale3Id = DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user1->id,
            'customer_id' => $custToko->id,
            'invoice_number' => 'INV-T1-20260320-001',
            'sale_date' => '2026-03-20',
            'payment_method' => 'transfer',
            'subtotal' => 400000,
            'discount' => 0,
            'tax' => 0,
            'total' => 400000,
            'paid' => 250000,
            'change' => 0,
            'status' => 'partial',
            'notes' => 'Sisa tagihan Rp 150.000',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            ['sale_id' => $sale3Id, 'product_id' => $prodPulpen->id, 'product_name' => $prodPulpen->name, 'qty' => 10, 'price' => 25000, 'cost_price' => 15000, 'discount' => 0, 'subtotal' => 250000],
            ['sale_id' => $sale3Id, 'product_id' => $prodBuku->id, 'product_name' => $prodBuku->name, 'qty' => 20, 'price' => 7500, 'cost_price' => 5000, 'discount' => 0, 'subtotal' => 150000],
        ]);

        $this->recordStockMovement($tenant1->id, $user1->id, $prodPulpen->id, 'sale', $sale3Id, 'out', 10, $now);
        $this->recordStockMovement($tenant1->id, $user1->id, $prodBuku->id, 'sale', $sale3Id, 'out', 20, $now);

        // Sale 4: Walk-in tanpa customer (cash)
        $sale4Id = DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user2->id,
            'customer_id' => null,
            'invoice_number' => 'INV-T1-20260322-001',
            'sale_date' => '2026-03-22',
            'payment_method' => 'cash',
            'subtotal' => 17500,
            'discount' => 0,
            'tax' => 0,
            'total' => 17500,
            'paid' => 20000,
            'change' => 2500,
            'status' => 'paid',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            ['sale_id' => $sale4Id, 'product_id' => $prodIndomie->id, 'product_name' => $prodIndomie->name, 'qty' => 3, 'price' => 3500, 'cost_price' => 2800, 'discount' => 0, 'subtotal' => 10500],
            ['sale_id' => $sale4Id, 'product_id' => $prodAqua->id, 'product_name' => $prodAqua->name, 'qty' => 2, 'price' => 4000, 'cost_price' => 3000, 'discount' => 0, 'subtotal' => 8000],
        ]);

        $this->recordStockMovement($tenant1->id, $user2->id, $prodIndomie->id, 'sale', $sale4Id, 'out', 3, $now);
        $this->recordStockMovement($tenant1->id, $user2->id, $prodAqua->id, 'sale', $sale4Id, 'out', 2, $now);

        // =====================
        // SALES - TENANT 2
        // =====================

        // Sale 5: Rina beli sembako
        $sale5Id = DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant2->id,
            'user_id' => $user3->id,
            'customer_id' => $custRina->id,
            'invoice_number' => 'INV-T2-20260312-001',
            'sale_date' => '2026-03-12',
            'payment_method' => 'cash',
            'subtotal' => 109000,
            'discount' => 0,
            'tax' => 0,
            'total' => 109000,
            'paid' => 110000,
            'change' => 1000,
            'status' => 'paid',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            ['sale_id' => $sale5Id, 'product_id' => $prodBeras->id, 'product_name' => $prodBeras->name, 'qty' => 1, 'price' => 75000, 'cost_price' => 62000, 'discount' => 0, 'subtotal' => 75000],
            ['sale_id' => $sale5Id, 'product_id' => $prodMinyak->id, 'product_name' => $prodMinyak->name, 'qty' => 1, 'price' => 18000, 'cost_price' => 14500, 'discount' => 0, 'subtotal' => 18000],
            ['sale_id' => $sale5Id, 'product_id' => $prodGula->id, 'product_name' => $prodGula->name, 'qty' => 1, 'price' => 16000, 'cost_price' => 13000, 'discount' => 0, 'subtotal' => 16000],
        ]);

        $this->recordStockMovement($tenant2->id, $user3->id, $prodBeras->id, 'sale', $sale5Id, 'out', 1, $now);
        $this->recordStockMovement($tenant2->id, $user3->id, $prodMinyak->id, 'sale', $sale5Id, 'out', 1, $now);
        $this->recordStockMovement($tenant2->id, $user3->id, $prodGula->id, 'sale', $sale5Id, 'out', 1, $now);

        // Sale 6: Kantin beli snack + minuman grosir
        $sale6Id = DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant2->id,
            'user_id' => $user3->id,
            'customer_id' => $custKantin->id,
            'invoice_number' => 'INV-T2-20260317-001',
            'sale_date' => '2026-03-17',
            'payment_method' => 'transfer',
            'subtotal' => 330000,
            'discount' => 30000,
            'tax' => 0,
            'total' => 300000,
            'paid' => 300000,
            'change' => 0,
            'status' => 'paid',
            'notes' => 'Diskon grosir 10%',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            ['sale_id' => $sale6Id, 'product_id' => $prodTeh->id, 'product_name' => $prodTeh->name, 'qty' => 20, 'price' => 6000, 'cost_price' => 4500, 'discount' => 10, 'subtotal' => 114000],
            ['sale_id' => $sale6Id, 'product_id' => $prodChitato->id, 'product_name' => $prodChitato->name, 'qty' => 12, 'price' => 12000, 'cost_price' => 9000, 'discount' => 10, 'subtotal' => 129600],
        ]);

        $this->recordStockMovement($tenant2->id, $user3->id, $prodTeh->id, 'sale', $sale6Id, 'out', 20, $now);
        $this->recordStockMovement($tenant2->id, $user3->id, $prodChitato->id, 'sale', $sale6Id, 'out', 12, $now);
    }

    private function recordStockMovement(
        int $tenantId,
        int $userId,
        int $productId,
        string $refType,
        int $refId,
        string $type,
        int $qty,
        $now
    ): void {
        $product = DB::table('products')->find($productId);
        $stockBefore = $product->stock;
        $stockAfter = $type === 'out' ? $stockBefore - $qty : $stockBefore + $qty;

        DB::table('stock_movements')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => $userId,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'type' => $type,
            'qty' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'notes' => null,
            'created_at' => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}