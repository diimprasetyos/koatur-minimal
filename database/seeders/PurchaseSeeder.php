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
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $prod = fn(string $sku)  => DB::table('products')->where('sku', $sku)->first();
        $sup  = fn(string $code) => DB::table('suppliers')
            ->where('tenant_id', $t->id)->where('code', $code)->first();

        // ── PO 1 — Distributor Samsung & Xiaomi, smartphone, lunas ──
        $po1 = $this->purchase($t->id, $u->id, $sup('SUP-001')->id,
            'PO-T1-20260305-001', 'INV-DSX-20260305', '2026-03-05', null,
            110000000, 0, 0, 110000000, 110000000, 0,
            'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po1, [
            [$prod('SGA-A35'),  15, 15, 3500000],
            [$prod('XMI-RN13'), 20, 20, 2000000],
            [$prod('RLM-C67'),  18, 18, 1500000],
        ], $t->id, $u->id, $now);

        // ── PO 2 — Distributor ASUS & Lenovo, laptop, bayar sebagian ──
        $po2 = $this->purchase($t->id, $u->id, $sup('SUP-002')->id,
            'PO-T1-20260308-001', null, '2026-03-08', '2026-04-08',
            123800000, 0, 0, 123800000, 80000000, 43800000,
            'received', 'partial', 'transfer', 'Sisa hutang dibayar akhir bulan', $now);
        $this->purchaseItems($po2, [
            [$prod('ASS-VB14'), 10, 10, 8500000],
            [$prod('LNV-IPS3'),  8,  8, 7200000],
            [$prod('ACR-AS3R'),  6,  6, 6600000],
        ], $t->id, $u->id, $now);

        // ── PO 3 — Distributor aksesoris & audio, lunas ──
        $po3 = $this->purchase($t->id, $u->id, $sup('SUP-003')->id,
            'PO-T1-20260312-001', 'INV-AKS-3312', '2026-03-12', null,
            22285000, 285000, 0, 22000000, 22000000, 0,
            'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po3, [
            [$prod('TGL-65U'),   200, 200, 8000],
            [$prod('CSE-IP15'),   80,  80, 35000],
            [$prod('RNG-MAG'),   120, 120, 15000],
            [$prod('PWB-BS20'),   30,  30, 250000],
            [$prod('TWS-ANK'),    25,  25, 210000],
            [$prod('SPK-JBL'),    15,  15, 450000],
            [$prod('HDP-SNY'),    12,  12, 620000],
        ], $t->id, $u->id, $now);

        // ── PO 4 — Distributor kabel & charger, lunas ──
        $po4 = $this->purchase($t->id, $u->id, $sup('SUP-003')->id,
            'PO-T1-20260314-001', 'INV-KBL-3314', '2026-03-14', null,
            17380000, 0, 0, 17380000, 17380000, 0,
            'received', 'paid', 'cash', null, $now);
        $this->purchaseItems($po4, [
            [$prod('CHG-G65'),  50,  50, 120000],
            [$prod('KBL-CC1'), 150, 150, 22000],
            [$prod('KBL-CL1'), 100, 100, 35000],
            [$prod('ADP-HDC'),  40,  40, 75000],
        ], $t->id, $u->id, $now);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function purchase(
        int $tenantId, int $userId, int $supplierId,
        string $ref, ?string $invoice, string $date, ?string $dueDate,
        float $subtotal, float $discount, float $tax,
        float $total, float $paid, float $due,
        string $status, string $paymentStatus, string $method,
        ?string $notes, $now
    ): int {
        return DB::table('purchases')->insertGetId([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $tenantId,
            'user_id'          => $userId,
            'supplier_id'      => $supplierId,
            'reference_number' => $ref,
            'supplier_invoice' => $invoice,
            'purchase_date'    => $date,
            'due_date'         => $dueDate,
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'tax'              => $tax,
            'total'            => $total,
            'paid'             => $paid,
            'due'              => $due,
            'status'           => $status,
            'payment_status'   => $paymentStatus,
            'payment_method'   => $method,
            'notes'            => $notes,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }

    private function purchaseItems(int $purchaseId, array $items, int $tenantId, int $userId, $now): void
    {
        $rows = [];
        foreach ($items as [$prod, $qty, $qtyReceived, $costPrice]) {
            $rows[] = [
                'purchase_id'  => $purchaseId,
                'product_id'   => $prod->id,
                'qty'          => $qty,
                'qty_received' => $qtyReceived,
                'cost_price'   => $costPrice,
                'subtotal'     => $qty * $costPrice,
            ];
            $this->stockIn($tenantId, $userId, $prod->id, 'purchase', $purchaseId, $qtyReceived, $now);
        }
        DB::table('purchase_items')->insert($rows);
    }

    private function stockIn(int $tenantId, int $userId, int $productId, string $refType, int $refId, int $qty, $now): void
    {
        $product     = DB::table('products')->find($productId);
        $stockBefore = $product->stock;
        $stockAfter  = $stockBefore + $qty;

        DB::table('stock_movements')->insert([
            'tenant_id'      => $tenantId,
            'product_id'     => $productId,
            'user_id'        => $userId,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'type'           => 'in',
            'qty'            => $qty,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'notes'          => null,
            'created_at'     => $now,
        ]);

        DB::table('products')->where('id', $productId)->update(['stock' => $stockAfter]);
    }
}
