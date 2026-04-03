<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Helper: owner user per tenant
        $ownerUser = fn(string $slug) => DB::table('users')
            ->where('email', 'like', 'owner.%')
            ->whereIn('id', function ($q) use ($slug) {
                $q->select('user_id')->from('tenant_user')
                    ->where('tenant_id', DB::table('tenants')->where('slug', $slug)->value('id'));
            })->first();

        $prod = fn(string $sku) => DB::table('products')->where('sku', $sku)->first();
        $cust = fn(int $tid, string $name) => DB::table('customers')->where('tenant_id', $tid)->where('name', $name)->first();

        // ─── TOKO 1 — Toko Maju Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u = $ownerUser('toko-1');

        $s1 = $this->sale($t->id, $u->id, $cust($t->id, 'Agus Prasetyo')->id, 'INV-T1-20260310-001', '2026-03-10', 'cash', 37500, 0, 37500, 40000, 2500, 'paid', null, $now);
        $this->saleItems($s1, [
            [$prod('IMG-001'), 5, 3500, 2800, 0],
            [$prod('AQU-001'), 5, 4000, 3000, 0],
        ], $t->id, $u->id, $now);

        $s2 = $this->sale($t->id, $u->id, $cust($t->id, 'Dewi Rahayu')->id, 'INV-T1-20260315-001', '2026-03-15', 'qris', 215000, 15000, 200000, 200000, 0, 'paid', 'Diskon member 7%', $now);
        $this->saleItems($s2, [
            [$prod('CHG-065'), 1, 150000, 95000, 10000],
            [$prod('KPS-W-L'), 1, 65000, 40000, 5000],
        ], $t->id, $u->id, $now);

        $s3 = $this->sale($t->id, $u->id, $cust($t->id, 'Toko Sinar Mas')->id, 'INV-T1-20260320-001', '2026-03-20', 'transfer', 400000, 0, 400000, 250000, 0, 'partial', 'Sisa tagihan Rp 150.000', $now);
        $this->saleItems($s3, [
            [$prod('PEN-BLK'), 10, 25000, 15000, 0],
            [$prod('BUK-058'), 20, 7500, 5000, 0],
        ], $t->id, $u->id, $now);

        $s4 = $this->sale($t->id, $u->id, null, 'INV-T1-20260322-001', '2026-03-22', 'cash', 17500, 0, 17500, 20000, 2500, 'paid', null, $now);
        $this->saleItems($s4, [
            [$prod('IMG-001'), 3, 3500, 2800, 0],
            [$prod('AQU-001'), 2, 4000, 3000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 2 — Warung Berkah Abadi ───
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $u = $ownerUser('toko-2');

        $s5 = $this->sale($t->id, $u->id, $cust($t->id, 'Rina Marlina')->id, 'INV-T2-20260312-001', '2026-03-12', 'cash', 109000, 0, 109000, 110000, 1000, 'paid', null, $now);
        $this->saleItems($s5, [
            [$prod('BRS-5KG'), 1, 75000, 62000, 0],
            [$prod('MNY-1LT'), 1, 18000, 14500, 0],
            [$prod('GUL-1KG'), 1, 16000, 13000, 0],
        ], $t->id, $u->id, $now);

        $s6 = $this->sale($t->id, $u->id, $cust($t->id, 'Kantin Sekolah SMK 1')->id, 'INV-T2-20260317-001', '2026-03-17', 'transfer', 330000, 30000, 300000, 300000, 0, 'paid', 'Diskon grosir 10%', $now);
        $this->saleItems($s6, [
            [$prod('TEH-350'), 20, 6000, 4500, 0],
            [$prod('CHT-068'), 12, 12000, 9000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 3 — CV Sumber Rejeki ───
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $u = $ownerUser('toko-3');

        $s7 = $this->sale($t->id, $u->id, $cust($t->id, 'PT Graha Bangun Jaya')->id, 'INV-T3-20260310-001', '2026-03-10', 'transfer', 1850000, 0, 1850000, 1850000, 0, 'paid', null, $now);
        $this->saleItems($s7, [
            [$prod('BSH-446'), 10, 185000, 145000, 0],
        ], $t->id, $u->id, $now);

        $s8 = $this->sale($t->id, $u->id, $cust($t->id, 'CV Mitra Konstruksi')->id, 'INV-T3-20260318-001', '2026-03-18', 'transfer', 2500000, 0, 2500000, 0, 0, 'unpaid', 'Termin 30 hari', $now);
        $this->saleItems($s8, [
            [$prod('KSP-ALU'), 2, 850000, 600000, 0],
            [$prod('CAT-25K'), 2, 420000, 340000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 4 — Apotek Sehat Sentosa ───
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $u = $ownerUser('toko-4');

        $s9 = $this->sale($t->id, $u->id, $cust($t->id, 'Hendra Wijaya')->id, 'INV-T4-20260311-001', '2026-03-11', 'cash', 16500, 0, 16500, 20000, 3500, 'paid', null, $now);
        $this->saleItems($s9, [
            [$prod('OBT-PCT'), 2, 5000, 3200, 0],
            [$prod('VIT-C1K'), 1, 45000, 32000, 0],
        ], $t->id, $u->id, $now);

        $s10 = $this->sale($t->id, $u->id, $cust($t->id, 'Klinik Husada Sehat')->id, 'INV-T4-20260316-001', '2026-03-16', 'transfer', 350000, 0, 350000, 350000, 0, 'paid', 'Resep bulanan klinik', $now);
        $this->saleItems($s10, [
            [$prod('OBT-ANT'), 20, 3500, 2500, 0],
            [$prod('OBT-PMG'), 10, 6500, 4500, 0],
            [$prod('ALK-MSK'), 2, 35000, 22000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 5 — Toko Bangunan Kokoh ───
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $u = $ownerUser('toko-5');

        $s11 = $this->sale($t->id, $u->id, $cust($t->id, 'PT Griya Indah Properti')->id, 'INV-T5-20260309-001', '2026-03-09', 'transfer', 7680000, 0, 7680000, 7680000, 0, 'paid', null, $now);
        $this->saleItems($s11, [
            [$prod('SMN-TR50'), 50, 68000, 55000, 0],
            [$prod('BSI-D10'), 20, 125000, 98000, 0],
        ], $t->id, $u->id, $now);

        $s12 = $this->sale($t->id, $u->id, $cust($t->id, 'CV Karya Prima')->id, 'INV-T5-20260318-001', '2026-03-18', 'cash', 2095000, 0, 2095000, 2095000, 0, 'paid', null, $now);
        $this->saleItems($s12, [
            [$prod('CAT-DLX'), 2, 550000, 430000, 0],
            [$prod('KRM-6060'), 5, 185000, 140000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 6 — UD Elektronik Mandiri ───
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $u = $ownerUser('toko-6');

        $s13 = $this->sale($t->id, $u->id, $cust($t->id, 'Rizky Pratama')->id, 'INV-T6-20260313-001', '2026-03-13', 'cash', 2234000, 0, 2234000, 2234000, 0, 'paid', null, $now);
        $this->saleItems($s13, [
            [$prod('HP-RDM13'), 1, 1499000, 1250000, 0],
            [$prod('AKS-TG'), 2, 25000, 12000, 0],
            [$prod('KBL-USBC'), 1, 45000, 28000, 0],
        ], $t->id, $u->id, $now);

        $s14 = $this->sale($t->id, $u->id, $cust($t->id, 'Toko Pulsa Kencana')->id, 'INV-T6-20260319-001', '2026-03-19', 'transfer', 325000, 25000, 300000, 300000, 0, 'paid', 'Diskon reseller 8%', $now);
        $this->saleItems($s14, [
            [$prod('AKS-CSL'), 5, 30000, 15000, 0],
            [$prod('AKS-CHG'), 4, 35000, 22000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 7 — Butik Mode Terkini ───
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $u = $ownerUser('toko-7');

        $s15 = $this->sale($t->id, $u->id, $cust($t->id, 'Sari Dewi Lestari')->id, 'INV-T7-20260312-001', '2026-03-12', 'qris', 595000, 0, 595000, 595000, 0, 'paid', null, $now);
        $this->saleItems($s15, [
            [$prod('PWN-DBT'), 1, 350000, 220000, 0],
            [$prod('TAS-DPT'), 1, 155000, 90000, 0],
        ], $t->id, $u->id, $now);

        $s16 = $this->sale($t->id, $u->id, $cust($t->id, 'Toko Grosir Mama')->id, 'INV-T7-20260321-001', '2026-03-21', 'transfer', 1950000, 150000, 1800000, 1800000, 0, 'paid', 'Diskon grosir', $now);
        $this->saleItems($s16, [
            [$prod('PPR-KMS'), 5, 185000, 110000, 0],
            [$prod('PPR-CLC'), 5, 210000, 135000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 8 — Minimarket Segar Prima ───
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $u = $ownerUser('toko-8');

        $s17 = $this->sale($t->id, $u->id, $cust($t->id, 'Indah Permatasari')->id, 'INV-T8-20260311-001', '2026-03-11', 'cash', 56500, 0, 56500, 60000, 3500, 'paid', null, $now);
        $this->saleItems($s17, [
            [$prod('SGR-TLR'), 1, 28000, 22000, 0],
            [$prod('MNK-AQ15'), 2, 7000, 5000, 0],
            [$prod('MKI-IMG'), 2, 3500, 2800, 0],
        ], $t->id, $u->id, $now);

        $s18 = $this->sale($t->id, $u->id, $cust($t->id, 'Panti Asuhan Al-Ikhlas')->id, 'INV-T8-20260316-001', '2026-03-16', 'transfer', 385000, 0, 385000, 385000, 0, 'paid', 'Pembelian bulanan panti', $now);
        $this->saleItems($s18, [
            [$prod('MKI-IMG'), 30, 3500, 2800, 0],
            [$prod('KBS-SUN'), 5, 22000, 16000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 9 — Bengkel Motor Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $u = $ownerUser('toko-9');

        $s19 = $this->sale($t->id, $u->id, $cust($t->id, 'Doni Setiawan')->id, 'INV-T9-20260310-001', '2026-03-10', 'cash', 113000, 0, 113000, 120000, 7000, 'paid', 'Servis + ganti oli', $now);
        $this->saleItems($s19, [
            [$prod('OLI-FDR'), 1, 48000, 37000, 0],
            [$prod('SPR-KRM'), 1, 65000, 45000, 0],
        ], $t->id, $u->id, $now);

        $s20 = $this->sale($t->id, $u->id, $cust($t->id, 'Rental Motor Ceria')->id, 'INV-T9-20260317-001', '2026-03-17', 'transfer', 620000, 0, 620000, 620000, 0, 'paid', 'Ganti oli & tune-up 4 unit', $now);
        $this->saleItems($s20, [
            [$prod('OLI-SHL'), 4, 55000, 43000, 0],
            [$prod('SPR-BSI'), 4, 35000, 24000, 0],
            [$prod('SPR-FLT'), 4, 45000, 32000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 10 — Toko Peralatan Dapur ───
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $u = $ownerUser('toko-10');

        $s21 = $this->sale($t->id, $u->id, $cust($t->id, 'Catering Bu Lastri')->id, 'INV-T10-20260313-001', '2026-03-13', 'cash', 580000, 0, 580000, 580000, 0, 'paid', null, $now);
        $this->saleItems($s21, [
            [$prod('MSK-WJN'), 2, 185000, 125000, 0],
            [$prod('MSK-SPT'), 2, 45000, 28000, 0],
        ], $t->id, $u->id, $now);

        $s22 = $this->sale($t->id, $u->id, $cust($t->id, 'Hotel Bintang Timur')->id, 'INV-T10-20260320-001', '2026-03-20', 'transfer', 1585000, 0, 1585000, 0, 0, 'unpaid', 'Termin 14 hari', $now);
        $this->saleItems($s22, [
            [$prod('MSK-PRS'), 2, 350000, 240000, 0],
            [$prod('MKN-PLR'), 3, 95000, 65000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 11 — Koperasi Makmur Bersama ───
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $u = $ownerUser('toko-11');

        $s23 = $this->sale($t->id, $u->id, $cust($t->id, 'Anggota Koperasi 001')->id, 'INV-T11-20260308-001', '2026-03-08', 'cash', 138500, 0, 138500, 140000, 1500, 'paid', null, $now);
        $this->saleItems($s23, [
            [$prod('KOP-BRS'), 1, 78000, 65000, 0],
            [$prod('KOP-GUL'), 2, 16500, 13500, 0],
            [$prod('KOP-BUK'), 2, 12000, 8500, 0],
        ], $t->id, $u->id, $now);

        $s24 = $this->sale($t->id, $u->id, $cust($t->id, 'Warung Pak Trimo')->id, 'INV-T11-20260318-001', '2026-03-18', 'cash', 174000, 0, 174000, 200000, 26000, 'paid', null, $now);
        $this->saleItems($s24, [
            [$prod('KOP-MNY'), 3, 32000, 26000, 0],
            [$prod('KOP-EMB'), 2, 28000, 18000, 0],
            [$prod('KOP-PLN'), 4, 7000, 4500, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 12 — Toko Olahraga Sporty ───
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $u = $ownerUser('toko-12');

        $s25 = $this->sale($t->id, $u->id, $cust($t->id, 'Eko Budianto')->id, 'INV-T12-20260311-001', '2026-03-11', 'cash', 645000, 0, 645000, 650000, 5000, 'paid', null, $now);
        $this->saleItems($s25, [
            [$prod('SPT-JRS'), 2, 155000, 95000, 0],
            [$prod('SPT-MAT'), 2, 165000, 115000, 0],
        ], $t->id, $u->id, $now);

        $s26 = $this->sale($t->id, $u->id, $cust($t->id, 'Klub Futsal Garuda')->id, 'INV-T12-20260319-001', '2026-03-19', 'transfer', 2480000, 0, 2480000, 0, 0, 'unpaid', 'Termin 7 hari', $now);
        $this->saleItems($s26, [
            [$prod('SPT-JRS'), 10, 155000, 95000, 0],
            [$prod('SPT-CLT'), 8, 125000, 78000, 0],
        ], $t->id, $u->id, $now);

        // ─── TOKO 13 — Percetakan Digital Cepat ───
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $u = $ownerUser('toko-13');

        $s27 = $this->sale($t->id, $u->id, $cust($t->id, 'Rudi Santoso')->id, 'INV-T13-20260312-001', '2026-03-12', 'cash', 195000, 0, 195000, 200000, 5000, 'paid', null, $now);
        $this->saleItems($s27, [
            [$prod('PCT-KNM'), 2, 45000, 25000, 0],
            [$prod('SOU-MUG'), 1, 55000, 35000, 0],
        ], $t->id, $u->id, $now);

        $s28 = $this->sale($t->id, $u->id, $cust($t->id, 'PT Cahaya Media Utama')->id, 'INV-T13-20260318-001', '2026-03-18', 'transfer', 1450000, 0, 1450000, 0, 0, 'unpaid', 'Klien korporat net 30', $now);
        $this->saleItems($s28, [
            [$prod('PCT-BRS'), 3, 150000, 90000, 0],
            [$prod('PCT-SPD'), 5, 85000, 50000, 0],
            [$prod('SOU-KOS'), 5, 95000, 60000, 0],
        ], $t->id, $u->id, $now);
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────

    private function sale(
        int $tenantId,
        int $userId,
        ?int $customerId,
        string $invoice,
        string $date,
        string $method,
        float $subtotal,
        float $discount,
        float $total,
        float $paid,
        float $change,
        string $status,
        ?string $notes,
        $now
    ): int {
        return DB::table('sales')->insertGetId([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'customer_id' => $customerId,
            'invoice_number' => $invoice,
            'sale_date' => $date,
            'payment_method' => $method,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => 0,
            'total' => $total,
            'paid' => $paid,
            'change' => $change,
            'status' => $status,
            'notes' => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /** @param array<array{0:object,1:int,2:float,3:float,4:float}> $items */
    private function saleItems(int $saleId, array $items, int $tenantId, int $userId, $now): void
    {
        $rows = [];
        foreach ($items as [$prod, $qty, $price, $costPrice, $discount]) {
            $rows[] = [
                'sale_id' => $saleId,
                'product_id' => $prod->id,
                'product_name' => $prod->name,
                'qty' => $qty,
                'price' => $price,
                'cost_price' => $costPrice,
                'discount' => $discount,
                'subtotal' => ($price - $discount) * $qty,
            ];
            $this->stockOut($tenantId, $userId, $prod->id, 'sale', $saleId, $qty, $now);
        }
        DB::table('sale_items')->insert($rows);
    }

    private function stockOut(int $tenantId, int $userId, int $productId, string $refType, int $refId, int $qty, $now): void
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
            'notes' => null,
            'created_at' => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}