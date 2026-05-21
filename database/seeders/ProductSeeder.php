<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();

        $cat = fn(string $name) => DB::table('categories')
            ->where('tenant_id', $t->id)
            ->where('name', $name)
            ->value('id');

        $products = [];
        $add = function (int $catId, array $rows) use (&$products, $t, $now) {
            foreach ($rows as $r) {
                $products[] = array_merge([
                    'uuid'        => Str::uuid(),
                    'tenant_id'   => $t->id,
                    'category_id' => $catId,
                    'description' => null,
                    'image'       => null,
                    'track_stock' => true,
                    'is_active'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ], $r);
            }
        };

        $add($cat('Makanan & Minuman'), [
            ['name' => 'Indomie Goreng',   'sku' => 'IMG-001', 'price' => 3500,  'cost_price' => 2800,  'stock' => 200],
            ['name' => 'Aqua 600ml',       'sku' => 'AQU-001', 'price' => 4000,  'cost_price' => 3000,  'stock' => 150],
            ['name' => 'Teh Botol Sosro',  'sku' => 'TBS-001', 'price' => 5000,  'cost_price' => 3800,  'stock' => 100],
        ]);

        $add($cat('Elektronik'), [
            ['name' => 'Charger USB-C 65W', 'sku' => 'CHG-065', 'price' => 150000, 'cost_price' => 95000, 'stock' => 30],
            ['name' => 'Kabel HDMI 2m',     'sku' => 'HDM-002', 'price' => 75000,  'cost_price' => 45000, 'stock' => 25],
        ]);

        $add($cat('Pakaian'), [
            ['name' => 'Kaos Polos Putih L', 'sku' => 'KPS-W-L', 'price' => 65000, 'cost_price' => 40000, 'stock' => 50],
            ['name' => 'Kaos Polos Hitam M', 'sku' => 'KPS-B-M', 'price' => 65000, 'cost_price' => 40000, 'stock' => 45],
        ]);

        $add($cat('Perlengkapan Rumah'), [
            ['name' => 'Sapu Lantai', 'sku' => 'SAP-001', 'price' => 35000, 'cost_price' => 22000, 'stock' => 20],
        ]);

        $add($cat('Alat Tulis'), [
            ['name' => 'Pulpen Hitam (1 pak)',  'sku' => 'PEN-BLK', 'price' => 25000, 'cost_price' => 15000, 'stock' => 100],
            ['name' => 'Buku Tulis 58 lembar', 'sku' => 'BUK-058', 'price' => 7500,  'cost_price' => 5000,  'stock' => 200],
        ]);

        DB::table('products')->insert($products);
    }
}
