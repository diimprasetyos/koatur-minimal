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

        $ownerUser = fn(string $slug) => DB::table('users')
            ->where('email', 'like', 'owner.%')
            ->whereIn('id', function ($q) use ($slug) {
                $q->select('user_id')->from('tenant_user')
                    ->where('tenant_id', DB::table('tenants')->where('slug', $slug)->value('id'));
            })->first();

        $po = fn(string $ref) => DB::table('purchases')->where('reference_number', $ref)->first();
        $poItem = fn(int $pid, string $sku) => DB::table('purchase_items')
            ->where('purchase_id', $pid)
            ->where('product_id', DB::table('products')->where('sku', $sku)->value('id'))
            ->first();
        $sup = fn(int $tid, string $code) => DB::table('suppliers')->where('tenant_id', $tid)->where('code', $code)->first();

        // ─── RETURN 1: Toko 1 — Kembalikan 1 charger rusak ke Elektro Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u = $ownerUser('toko-1');
        $p = $po('PO-T1-20260308-001');
        $pi = $poItem($p->id, 'CHG-065');

        $pr1 = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'purchase_id' => $p->id,
            'supplier_id' => $sup($t->id, 'SUP-002')->id,
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
        DB::table('purchase_return_items')->insert([['purchase_return_id' => $pr1, 'product_id' => DB::table('products')->where('sku', 'CHG-065')->value('id'), 'purchase_item_id' => $pi->id, 'qty' => 1, 'cost_price' => 95000, 'subtotal' => 95000, 'reason' => 'Unit rusak, tidak berfungsi']]);
        $this->removeStock('CHG-065', 1, 'purchase_return', $pr1, $t->id, $u->id, $now);

        // ─── RETURN 2: Toko 3 — Besi hollow cacat ke Krakatau Steel ───
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $u = $ownerUser('toko-3');
        $p = $po('PO-T3-20260301-001');
        $pi = $poItem($p->id, 'BSH-446');

        $pr2 = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'purchase_id' => $p->id,
            'supplier_id' => $sup($t->id, 'VND-001')->id,
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
        DB::table('purchase_return_items')->insert([['purchase_return_id' => $pr2, 'product_id' => DB::table('products')->where('sku', 'BSH-446')->value('id'), 'purchase_item_id' => $pi->id, 'qty' => 5, 'cost_price' => 145000, 'subtotal' => 725000, 'reason' => 'Dimensi tidak sesuai & material bengkok']]);
        $this->removeStock('BSH-446', 5, 'purchase_return', $pr2, $t->id, $u->id, $now);

        // ─── RETURN 3: Toko 5 — Semen rusak basah dari PT Semen Indonesia ───
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $u = $ownerUser('toko-5');
        $p = $po('PO-T5-20260302-001');
        $pi = $poItem($p->id, 'SMN-TR50');

        $pr3 = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'purchase_id' => $p->id,
            'supplier_id' => $sup($t->id, 'BNG-S001')->id,
            'reference_number' => 'PR-T5-20260305-001',
            'return_date' => '2026-03-05',
            'total_return' => 550000,
            'status' => 'approved',
            'return_method' => 'replacement',
            'reason' => 'Beberapa sak semen basah dan mengeras saat tiba',
            'notes' => 'Penggantian dijadwalkan pengiriman berikutnya',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('purchase_return_items')->insert([['purchase_return_id' => $pr3, 'product_id' => DB::table('products')->where('sku', 'SMN-TR50')->value('id'), 'purchase_item_id' => $pi->id, 'qty' => 10, 'cost_price' => 55000, 'subtotal' => 550000, 'reason' => 'Semen basah/keras sebelum digunakan']]);
        $this->removeStock('SMN-TR50', 10, 'purchase_return', $pr3, $t->id, $u->id, $now);

        // ─── RETURN 4: Toko 8 — Ayam broiler tidak segar dari UD Sumber Sari ───
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $u = $ownerUser('toko-8');
        $p = $po('PO-T8-20260310-001');
        $pi = $poItem($p->id, 'SGR-AYM');

        $pr4 = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'purchase_id' => $p->id,
            'supplier_id' => $sup($t->id, 'MNI-S002')->id,
            'reference_number' => 'PR-T8-20260311-001',
            'return_date' => '2026-03-11',
            'total_return' => 280000,
            'status' => 'approved',
            'return_method' => 'refund',
            'reason' => 'Ayam broiler tidak memenuhi standar kesegaran',
            'notes' => 'Refund dikreditkan ke pembelian berikutnya',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('purchase_return_items')->insert([['purchase_return_id' => $pr4, 'product_id' => DB::table('products')->where('sku', 'SGR-AYM')->value('id'), 'purchase_item_id' => $pi->id, 'qty' => 10, 'cost_price' => 28000, 'subtotal' => 280000, 'reason' => 'Kualitas tidak memenuhi standar']]);
        $this->removeStock('SGR-AYM', 10, 'purchase_return', $pr4, $t->id, $u->id, $now);

        // ─── RETURN 5: Toko 12 — Jersey cacat dari UD Olahraga Nasional ───
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $u = $ownerUser('toko-12');
        $p = $po('PO-T12-20260312-001');
        $pi = $poItem($p->id, 'SPT-JRS');

        $pr5 = DB::table('purchase_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'purchase_id' => $p->id,
            'supplier_id' => $sup($t->id, 'SPT-S002')->id,
            'reference_number' => 'PR-T12-20260313-001',
            'return_date' => '2026-03-13',
            'total_return' => 475000,
            'status' => 'approved',
            'return_method' => 'replacement',
            'reason' => 'Sablon jersey buram dan tidak rata',
            'notes' => 'Supplier ganti dengan produksi ulang',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('purchase_return_items')->insert([['purchase_return_id' => $pr5, 'product_id' => DB::table('products')->where('sku', 'SPT-JRS')->value('id'), 'purchase_item_id' => $pi->id, 'qty' => 5, 'cost_price' => 95000, 'subtotal' => 475000, 'reason' => 'Sablon buram dan tidak rata']]);
        $this->removeStock('SPT-JRS', 5, 'purchase_return', $pr5, $t->id, $u->id, $now);
    }

    private function removeStock(string $sku, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
    {
        $product = DB::table('products')->where('sku', $sku)->first();
        $stockBefore = $product->stock;
        $stockAfter = max(0, $stockBefore - $qty);

        DB::table('stock_movements')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $product->id,
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

        DB::table('products')->where('id', $product->id)->update(['stock' => $stockAfter]);
    }
}