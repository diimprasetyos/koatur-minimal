<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $prod = fn(string $sku) => DB::table('products')->where('sku', $sku)->first();

        $adjustments = [
            [
                'ref'    => 'ADJ-T1-20260325-001',
                'date'   => '2026-03-25',
                'notes'  => 'Stock opname 25 Maret 2026',
                'status' => 'confirmed',
                'items'  => [
                    ['sku' => 'IMG-001', 'adj' => -3,  'reason' => 'Selisih stock opname — 3 pcs tidak ditemukan'],
                    ['sku' => 'AQU-001', 'adj' => -5,  'reason' => 'Botol retak / tidak layak jual'],
                ],
            ],
            [
                'ref'    => 'ADJ-T1-20260326-001',
                'date'   => '2026-03-26',
                'notes'  => 'Temuan stok lama di gudang',
                'status' => 'confirmed',
                'items'  => [
                    ['sku' => 'BUK-058', 'adj' => +10, 'reason' => 'Stok lama ditemukan di gudang belakang'],
                ],
            ],
        ];

        foreach ($adjustments as $a) {
            // 1. Insert ke stock_adjustments (header saja, tanpa product_id)
            $adjId = DB::table('stock_adjustments')->insertGetId([
                'uuid'             => Str::uuid(),
                'tenant_id'        => $t->id,
                'user_id'          => $u->id,
                'reference_number' => $a['ref'],
                'adjustment_date'  => $a['date'],
                'status'           => $a['status'],
                'notes'            => $a['notes'],
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // 2. Insert tiap produk ke stock_adjustment_items
            foreach ($a['items'] as $item) {
                $product     = $prod($item['sku']);
                $stockBefore = $product->stock;
                $stockAfter  = max(0, $stockBefore + $item['adj']);
                $qtyDiff     = $stockAfter - $stockBefore;
                $type        = match (true) {
                    $qtyDiff > 0 => 'add',
                    $qtyDiff < 0 => 'subtract',
                    default      => 'set',
                };
                $movementType = $qtyDiff >= 0 ? 'in' : 'out';

                DB::table('stock_adjustment_items')->insert([
                    'stock_adjustment_id' => $adjId,
                    'product_id'          => $product->id,
                    'stock_before'        => $stockBefore,
                    'stock_after'         => $stockAfter,
                    'qty_difference'      => $qtyDiff,
                    'type'                => $type,
                    'notes'               => $item['reason'],
                ]);

                // 3. Catat ke stock_movements
                DB::table('stock_movements')->insert([
                    'tenant_id'      => $t->id,
                    'product_id'     => $product->id,
                    'user_id'        => $u->id,
                    'reference_type' => 'adjustment',
                    'reference_id'   => $adjId,
                    'type'           => $movementType,
                    'qty'            => abs($qtyDiff),
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockAfter,
                    'notes'          => $item['reason'],
                    'created_at'     => $now,
                ]);

                // 4. Update stok produk
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['stock' => $stockAfter]);
            }
        }
    }
}
