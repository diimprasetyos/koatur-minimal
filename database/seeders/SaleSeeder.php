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
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $prod = fn(string $sku) => DB::table('products')->where('sku', $sku)->first();
        $cust = fn(string $name) => DB::table('customers')
            ->where('tenant_id', $t->id)->where('name', $name)->first();

        // ── Penjualan 1 — tunai, Rendra, beli smartphone + aksesoris ──
        $s1 = $this->sale($t->id, $u->id, $cust('Rendra Kusuma')->id,
            'INV-T1-20260310-001', '2026-03-10', 'cash',
            4324000, 100000, 4224000, 4500000, 276000,
            'paid', null, $now);
        $this->saleItems($s1, [
            [$prod('XMI-RN13'), 1, 2499000, 2000000, 0],
            [$prod('TGL-65U'),  2, 25000,   8000,    0],
            [$prod('KBL-CC1'),  3, 49000,   22000,   0],
        ], $t->id, $u->id, $now);

        // ── Penjualan 2 — transfer, Sari, beli laptop + charger, diskon member ──
        $s2 = $this->sale($t->id, $u->id, $cust('Sari Dewi')->id,
            'INV-T1-20260315-001', '2026-03-15', 'transfer',
            10188000, 488000, 9700000, 9700000, 0,
            'paid', 'Diskon member 5%', $now);
        $this->saleItems($s2, [
            [$prod('ASS-VB14'), 1, 9999000, 8500000, 488000],
            [$prod('CHG-G65'),  1, 189000,  120000,  0],
        ], $t->id, $u->id, $now);

        // ── Penjualan 3 — transfer, CV Maju Bersama, pembelian massal aksesoris, bayar sebagian ──
        $s3 = $this->sale($t->id, $u->id, $cust('CV Maju Bersama')->id,
            'INV-T1-20260320-001', '2026-03-20', 'transfer',
            5735000, 0, 5735000, 3000000, 0,
            'pending', 'Sisa tagihan Rp 2.735.000 — NET 30', $now);
        $this->saleItems($s3, [
            [$prod('PWB-BS20'), 5, 349000, 250000, 0],
            [$prod('TWS-ANK'),  5, 299000, 210000, 0],
            [$prod('KBL-CL1'), 10, 69000,  35000,  0],
            [$prod('RNG-MAG'), 10, 35000,  15000,  0],
        ], $t->id, $u->id, $now);

        // ── Penjualan 4 — QRIS, walk-in, earbuds + case ──
        $s4 = $this->sale($t->id, $u->id, null,
            'INV-T1-20260322-001', '2026-03-22', 'ewallet',
            374000, 0, 374000, 374000, 0,
            'paid', null, $now);
        $this->saleItems($s4, [
            [$prod('TWS-ANK'), 1, 299000, 210000, 0],
            [$prod('CSE-IP15'), 1, 75000, 35000,  0],
        ], $t->id, $u->id, $now);

        // ── Penjualan 5 — tunai, Bagas, beli tablet + headphone ──
        $s5 = $this->sale($t->id, $u->id, $cust('Bagas Firmansyah')->id,
            'INV-T1-20260325-001', '2026-03-25', 'cash',
            9298000, 0, 9298000, 10000000, 702000,
            'paid', null, $now);
        $this->saleItems($s5, [
            [$prod('APL-IPM6'), 1, 8499000, 7200000, 0],
            [$prod('HDP-SNY'),  1, 799000,  620000,  0],
        ], $t->id, $u->id, $now);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function sale(
        int $tenantId, int $userId, ?int $customerId,
        string $invoice, string $date, string $method,
        float $subtotal, float $discount, float $total,
        float $paid, float $change, string $status,
        ?string $notes, $now
    ): int {
        return DB::table('sales')->insertGetId([
            'uuid'           => Str::uuid(),
            'tenant_id'      => $tenantId,
            'user_id'        => $userId,
            'customer_id'    => $customerId,
            'invoice_number' => $invoice,
            'sale_date'      => $date,
            'payment_method' => $method,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => 0,
            'total'          => $total,
            'paid'           => $paid,
            'change'         => $change,
            'status'         => $status,
            'notes'          => $notes,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
    }

    private function saleItems(int $saleId, array $items, int $tenantId, int $userId, $now): void
    {
        $rows = [];
        foreach ($items as [$prod, $qty, $price, $costPrice, $discount]) {
            $rows[] = [
                'sale_id'      => $saleId,
                'product_id'   => $prod->id,
                'product_name' => $prod->name,
                'qty'          => $qty,
                'price'        => $price,
                'cost_price'   => $costPrice,
                'discount'     => $discount,
                'subtotal'     => ($price - $discount) * $qty,
            ];
            $this->stockOut($tenantId, $userId, $prod->id, 'sale', $saleId, $qty, $now);
        }
        DB::table('sale_items')->insert($rows);
    }

    private function stockOut(int $tenantId, int $userId, int $productId, string $refType, int $refId, int $qty, $now): void
    {
        $product     = DB::table('products')->find($productId);
        $stockBefore = $product->stock;
        $stockAfter  = max(0, $stockBefore - $qty);

        DB::table('stock_movements')->insert([
            'tenant_id'      => $tenantId,
            'product_id'     => $productId,
            'user_id'        => $userId,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'type'           => 'out',
            'qty'            => $qty,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'notes'          => null,
            'created_at'     => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}
