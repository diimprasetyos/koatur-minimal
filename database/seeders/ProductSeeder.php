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

        $add($cat('Smartphone & Tablet'), [
            ['name' => 'Samsung Galaxy A35 5G',  'sku' => 'SGA-A35',  'price' => 4199000,  'cost_price' => 3500000,  'stock' => 15],
            ['name' => 'Xiaomi Redmi Note 13',   'sku' => 'XMI-RN13', 'price' => 2499000,  'cost_price' => 2000000,  'stock' => 20],
            ['name' => 'Realme C67',             'sku' => 'RLM-C67',  'price' => 1899000,  'cost_price' => 1500000,  'stock' => 18],
            ['name' => 'iPad Mini 6 WiFi 64GB',  'sku' => 'APL-IPM6', 'price' => 8499000,  'cost_price' => 7200000,  'stock' => 8],
        ]);

        $add($cat('Laptop & Komputer'), [
            ['name' => 'ASUS VivoBook 14 i5',   'sku' => 'ASS-VB14', 'price' => 9999000,  'cost_price' => 8500000,  'stock' => 10],
            ['name' => 'Lenovo IdeaPad Slim 3', 'sku' => 'LNV-IPS3', 'price' => 8499000,  'cost_price' => 7200000,  'stock' => 8],
            ['name' => 'Acer Aspire 3 Ryzen 5', 'sku' => 'ACR-AS3R', 'price' => 7799000,  'cost_price' => 6600000,  'stock' => 6],
        ]);

        $add($cat('Aksesoris HP'), [
            ['name' => 'Tempered Glass Universal 6.5"', 'sku' => 'TGL-65U',  'price' => 25000,   'cost_price' => 8000,    'stock' => 200],
            ['name' => 'Case Silikon Anti Crack iPhone 15', 'sku' => 'CSE-IP15', 'price' => 75000, 'cost_price' => 35000,  'stock' => 80],
            ['name' => 'Ring Holder Magnetic 360°',   'sku' => 'RNG-MAG', 'price' => 35000,   'cost_price' => 15000,   'stock' => 120],
            ['name' => 'Power Bank Baseus 20000mAh',  'sku' => 'PWB-BS20', 'price' => 349000,  'cost_price' => 250000,  'stock' => 30],
        ]);

        $add($cat('Audio & Speaker'), [
            ['name' => 'TWS Earbuds Anker Q20i',      'sku' => 'TWS-ANK', 'price' => 299000,  'cost_price' => 210000,  'stock' => 25],
            ['name' => 'Speaker Bluetooth JBL Go 3',  'sku' => 'SPK-JBL', 'price' => 599000,  'cost_price' => 450000,  'stock' => 15],
            ['name' => 'Headphone Sony WH-CH520',     'sku' => 'HDP-SNY', 'price' => 799000,  'cost_price' => 620000,  'stock' => 12],
        ]);

        $add($cat('Kabel & Charger'), [
            ['name' => 'Charger GaN 65W USB-C',       'sku' => 'CHG-G65', 'price' => 189000,  'cost_price' => 120000,  'stock' => 50],
            ['name' => 'Kabel Data USB-C to USB-C 1m','sku' => 'KBL-CC1', 'price' => 49000,   'cost_price' => 22000,   'stock' => 150],
            ['name' => 'Kabel Data USB-C to Lightning','sku' => 'KBL-CL1','price' => 69000,   'cost_price' => 35000,   'stock' => 100],
            ['name' => 'Adaptor HDMI to USB-C',        'sku' => 'ADP-HDC', 'price' => 129000,  'cost_price' => 75000,   'stock' => 40],
        ]);

        DB::table('products')->insert($products);
    }
}
