<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleReturnSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();

        $user2 = DB::table('users')->where('email', 'budi@example.com')->first();
        $user3 = DB::table('users')->where('email', 'siti@example.com')->first();

        // Sales
        $sale1 = DB::table('sales')->where('invoice_number', 'INV-T1-20260310-001')->first();
        $sale5 = DB::table('sales')->where('invoice_number', 'INV-T2-20260312-001')->first();

        // Products
        $prodIndomie = DB::table('products')->where('sku', 'IMG-001')->first();
        $prodGula = DB::table('products')->where('sku', 'GUL-1KG')->first();

        // Sale items
        $saleItem1Indomie = DB::table('sale_items')
            ->where('sale_id', $sale1->id)
            ->where('product_id', $prodIndomie->id)
            ->first();

        $saleItem5Gula = DB::table('sale_items')
            ->where('sale_id', $sale5->id)
            ->where('product_id', $prodGula->id)
            ->first();

        $now = now();

        // =====================
        // SALE RETURN 1 - Tenant 1
        // Agus kembalikan 2 Indomie (rusak)
        // =====================
        $sr1Id = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant1->id,
            'user_id' => $user2->id,
            'sale_id' => $sale1->id,
            'reference_number' => 'SR-T1-20260311-001',
            'return_date' => '2026-03-11',
            'total_refund' => 7000,
            'status' => 'approved',
            'refund_method' => 'refund',
            'reason' => 'Produk rusak / penyok saat diterima',
            'notes' => 'Refund tunai kepada pelanggan',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_return_items')->insert([
            [
                'sale_return_id' => $sr1Id,
                'product_id' => $prodIndomie->id,
                'sale_item_id' => $saleItem1Indomie->id,
                'qty' => 2,
                'price' => 3500,
                'subtotal' => 7000,
                'reason' => 'Kemasan penyok',
            ],
        ]);

        // Kembalikan stok
        $this->returnStock($prodIndomie->id, 2, 'sale_return', $sr1Id, $tenant1->id, $user2->id, $now);

        // =====================
        // SALE RETURN 2 - Tenant 2
        // Rina kembalikan gula (salah beli)
        // =====================
        $sr2Id = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant2->id,
            'user_id' => $user3->id,
            'sale_id' => $sale5->id,
            'reference_number' => 'SR-T2-20260313-001',
            'return_date' => '2026-03-13',
            'total_refund' => 16000,
            'status' => 'approved',
            'refund_method' => 'store_credit',
            'reason' => 'Pelanggan salah beli, ingin tukar produk lain',
            'notes' => 'Kredit toko disimpan untuk pembelian berikutnya',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_return_items')->insert([
            [
                'sale_return_id' => $sr2Id,
                'product_id' => $prodGula->id,
                'sale_item_id' => $saleItem5Gula->id,
                'qty' => 1,
                'price' => 16000,
                'subtotal' => 16000,
                'reason' => 'Salah beli',
            ],
        ]);

        $this->returnStock($prodGula->id, 1, 'sale_return', $sr2Id, $tenant2->id, $user3->id, $now);
    }

    private function returnStock(int $productId, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
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
            'notes' => 'Retur penjualan - stok dikembalikan',
            'created_at' => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}