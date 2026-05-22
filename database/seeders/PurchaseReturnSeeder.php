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

        // ── Return 1 — 1 laptop ASUS rusak ke Distributor ASUS & Lenovo ──
        $p  = $po('PO-T1-20260308-001');
        $pi = $poItem($p->id, 'ASS-VB14');

        $pr1 = DB::table('purchase_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'purchase_id'      => $p->id,
            'supplier_id'      => $sup('SUP-002')->id,
            'reference_number' => 'PR-T1-20260309-001',
            'return_date'      => '2026-03-09',
            'total_return'     => 8500000,
            'status'           => 'approved',
            'return_method'    => 'debit_note',
            'reason'           => 'Laptop diterima dalam kondisi mati total, tidak bisa dinyalakan',
            'notes'            => 'Debit note untuk pengurang tagihan',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('purchase_return_items')->insert([[
            'purchase_return_id' => $pr1,
            'product_id'         => DB::table('products')->where('sku', 'ASS-VB14')->value('id'),
            'purchase_item_id'   => $pi->id,
            'qty'                => 1,
            'cost_price'         => 8500000,
            'subtotal'           => 8500000,
            'reason'             => 'Unit DOA (Dead on Arrival), tidak berfungsi sama sekali',
        ]]);
        $this->removeStock('ASS-VB14', 1, 'purchase_return', $pr1, $t->id, $u->id, $now);

        // ── Return 2 — 3 TWS earphone cacat ke Distributor aksesoris & audio ──
        $p  = $po('PO-T1-20260312-001');
        $pi = $poItem($p->id, 'TWS-ANK');

        $pr2 = DB::table('purchase_returns')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $t->id,
            'user_id'          => $u->id,
            'purchase_id'      => $p->id,
            'supplier_id'      => $sup('SUP-003')->id,
            'reference_number' => 'PR-T1-20260314-001',
            'return_date'      => '2026-03-14',
            'total_return'     => 630000,
            'status'           => 'approved',
            'return_method'    => 'replacement',
            'reason'           => 'TWS earphone tidak dapat terhubung via Bluetooth dan suara sebelah kiri mati',
            'notes'            => 'Supplier setuju kirim pengganti',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        DB::table('purchase_return_items')->insert([[
            'purchase_return_id' => $pr2,
            'product_id'         => DB::table('products')->where('sku', 'TWS-ANK')->value('id'),
            'purchase_item_id'   => $pi->id,
            'qty'                => 3,
            'cost_price'         => 210000,
            'subtotal'           => 630000,
            'reason'             => 'Unit cacat produksi, Bluetooth tidak stabil dan channel kiri tidak berfungsi',
        ]]);
        $this->removeStock('TWS-ANK', 3, 'purchase_return', $pr2, $t->id, $u->id, $now);
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
