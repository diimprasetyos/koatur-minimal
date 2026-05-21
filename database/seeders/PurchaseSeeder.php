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

        // ── PO 1 — Indofood, sembako, lunas ──
        $po1 = $this->purchase($t->id, $u->id, $sup('SUP-001')->id,
            'PO-T1-20260305-001', 'INV-IDF-20260305', '2026-03-05', null,
            1120000, 0, 0, 1120000, 1120000, 0,
            'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po1, [
            [$prod('IMG-001'), 200, 200, 2800],
            [$prod('AQU-001'), 150, 150, 3000],
        ], $t->id, $u->id, $now);

        // ── PO 2 — CV Elektro Jaya, charger, bayar sebagian ──
        $po2 = $this->purchase($t->id, $u->id, $sup('SUP-002')->id,
            'PO-T1-20260308-001', null, '2026-03-08', '2026-04-08',
            950000, 0, 0, 950000, 600000, 350000,
            'received', 'partial', 'transfer', 'Sisa hutang dibayar bulan depan', $now);
        $this->purchaseItems($po2, [
            [$prod('CHG-065'), 10, 10, 95000],
        ], $t->id, $u->id, $now);

        // ── PO 3 — Distro Pakaian, kaos, lunas ──
        $po3 = $this->purchase($t->id, $u->id, $sup('SUP-003')->id,
            'PO-T1-20260312-001', 'DIST-INV-3201', '2026-03-12', null,
            3200000, 200000, 0, 3000000, 3000000, 0,
            'received', 'paid', 'transfer', null, $now);
        $this->purchaseItems($po3, [
            [$prod('KPS-W-L'), 40, 40, 40000],
            [$prod('KPS-B-M'), 40, 40, 40000],
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
