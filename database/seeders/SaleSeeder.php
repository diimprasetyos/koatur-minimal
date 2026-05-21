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

        // ── Penjualan 1 — tunai, Agus, sembako ──
        $s1 = $this->sale(
            $t->id,
            $u->id,
            $cust('Agus Prasetyo')->id,
            'INV-T1-20260310-001',
            '2026-03-10',
            'cash',
            37500,
            0,
            37500,
            40000,
            2500,
            'paid',
            null,
            $now
        );
        $this->saleItems($s1, [
            [$prod('IMG-001'), 5, 3500, 2800, 0],
            [$prod('AQU-001'), 5, 4000, 3000, 0],
        ], $t->id, $u->id, $now);

        // ── Penjualan 2 — QRIS, Dewi, elektronik + pakaian, diskon member ──
        $s2 = $this->sale(
            $t->id,
            $u->id,
            $cust('Dewi Rahayu')->id,
            'INV-T1-20260315-001',
            '2026-03-15',
            'qris',
            215000,
            15000,
            200000,
            200000,
            0,
            'paid',
            'Diskon member 7%',
            $now
        );
        $this->saleItems($s2, [
            [$prod('CHG-065'), 1, 150000, 95000, 10000],
            [$prod('KPS-W-L'), 1, 65000,  40000, 5000],
        ], $t->id, $u->id, $now);

        // ── Penjualan 3 — transfer, Toko Sinar Mas, ATK, bayar sebagian ──
        $s3 = $this->sale(
            $t->id,
            $u->id,
            $cust('Toko Sinar Mas')->id,
            'INV-T1-20260320-001',
            '2026-03-20',
            'transfer',
            400000,
            0,
            400000,
            250000,
            0,
            'partial',
            'Sisa tagihan Rp 150.000',
            $now
        );
        $this->saleItems($s3, [
            [$prod('PEN-BLK'), 10, 25000, 15000, 0],
            [$prod('BUK-058'), 20, 7500,  5000,  0],
        ], $t->id, $u->id, $now);

        // ── Penjualan 4 — tunai, walk-in, sembako ──
        $s4 = $this->sale(
            $t->id,
            $u->id,
            null,
            'INV-T1-20260322-001',
            '2026-03-22',
            'cash',
            17500,
            0,
            17500,
            20000,
            2500,
            'paid',
            null,
            $now
        );
        $this->saleItems($s4, [
            [$prod('IMG-001'), 3, 3500, 2800, 0],
            [$prod('AQU-001'), 2, 4000, 3000, 0],
        ], $t->id, $u->id, $now);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

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
