<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
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

        $prod = fn(string $sku) => DB::table('products')->where('sku', $sku)->first();
        $sup = fn(int $tid, string $code) => DB::table('suppliers')->where('tenant_id', $tid)->where('code', $code)->first();

        // ─── TOKO 1 — Toko Maju Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u = $ownerUser('toko-1');

        $po1 = $this->purchase($t->id, $u->id, $sup($t->id, 'SUP-001')->id, 'PO-T1-20260305-001', 'INV-IDF-20260305', '2026-03-05', null, 1120000, 0, 0, 1120000, 1120000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po1, [[$prod('IMG-001'), 200, 200, 2800], [$prod('AQU-001'), 150, 150, 3000]], $t->id, $u->id, $now);

        $po2 = $this->purchase($t->id, $u->id, $sup($t->id, 'SUP-002')->id, 'PO-T1-20260308-001', null, '2026-03-08', '2026-04-08', 950000, 0, 0, 950000, 600000, 350000, 'received', 'partial', 'transfer', 'Sisa hutang dibayar bulan depan', $now);
        $this->purchaseItems($po2, [[$prod('CHG-065'), 10, 10, 95000]], $t->id, $u->id, $now);

        $po3 = $this->purchase($t->id, $u->id, $sup($t->id, 'SUP-003')->id, 'PO-T1-20260312-001', 'DIST-INV-3201', '2026-03-12', null, 3200000, 200000, 0, 3000000, 3000000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po3, [[$prod('KPS-W-L'), 40, 40, 40000], [$prod('KPS-B-M'), 40, 40, 40000]], $t->id, $u->id, $now);

        // ─── TOKO 2 — Warung Berkah Abadi ───
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $u = $ownerUser('toko-2');

        $po4 = $this->purchase($t->id, $u->id, $sup($t->id, 'S-003')->id, 'PO-T2-20260307-001', null, '2026-03-07', '2026-03-14', 2770000, 0, 0, 2770000, 2645000, 125000, 'received', 'partial', 'cash', null, $now);
        $this->purchaseItems($po4, [[$prod('BRS-5KG'), 20, 20, 62000], [$prod('MNY-1LT'), 30, 30, 14500], [$prod('GUL-1KG'), 50, 50, 13000]], $t->id, $u->id, $now);

        $po5 = $this->purchase($t->id, $u->id, $sup($t->id, 'S-002')->id, 'PO-T2-20260314-001', 'CC-INV-031401', '2026-03-14', null, 900000, 0, 0, 900000, 900000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po5, [[$prod('TEH-350'), 100, 100, 4500], [$prod('CHT-068'), 50, 50, 9000]], $t->id, $u->id, $now);

        // ─── TOKO 3 — CV Sumber Rejeki ───
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $u = $ownerUser('toko-3');

        $po6 = $this->purchase($t->id, $u->id, $sup($t->id, 'VND-001')->id, 'PO-T3-20260301-001', 'KS-INV-2026031', '2026-03-01', '2026-04-01', 7250000, 250000, 0, 7000000, 0, 7000000, 'received', 'unpaid', 'transfer', 'Pembayaran net 30 hari', $now);
        $this->purchaseItems($po6, [[$prod('BSH-446'), 50, 50, 145000]], $t->id, $u->id, $now);

        $po7 = $this->purchase($t->id, $u->id, $sup($t->id, 'VND-002')->id, 'PO-T3-20260315-001', null, '2026-03-15', null, 1700000, 0, 0, 1700000, 1700000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po7, [[$prod('CAT-25K'), 5, 5, 340000]], $t->id, $u->id, $now);

        // ─── TOKO 4 — Apotek Sehat Sentosa ───
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $u = $ownerUser('toko-4');

        $po8 = $this->purchase($t->id, $u->id, $sup($t->id, 'APT-S001')->id, 'PO-T4-20260303-001', 'KF-INV-030301', '2026-03-03', '2026-04-03', 740000, 0, 0, 740000, 0, 740000, 'received', 'unpaid', 'transfer', 'Net 30 hari', $now);
        $this->purchaseItems($po8, [[$prod('OBT-PCT'), 100, 100, 3200], [$prod('VIT-C1K'), 10, 10, 32000]], $t->id, $u->id, $now);

        $po9 = $this->purchase($t->id, $u->id, $sup($t->id, 'APT-S002')->id, 'PO-T4-20260310-001', null, '2026-03-10', null, 550000, 0, 0, 550000, 550000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po9, [[$prod('OBT-ANT'), 100, 100, 2500], [$prod('OBT-PMG'), 50, 50, 4500]], $t->id, $u->id, $now);

        // ─── TOKO 5 — Toko Bangunan Kokoh ───
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $u = $ownerUser('toko-5');

        $po10 = $this->purchase($t->id, $u->id, $sup($t->id, 'BNG-S001')->id, 'PO-T5-20260302-001', 'SI-INV-030201', '2026-03-02', '2026-04-02', 11000000, 0, 0, 11000000, 0, 11000000, 'received', 'unpaid', 'transfer', 'Net 30 hari pembayaran', $now);
        $this->purchaseItems($po10, [[$prod('SMN-TR50'), 100, 100, 55000], [$prod('BSI-D10'), 60, 60, 98000]], $t->id, $u->id, $now);

        $po11 = $this->purchase($t->id, $u->id, $sup($t->id, 'BNG-S003')->id, 'PO-T5-20260310-001', null, '2026-03-10', null, 2800000, 0, 0, 2800000, 2800000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po11, [[$prod('KRM-6060'), 20, 20, 140000]], $t->id, $u->id, $now);

        // ─── TOKO 6 — UD Elektronik Mandiri ───
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $u = $ownerUser('toko-6');

        $po12 = $this->purchase($t->id, $u->id, $sup($t->id, 'ELK-S001')->id, 'PO-T6-20260304-001', 'SS-INV-030401', '2026-03-04', '2026-04-04', 18500000, 500000, 0, 18000000, 9000000, 9000000, 'received', 'partial', 'transfer', 'DP 50%, sisa net 30', $now);
        $this->purchaseItems($po12, [[$prod('HP-SGA15'), 5, 5, 1850000], [$prod('HP-RDM13'), 5, 5, 1250000]], $t->id, $u->id, $now);

        $po13 = $this->purchase($t->id, $u->id, $sup($t->id, 'ELK-S003')->id, 'PO-T6-20260312-001', null, '2026-03-12', null, 1200000, 0, 0, 1200000, 1200000, 0, 'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po13, [[$prod('AKS-TG'), 50, 50, 12000], [$prod('AKS-CSL'), 30, 30, 15000]], $t->id, $u->id, $now);

        // ─── TOKO 7 — Butik Mode Terkini ───
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $u = $ownerUser('toko-7');

        $po14 = $this->purchase($t->id, $u->id, $sup($t->id, 'BUT-S001')->id, 'PO-T7-20260305-001', 'FN-INV-030501', '2026-03-05', '2026-03-19', 8750000, 750000, 0, 8000000, 6000000, 2000000, 'received', 'partial', 'transfer', 'Sisa bayar 2 minggu', $now);
        $this->purchaseItems($po14, [[$prod('PWN-DBT'), 20, 20, 220000], [$prod('PPR-KMS'), 20, 20, 110000], [$prod('PPR-CLC'), 20, 20, 135000]], $t->id, $u->id, $now);

        $po15 = $this->purchase($t->id, $u->id, $sup($t->id, 'BUT-S002')->id, 'PO-T7-20260314-001', null, '2026-03-14', null, 1650000, 0, 0, 1650000, 1650000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po15, [[$prod('TAS-SLM'), 10, 10, 165000]], $t->id, $u->id, $now);

        // ─── TOKO 8 — Minimarket Segar Prima ───
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $u = $ownerUser('toko-8');

        $po16 = $this->purchase($t->id, $u->id, $sup($t->id, 'MNI-S001')->id, 'PO-T8-20260302-001', 'IAP-INV-030201', '2026-03-02', '2026-04-02', 7400000, 0, 0, 7400000, 0, 7400000, 'received', 'unpaid', 'transfer', 'Net 30', $now);
        $this->purchaseItems($po16, [[$prod('MKI-IMG'), 200, 200, 2800], [$prod('MKI-PMI'), 100, 100, 4500], [$prod('MNK-AQ15'), 100, 100, 5000]], $t->id, $u->id, $now);

        $po17 = $this->purchase($t->id, $u->id, $sup($t->id, 'MNI-S002')->id, 'PO-T8-20260310-001', null, '2026-03-10', null, 2200000, 0, 0, 2200000, 2200000, 0, 'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po17, [[$prod('SGR-TLR'), 50, 50, 22000], [$prod('SGR-AYM'), 30, 30, 28000]], $t->id, $u->id, $now);

        // ─── TOKO 9 — Bengkel Motor Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $u = $ownerUser('toko-9');

        $po18 = $this->purchase($t->id, $u->id, $sup($t->id, 'BNK-S001')->id, 'PO-T9-20260303-001', 'AHM-INV-030301', '2026-03-03', '2026-04-03', 4990000, 0, 0, 4990000, 0, 4990000, 'received', 'unpaid', 'transfer', 'Bayar via distributor AHM', $now);
        $this->purchaseItems($po18, [[$prod('SPR-KRM'), 50, 50, 45000], [$prod('SPR-FLT'), 40, 40, 32000], [$prod('SPR-BSI'), 60, 60, 24000]], $t->id, $u->id, $now);

        $po19 = $this->purchase($t->id, $u->id, $sup($t->id, 'BNK-S002')->id, 'PO-T9-20260310-001', null, '2026-03-10', null, 2380000, 0, 0, 2380000, 2380000, 0, 'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po19, [[$prod('OLI-FDR'), 40, 40, 37000], [$prod('OLI-SHL'), 30, 30, 43000]], $t->id, $u->id, $now);

        // ─── TOKO 10 — Toko Peralatan Dapur ───
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $u = $ownerUser('toko-10');

        $po20 = $this->purchase($t->id, $u->id, $sup($t->id, 'DPR-S001')->id, 'PO-T10-20260303-001', 'MAS-INV-030301', '2026-03-03', '2026-04-03', 5620000, 0, 0, 5620000, 2810000, 2810000, 'received', 'partial', 'transfer', 'DP 50%', $now);
        $this->purchaseItems($po20, [[$prod('MSK-WJN'), 20, 20, 125000], [$prod('MSK-PRS'), 10, 10, 240000], [$prod('LST-RC18'), 8, 8, 180000]], $t->id, $u->id, $now);

        $po21 = $this->purchase($t->id, $u->id, $sup($t->id, 'DPR-S002')->id, 'PO-T10-20260312-001', null, '2026-03-12', null, 1300000, 0, 0, 1300000, 1300000, 0, 'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po21, [[$prod('WDH-TPS'), 20, 20, 35000], [$prod('WDH-SWR'), 10, 10, 50000], [$prod('MKN-PLR'), 10, 10, 65000]], $t->id, $u->id, $now);

        // ─── TOKO 11 — Koperasi Makmur Bersama ───
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $u = $ownerUser('toko-11');

        $po22 = $this->purchase($t->id, $u->id, $sup($t->id, 'KOP-S001')->id, 'PO-T11-20260305-001', null, '2026-03-05', '2026-03-12', 2870000, 0, 0, 2870000, 1870000, 1000000, 'received', 'partial', 'cash', null, $now);
        $this->purchaseItems($po22, [[$prod('KOP-BRS'), 30, 30, 65000], [$prod('KOP-GUL'), 40, 40, 13500], [$prod('KOP-MNY'), 25, 25, 26000]], $t->id, $u->id, $now);

        $po23 = $this->purchase($t->id, $u->id, $sup($t->id, 'KOP-S002')->id, 'PO-T11-20260312-001', null, '2026-03-12', null, 725000, 0, 0, 725000, 725000, 0, 'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po23, [[$prod('KOP-BUK'), 50, 50, 8500], [$prod('KOP-PLN'), 75, 75, 4500]], $t->id, $u->id, $now);

        // ─── TOKO 12 — Toko Olahraga Sporty ───
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $u = $ownerUser('toko-12');

        $po24 = $this->purchase($t->id, $u->id, $sup($t->id, 'SPT-S001')->id, 'PO-T12-20260304-001', 'NK-INV-030401', '2026-03-04', '2026-04-04', 12950000, 450000, 0, 12500000, 0, 12500000, 'received', 'unpaid', 'transfer', 'Net 30 sesuai kontrak', $now);
        $this->purchaseItems($po24, [[$prod('SPT-SBD'), 10, 10, 320000], [$prod('SPT-SRN'), 10, 10, 490000], [$prod('SPT-WHY'), 10, 10, 270000]], $t->id, $u->id, $now);

        $po25 = $this->purchase($t->id, $u->id, $sup($t->id, 'SPT-S002')->id, 'PO-T12-20260312-001', null, '2026-03-12', null, 4200000, 0, 0, 4200000, 4200000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po25, [[$prod('SPT-JRS'), 20, 20, 95000], [$prod('SPT-CLT'), 20, 20, 78000], [$prod('SPT-DBL'), 10, 10, 140000], [$prod('SPT-MAT'), 10, 10, 115000]], $t->id, $u->id, $now);

        // ─── TOKO 13 — Percetakan Digital Cepat ───
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $u = $ownerUser('toko-13');

        $po26 = $this->purchase($t->id, $u->id, $sup($t->id, 'PCT-S001')->id, 'PO-T13-20260303-001', 'IK-INV-030301', '2026-03-03', '2026-04-03', 8400000, 0, 0, 8400000, 0, 8400000, 'received', 'unpaid', 'transfer', 'Net 30', $now);
        $this->purchaseItems($po26, [[$prod('BCT-HVS'), 100, 100, 42000], [$prod('BCT-GLS'), 500, 500, 3200]], $t->id, $u->id, $now);

        $po27 = $this->purchase($t->id, $u->id, $sup($t->id, 'PCT-S002')->id, 'PO-T13-20260310-001', null, '2026-03-10', null, 4050000, 0, 0, 4050000, 4050000, 0, 'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po27, [[$prod('BCT-TNT'), 30, 30, 135000]], $t->id, $u->id, $now);

        $po28 = $this->purchase($t->id, $u->id, $sup($t->id, 'PCT-S003')->id, 'PO-T13-20260315-001', null, '2026-03-15', null, 1750000, 0, 0, 1750000, 1750000, 0, 'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po28, [[$prod('SOU-MUG'), 30, 30, 35000], [$prod('SOU-KOS'), 20, 20, 60000]], $t->id, $u->id, $now);
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────

    private function purchase(
        int $tenantId,
        int $userId,
        int $supplierId,
        string $ref,
        ?string $invoice,
        string $date,
        ?string $dueDate,
        float $subtotal,
        float $discount,
        float $tax,
        float $total,
        float $paid,
        float $due,
        string $status,
        string $paymentStatus,
        string $method,
        ?string $notes,
        $now
    ): int {
        return DB::table('purchases')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'supplier_id' => $supplierId,
            'reference_number' => $ref,
            'supplier_invoice' => $invoice,
            'purchase_date' => $date,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'paid' => $paid,
            'due' => $due,
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => $method,
            'notes' => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /** @param array<array{0:object,1:int,2:int,3:float}> $items [product, qty, qty_received, cost_price] */
    private function purchaseItems(int $purchaseId, array $items, int $tenantId, int $userId, $now): void
    {
        $rows = [];
        foreach ($items as [$prod, $qty, $qtyReceived, $costPrice]) {
            $rows[] = [
                'purchase_id' => $purchaseId,
                'product_id' => $prod->id,
                'qty' => $qty,
                'qty_received' => $qtyReceived,
                'cost_price' => $costPrice,
                'subtotal' => $qty * $costPrice,
            ];
            $this->stockIn($tenantId, $userId, $prod->id, 'purchase', $purchaseId, $qtyReceived, $now);
        }
        DB::table('purchase_items')->insert($rows);
    }

    private function stockIn(int $tenantId, int $userId, int $productId, string $refType, int $refId, int $qty, $now): void
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