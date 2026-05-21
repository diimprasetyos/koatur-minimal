<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseReturnSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $po     = fn(string $ref) => DB::table('purchases')->where('reference_number', $ref)->first();
        $poItem = fn(int $pid, string $sku) => DB::table('purchase_items')
            ->where('purchase_id', $pid)
            ->where('product_id', DB::table('products')->where('sku', $sku)->value('id'))
            ->first();
        $sup    = fn(string $code) => DB::table('suppliers')
            ->where('tenant_id', $t->id)->where('code', $code)->first();

        // ── Return 1 — 1 charger rusak ke CV Elektro Jaya ──
        $p  = $po('PO-T1-20260308-001');
        $pi = $poItem($p->id, 'CHG-065');

        $pr1 = DB::table('purchase_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'purchase_id'      => $p->id,
            'supplier_id'      => $sup('SUP-002')->id,
            'reference_number' => 'PR-T1-20260309-001',
            'return_date'      => '2026-03-09',
            'total_return'     => 95000,
            'status'           => 'approved',
            'return_method'    => 'debit_note',
            'reason'           => 'Charger diterima dalam kondisi rusak / tidak menyala',
            'notes'            => 'Debit note untuk pengurang tagihan',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('purchase_return_items')->insert([[
            'purchase_return_id' => $pr1,
            'product_id'         => DB::table('products')->where('sku', 'CHG-065')->value('id'),
            'purchase_item_id'   => $pi->id,
            'qty'                => 1,
            'cost_price'         => 95000,
            'subtotal'           => 95000,
            'reason'             => 'Unit rusak, tidak berfungsi',
        ]]);
        $this->removeStock('CHG-065', 1, 'purchase_return', $pr1, $t->id, $u->id, $now);

        // ── Return 2 — 5 kaos cacat ke Distro Pakaian ──
        $p  = $po('PO-T1-20260312-001');
        $pi = $poItem($p->id, 'KPS-W-L');

        $pr2 = DB::table('purchase_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'purchase_id'      => $p->id,
            'supplier_id'      => $sup('SUP-003')->id,
            'reference_number' => 'PR-T1-20260314-001',
            'return_date'      => '2026-03-14',
            'total_return'     => 200000,
            'status'           => 'approved',
            'return_method'    => 'replacement',
            'reason'           => 'Sablon kaos buram dan jahitan tidak rapi',
            'notes'            => 'Supplier setuju kirim pengganti',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('purchase_return_items')->insert([[
            'purchase_return_id' => $pr2,
            'product_id'         => DB::table('products')->where('sku', 'KPS-W-L')->value('id'),
            'purchase_item_id'   => $pi->id,
            'qty'                => 5,
            'cost_price'         => 40000,
            'subtotal'           => 200000,
            'reason'             => 'Kualitas sablon & jahitan tidak sesuai',
        ]]);
        $this->removeStock('KPS-W-L', 5, 'purchase_return', $pr2, $t->id, $u->id, $now);
    }

    private function removeStock(string $sku, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
    {
        $product     = DB::table('products')->where('sku', $sku)->first();
        $stockBefore = $product->stock;
        $stockAfter  = max(0, $stockBefore - $qty);

        DB::table('stock_movements')->insert([
            'tenant_id'      => $tenantId,
            'product_id'     => $product->id,
            'user_id'        => $userId,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'type'           => 'out',
            'qty'            => $qty,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'notes'          => 'Retur pembelian - stok dikurangi',
            'created_at'     => $now,
        ]);

        DB::table('products')->where('id', $product->id)->update(['stock' => $stockAfter]);
    }
}
