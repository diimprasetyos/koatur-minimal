<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $user1 = DB::table('users')->where('email', 'admin@example.com')->first();
        $user3 = DB::table('users')->where('email', 'siti@example.com')->first();
        $user4 = DB::table('users')->where('email', 'hendra@example.com')->first();

        // Suppliers
        $supIndofood = DB::table('suppliers')->where('code', 'SUP-001')->first();
        $supElektro = DB::table('suppliers')->where('code', 'SUP-002')->first();
        $supDistro = DB::table('suppliers')->where('code', 'SUP-003')->first();
        $supAtk = DB::table('suppliers')->where('code', 'SUP-004')->first();
        $supBogasari = DB::table('suppliers')->where('code', 'S-001')->first();
        $supSembako = DB::table('suppliers')->where('code', 'S-003')->first();
        $supKrakatau = DB::table('suppliers')->where('code', 'VND-001')->first();
        $supCat = DB::table('suppliers')->where('code', 'VND-002')->first();

        // Products
        $prodIndomie = DB::table('products')->where('sku', 'IMG-001')->first();
        $prodAqua = DB::table('products')->where('sku', 'AQU-001')->first();
        $prodCharger = DB::table('products')->where('sku', 'CHG-065')->first();
        $prodKaosW = DB::table('products')->where('sku', 'KPS-W-L')->first();
        $prodKaosB = DB::table('products')->where('sku', 'KPS-B-M')->first();
        $prodPulpen = DB::table('products')->where('sku', 'PEN-BLK')->first();
        $prodBeras = DB::table('products')->where('sku', 'BRS-5KG')->first();
        $prodMinyak = DB::table('products')->where('sku', 'MNY-1LT')->first();
        $prodGula = DB::table('products')->where('sku', 'GUL-1KG')->first();
        $prodBesi = DB::table('products')->where('sku', 'BSH-446')->first();
        $prodCat = DB::table('products')->where('sku', 'CAT-25K')->first();

        $now = now();

        // =====================
        // PURCHASES - TENANT 1
        // =====================

        // PO 1: Beli Indomie + Aqua dari Indofood
        $po1Id = DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user1->id,
            'supplier_id' => $supIndofood->id,
            'reference_number' => 'PO-T1-20260305-001',
            'supplier_invoice' => 'INV-IDF-20260305',
            'purchase_date' => '2026-03-05',
            'due_date' => null,
            'subtotal' => 1120000,
            'discount' => 0,
            'tax' => 0,
            'total' => 1120000,
            'paid' => 1120000,
            'due' => 0,
            'status' => 'received',
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_items')->insert([
            ['purchase_id' => $po1Id, 'product_id' => $prodIndomie->id, 'qty' => 200, 'qty_received' => 200, 'cost_price' => 2800, 'subtotal' => 560000],
            ['purchase_id' => $po1Id, 'product_id' => $prodAqua->id, 'qty' => 150, 'qty_received' => 150, 'cost_price' => 3000, 'subtotal' => 450000],
        ]);

        $this->addStock($prodIndomie->id, 200, 'purchase', $po1Id, $tenant1->id, $user1->id, $now);
        $this->addStock($prodAqua->id, 150, 'purchase', $po1Id, $tenant1->id, $user1->id, $now);

        // PO 2: Beli charger dari Elektro Jaya (hutang sebagian)
        $po2Id = DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user1->id,
            'supplier_id' => $supElektro->id,
            'reference_number' => 'PO-T1-20260308-001',
            'supplier_invoice' => null,
            'purchase_date' => '2026-03-08',
            'due_date' => '2026-04-08',
            'subtotal' => 950000,
            'discount' => 0,
            'tax' => 0,
            'total' => 950000,
            'paid' => 600000,
            'due' => 350000,
            'status' => 'received',
            'payment_status' => 'partial',
            'payment_method' => 'transfer',
            'notes' => 'Sisa hutang dibayar bulan depan',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_items')->insert([
            ['purchase_id' => $po2Id, 'product_id' => $prodCharger->id, 'qty' => 10, 'qty_received' => 10, 'cost_price' => 95000, 'subtotal' => 950000],
        ]);

        $this->addStock($prodCharger->id, 10, 'purchase', $po2Id, $tenant1->id, $user1->id, $now);

        // PO 3: Beli pakaian dari Distro
        $po3Id = DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user1->id,
            'supplier_id' => $supDistro->id,
            'reference_number' => 'PO-T1-20260312-001',
            'supplier_invoice' => 'DIST-INV-3201',
            'purchase_date' => '2026-03-12',
            'due_date' => null,
            'subtotal' => 3200000,
            'discount' => 200000,
            'tax' => 0,
            'total' => 3000000,
            'paid' => 3000000,
            'due' => 0,
            'status' => 'received',
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_items')->insert([
            ['purchase_id' => $po3Id, 'product_id' => $prodKaosW->id, 'qty' => 40, 'qty_received' => 40, 'cost_price' => 40000, 'subtotal' => 1600000],
            ['purchase_id' => $po3Id, 'product_id' => $prodKaosB->id, 'qty' => 40, 'qty_received' => 40, 'cost_price' => 40000, 'subtotal' => 1600000],
        ]);

        $this->addStock($prodKaosW->id, 40, 'purchase', $po3Id, $tenant1->id, $user1->id, $now);
        $this->addStock($prodKaosB->id, 40, 'purchase', $po3Id, $tenant1->id, $user1->id, $now);

        // =====================
        // PURCHASES - TENANT 2
        // =====================

        // PO 4: Beli sembako dari UD Sembako Makmur
        $po4Id = DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant2->id,
            'user_id' => $user3->id,
            'supplier_id' => $supSembako->id,
            'reference_number' => 'PO-T2-20260307-001',
            'supplier_invoice' => null,
            'purchase_date' => '2026-03-07',
            'due_date' => '2026-03-14',
            'subtotal' => 2770000,
            'discount' => 0,
            'tax' => 0,
            'total' => 2770000,
            'paid' => 2645000,
            'due' => 125000,
            'status' => 'received',
            'payment_status' => 'partial',
            'payment_method' => 'cash',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_items')->insert([
            ['purchase_id' => $po4Id, 'product_id' => $prodBeras->id, 'qty' => 20, 'qty_received' => 20, 'cost_price' => 62000, 'subtotal' => 1240000],
            ['purchase_id' => $po4Id, 'product_id' => $prodMinyak->id, 'qty' => 30, 'qty_received' => 30, 'cost_price' => 14500, 'subtotal' => 435000],
            ['purchase_id' => $po4Id, 'product_id' => $prodGula->id, 'qty' => 50, 'qty_received' => 50, 'cost_price' => 13000, 'subtotal' => 650000],
        ]);

        $this->addStock($prodBeras->id, 20, 'purchase', $po4Id, $tenant2->id, $user3->id, $now);
        $this->addStock($prodMinyak->id, 30, 'purchase', $po4Id, $tenant2->id, $user3->id, $now);
        $this->addStock($prodGula->id, 50, 'purchase', $po4Id, $tenant2->id, $user3->id, $now);

        // =====================
        // PURCHASES - TENANT 3
        // =====================

        // PO 5: Beli besi dari Krakatau Steel
        $po5Id = DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant3->id,
            'user_id' => $user4->id,
            'supplier_id' => $supKrakatau->id,
            'reference_number' => 'PO-T3-20260301-001',
            'supplier_invoice' => 'KS-INV-2026031',
            'purchase_date' => '2026-03-01',
            'due_date' => '2026-04-01',
            'subtotal' => 7250000,
            'discount' => 250000,
            'tax' => 0,
            'total' => 7000000,
            'paid' => 0,
            'due' => 7000000,
            'status' => 'received',
            'payment_status' => 'unpaid',
            'payment_method' => 'transfer',
            'notes' => 'Pembayaran net 30 hari',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_items')->insert([
            ['purchase_id' => $po5Id, 'product_id' => $prodBesi->id, 'qty' => 50, 'qty_received' => 50, 'cost_price' => 145000, 'subtotal' => 7250000],
        ]);

        $this->addStock($prodBesi->id, 50, 'purchase', $po5Id, $tenant3->id, $user4->id, $now);

        // PO 6: Beli cat dari Toko Cat
        $po6Id = DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant3->id,
            'user_id' => $user4->id,
            'supplier_id' => $supCat->id,
            'reference_number' => 'PO-T3-20260315-001',
            'supplier_invoice' => null,
            'purchase_date' => '2026-03-15',
            'due_date' => null,
            'subtotal' => 1700000,
            'discount' => 0,
            'tax' => 0,
            'total' => 1700000,
            'paid' => 1700000,
            'due' => 0,
            'status' => 'received',
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_items')->insert([
            ['purchase_id' => $po6Id, 'product_id' => $prodCat->id, 'qty' => 5, 'qty_received' => 5, 'cost_price' => 340000, 'subtotal' => 1700000],
        ]);

        $this->addStock($prodCat->id, 5, 'purchase', $po6Id, $tenant3->id, $user4->id, $now);
    }

    private function addStock(int $productId, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
    {
        $product = DB::table('products')->find($productId);
        $stockBefore = $product->stock;
        $stockAfter = $stockBefore + $qty;

        DB::table('stock_movements')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => $userId,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'type' => 'in',
            'qty' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'notes' => null,
            'created_at' => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}