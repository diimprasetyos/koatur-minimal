<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseReturnSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $user1 = DB::table('users')->where('email', 'admin@example.com')->first();
        $user4 = DB::table('users')->where('email', 'hendra@example.com')->first();

        // Purchases
        $po2 = DB::table('purchases')->where('reference_number', 'PO-T1-20260308-001')->first();
        $po5 = DB::table('purchases')->where('reference_number', 'PO-T3-20260301-001')->first();

        // Suppliers
        $supElektro = DB::table('suppliers')->where('code', 'SUP-002')->first();
        $supKrakatau = DB::table('suppliers')->where('code', 'VND-001')->first();

        // Products
        $prodCharger = DB::table('products')->where('sku', 'CHG-065')->first();
        $prodBesi = DB::table('products')->where('sku', 'BSH-446')->first();

        // Purchase items
        $poItem2Charger = DB::table('purchase_items')
            ->where('purchase_id', $po2->id)
            ->where('product_id', $prodCharger->id)
            ->first();

        $poItem5Besi = DB::table('purchase_items')
            ->where('purchase_id', $po5->id)
            ->where('product_id', $prodBesi->id)
            ->first();

        $now = now();

        // =====================
        // PURCHASE RETURN 1 - Tenant 1
        // Kembalikan 1 charger rusak ke Elektro Jaya
        // =====================
        $pr1Id = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user1->id,
            'purchase_id' => $po2->id,
            'supplier_id' => $supElektro->id,
            'reference_number' => 'PR-T1-20260309-001',
            'return_date' => '2026-03-09',
            'total_return' => 95000,
            'status' => 'approved',
            'return_method' => 'debit_note',
            'reason' => 'Charger diterima dalam kondisi rusak / tidak menyala',
            'notes' => 'Debit note untuk pengurang tagihan',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_return_items')->insert([
            [
                'purchase_return_id' => $pr1Id,
                'product_id' => $prodCharger->id,
                'purchase_item_id' => $poItem2Charger->id,
                'qty' => 1,
                'cost_price' => 95000,
                'subtotal' => 95000,
                'reason' => 'Unit rusak, tidak berfungsi',
            ],
        ]);

        // Kurangi stok karena dikembalikan ke supplier
        $this->removeStock($prodCharger->id, 1, 'purchase_return', $pr1Id, $tenant1->id, $user1->id, $now);

        // =====================
        // PURCHASE RETURN 2 - Tenant 3
        // Kembalikan 5 besi hollow cacat ke Krakatau Steel
        // =====================
        $pr2Id = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant3->id,
            'user_id' => $user4->id,
            'purchase_id' => $po5->id,
            'supplier_id' => $supKrakatau->id,
            'reference_number' => 'PR-T3-20260304-001',
            'return_date' => '2026-03-04',
            'total_return' => 725000,
            'status' => 'approved',
            'return_method' => 'replacement',
            'reason' => 'Dimensi tidak sesuai spesifikasi PO, besi bengkok',
            'notes' => 'Supplier setuju kirim pengganti minggu depan',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('purchase_return_items')->insert([
            [
                'purchase_return_id' => $pr2Id,
                'product_id' => $prodBesi->id,
                'purchase_item_id' => $poItem5Besi->id,
                'qty' => 5,
                'cost_price' => 145000,
                'subtotal' => 725000,
                'reason' => 'Dimensi tidak sesuai & material bengkok',
            ],
        ]);

        $this->removeStock($prodBesi->id, 5, 'purchase_return', $pr2Id, $tenant3->id, $user4->id, $now);
    }

    private function removeStock(int $productId, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
    {
        $product = DB::table('products')->find($productId);
        $stockBefore = $product->stock;
        $stockAfter = max(0, $stockBefore - $qty);

        DB::table('stock_movements')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => $userId,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'type' => 'out',
            'qty' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'notes' => 'Retur pembelian - stok dikurangi',
            'created_at' => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}