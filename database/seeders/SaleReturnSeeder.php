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

        // ── Return 1 — Rendra kembalikan Xiaomi (layar bergaris) ──
        $s  = $sale('INV-T1-20260310-001');
        $si = $saleItem($s->id, 'XMI-RN13');

        $sr1 = DB::table('sale_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'sale_id'          => $s->id,
            'reference_number' => 'SR-T1-20260312-001',
            'return_date'      => '2026-03-12',
            'total_refund'     => 2499000,
            'status'           => 'approved',
            'refund_method'    => 'exchange',
            'reason'           => 'Layar bergaris setelah 2 hari pemakaian — cacat produksi',
            'notes'            => 'Tukar unit baru stok yang sama',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('sale_return_items')->insert([[
            'sale_return_id' => $sr1,
            'product_id'     => DB::table('products')->where('sku', 'XMI-RN13')->value('id'),
            'sale_item_id'   => $si->id,
            'qty'            => 1,
            'price'          => 2499000,
            'subtotal'       => 2499000,
            'reason'         => 'Cacat produksi — layar bergaris',
        ]]);
        $this->returnStock('XMI-RN13', 1, 'sale_return', $sr1, $t->id, $u->id, $now);

        // ── Return 2 — Sari kembalikan charger GaN (tidak sesuai pesanan) ──
        $s  = $sale('INV-T1-20260315-001');
        $si = $saleItem($s->id, 'CHG-G65');

        $sr2 = DB::table('sale_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'sale_id'          => $s->id,
            'reference_number' => 'SR-T1-20260317-001',
            'return_date'      => '2026-03-17',
            'total_refund'     => 189000,
            'status'           => 'approved',
            'refund_method'    => 'store_credit',
            'reason'           => 'Charger tidak cocok dengan port laptop — salah pilih',
            'notes'            => 'Kredit toko untuk pembelian berikutnya',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('sale_return_items')->insert([[
            'sale_return_id' => $sr2,
            'product_id'     => DB::table('products')->where('sku', 'CHG-G65')->value('id'),
            'sale_item_id'   => $si->id,
            'qty'            => 1,
            'price'          => 189000,
            'subtotal'       => 189000,
            'reason'         => 'Tidak kompatibel — salah beli',
        ]]);
        $this->returnStock('CHG-G65', 1, 'sale_return', $sr2, $t->id, $u->id, $now);
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
            'notes'          => 'Retur penjualan — stok dikembalikan',
            'created_at'     => $now,
        ]);

        DB::table('products')->where('id', $product->id)->update(['stock' => $stockAfter]);
    }
}
