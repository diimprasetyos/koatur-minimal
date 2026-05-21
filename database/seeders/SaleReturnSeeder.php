<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleReturnSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $sale     = fn(string $inv) => DB::table('sales')->where('invoice_number', $inv)->first();
        $saleItem = fn(int $sid, string $sku) => DB::table('sale_items')
            ->where('sale_id', $sid)
            ->where('product_id', DB::table('products')->where('sku', $sku)->value('id'))
            ->first();

        // ── Return 1 — Agus kembalikan 2 Indomie (kemasan penyok) ──
        $s  = $sale('INV-T1-20260310-001');
        $si = $saleItem($s->id, 'IMG-001');

        $sr1 = DB::table('sale_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'sale_id'          => $s->id,
            'reference_number' => 'SR-T1-20260311-001',
            'return_date'      => '2026-03-11',
            'total_refund'     => 7000,
            'status'           => 'approved',
            'refund_method'    => 'refund',
            'reason'           => 'Produk rusak / penyok saat diterima',
            'notes'            => 'Refund tunai kepada pelanggan',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('sale_return_items')->insert([[
            'sale_return_id' => $sr1,
            'product_id'     => DB::table('products')->where('sku', 'IMG-001')->value('id'),
            'sale_item_id'   => $si->id,
            'qty'            => 2,
            'price'          => 3500,
            'subtotal'       => 7000,
            'reason'         => 'Kemasan penyok',
        ]]);
        $this->returnStock('IMG-001', 2, 'sale_return', $sr1, $t->id, $u->id, $now);

        // ── Return 2 — Dewi kembalikan charger (tidak compatible) ──
        $s  = $sale('INV-T1-20260315-001');
        $si = $saleItem($s->id, 'CHG-065');

        $sr2 = DB::table('sale_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'sale_id'          => $s->id,
            'reference_number' => 'SR-T1-20260316-001',
            'return_date'      => '2026-03-16',
            'total_refund'     => 150000,
            'status'           => 'approved',
            'refund_method'    => 'store_credit',
            'reason'           => 'Charger tidak kompatibel dengan perangkat pelanggan',
            'notes'            => 'Kredit toko untuk pembelian berikutnya',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('sale_return_items')->insert([[
            'sale_return_id' => $sr2,
            'product_id'     => DB::table('products')->where('sku', 'CHG-065')->value('id'),
            'sale_item_id'   => $si->id,
            'qty'            => 1,
            'price'          => 150000,
            'subtotal'       => 150000,
            'reason'         => 'Tidak kompatibel',
        ]]);
        $this->returnStock('CHG-065', 1, 'sale_return', $sr2, $t->id, $u->id, $now);
    }

    private function returnStock(string $sku, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
    {
        $product     = DB::table('products')->where('sku', $sku)->first();
        $stockBefore = $product->stock;
        $stockAfter  = $stockBefore + $qty;

        DB::table('stock_movements')->insert([
            'tenant_id'      => $tenantId,
            'product_id'     => $product->id,
            'user_id'        => $userId,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'type'           => 'in',
            'qty'            => $qty,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'notes'          => 'Retur penjualan - stok dikembalikan',
            'created_at'     => $now,
        ]);

        DB::table('products')->where('id', $product->id)->update(['stock' => $stockAfter]);
    }
}
