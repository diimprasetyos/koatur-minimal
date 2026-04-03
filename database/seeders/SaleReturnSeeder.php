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

        $ownerUser = fn(string $slug) => DB::table('users')
            ->where('email', 'like', 'owner.%')
            ->whereIn('id', function ($q) use ($slug) {
                $q->select('user_id')->from('tenant_user')
                    ->where('tenant_id', DB::table('tenants')->where('slug', $slug)->value('id'));
            })->first();

        $sale = fn(string $inv) => DB::table('sales')->where('invoice_number', $inv)->first();
        $saleItem = fn(int $sid, string $sku) => DB::table('sale_items')
            ->where('sale_id', $sid)
            ->where('product_id', DB::table('products')->where('sku', $sku)->value('id'))
            ->first();

        // ─── RETURN 1: Toko 1 — Agus kembalikan 2 Indomie (rusak) ───
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u = $ownerUser('toko-1');
        $s = $sale('INV-T1-20260310-001');
        $si = $saleItem($s->id, 'IMG-001');

        $sr1 = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'sale_id' => $s->id,
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
        DB::table('sale_return_items')->insert([['sale_return_id' => $sr1, 'product_id' => DB::table('products')->where('sku', 'IMG-001')->value('id'), 'sale_item_id' => $si->id, 'qty' => 2, 'price' => 3500, 'subtotal' => 7000, 'reason' => 'Kemasan penyok']]);
        $this->returnStock('IMG-001', 2, 'sale_return', $sr1, $t->id, $u->id, $now);

        // ─── RETURN 2: Toko 2 — Rina kembalikan gula (salah beli) ───
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $u = $ownerUser('toko-2');
        $s = $sale('INV-T2-20260312-001');
        $si = $saleItem($s->id, 'GUL-1KG');

        $sr2 = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'sale_id' => $s->id,
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
        DB::table('sale_return_items')->insert([['sale_return_id' => $sr2, 'product_id' => DB::table('products')->where('sku', 'GUL-1KG')->value('id'), 'sale_item_id' => $si->id, 'qty' => 1, 'price' => 16000, 'subtotal' => 16000, 'reason' => 'Salah beli']]);
        $this->returnStock('GUL-1KG', 1, 'sale_return', $sr2, $t->id, $u->id, $now);

        // ─── RETURN 3: Toko 4 — Vitamin C expired ───
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $u = $ownerUser('toko-4');
        $s = $sale('INV-T4-20260311-001');
        $si = $saleItem($s->id, 'VIT-C1K');

        $sr3 = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'sale_id' => $s->id,
            'reference_number' => 'SR-T4-20260312-001',
            'return_date' => '2026-03-12',
            'total_refund' => 45000,
            'status' => 'approved',
            'refund_method' => 'refund',
            'reason' => 'Produk mendekati tanggal kadaluarsa',
            'notes' => 'Refund tunai, produk ditarik dari rak',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('sale_return_items')->insert([['sale_return_id' => $sr3, 'product_id' => DB::table('products')->where('sku', 'VIT-C1K')->value('id'), 'sale_item_id' => $si->id, 'qty' => 1, 'price' => 45000, 'subtotal' => 45000, 'reason' => 'Hampir kadaluarsa']]);
        $this->returnStock('VIT-C1K', 1, 'sale_return', $sr3, $t->id, $u->id, $now);

        // ─── RETURN 4: Toko 6 — Tempered glass tidak cocok ───
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $u = $ownerUser('toko-6');
        $s = $sale('INV-T6-20260313-001');
        $si = $saleItem($s->id, 'AKS-TG');

        $sr4 = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'sale_id' => $s->id,
            'reference_number' => 'SR-T6-20260314-001',
            'return_date' => '2026-03-14',
            'total_refund' => 25000,
            'status' => 'approved',
            'refund_method' => 'exchange',
            'reason' => 'Ukuran tidak cocok dengan HP pelanggan',
            'notes' => 'Ditukar dengan tempered glass yang sesuai',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('sale_return_items')->insert([['sale_return_id' => $sr4, 'product_id' => DB::table('products')->where('sku', 'AKS-TG')->value('id'), 'sale_item_id' => $si->id, 'qty' => 1, 'price' => 25000, 'subtotal' => 25000, 'reason' => 'Ukuran tidak pas']]);
        $this->returnStock('AKS-TG', 1, 'sale_return', $sr4, $t->id, $u->id, $now);

        // ─── RETURN 5: Toko 9 — Kampas rem tidak sesuai motor ───
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $u = $ownerUser('toko-9');
        $s = $sale('INV-T9-20260310-001');
        $si = $saleItem($s->id, 'SPR-KRM');

        $sr5 = DB::table('sale_returns')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $t->id,
            'user_id' => $u->id,
            'sale_id' => $s->id,
            'reference_number' => 'SR-T9-20260311-001',
            'return_date' => '2026-03-11',
            'total_refund' => 65000,
            'status' => 'approved',
            'refund_method' => 'exchange',
            'reason' => 'Tipe spare part tidak sesuai motor pelanggan',
            'notes' => 'Ditukar ke part yang sesuai',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('sale_return_items')->insert([['sale_return_id' => $sr5, 'product_id' => DB::table('products')->where('sku', 'SPR-KRM')->value('id'), 'sale_item_id' => $si->id, 'qty' => 1, 'price' => 65000, 'subtotal' => 65000, 'reason' => 'Tipe tidak sesuai']]);
        $this->returnStock('SPR-KRM', 1, 'sale_return', $sr5, $t->id, $u->id, $now);
    }

    private function returnStock(string $sku, int $qty, string $refType, int $refId, int $tenantId, int $userId, $now): void
    {
        $product = DB::table('products')->where('sku', $sku)->first();
        $stockBefore = $product->stock;
        $stockAfter = $stockBefore + $qty;

        DB::table('stock_movements')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $product->id,
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

        DB::table('products')->where('id', $product->id)->update(['stock' => $stockAfter]);
    }
}