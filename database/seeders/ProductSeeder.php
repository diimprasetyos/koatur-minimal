<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        // Ambil kategori per tenant
        $catMakmin = DB::table('categories')->where('tenant_id', $tenant1->id)->where('name', 'Makanan & Minuman')->first();
        $catElektro = DB::table('categories')->where('tenant_id', $tenant1->id)->where('name', 'Elektronik')->first();
        $catPakaian = DB::table('categories')->where('tenant_id', $tenant1->id)->where('name', 'Pakaian')->first();
        $catRumah = DB::table('categories')->where('tenant_id', $tenant1->id)->where('name', 'Perlengkapan Rumah')->first();
        $catAtk = DB::table('categories')->where('tenant_id', $tenant1->id)->where('name', 'Alat Tulis')->first();

        $catSembako = DB::table('categories')->where('tenant_id', $tenant2->id)->where('name', 'Sembako')->first();
        $catMinuman = DB::table('categories')->where('tenant_id', $tenant2->id)->where('name', 'Minuman')->first();
        $catSnack = DB::table('categories')->where('tenant_id', $tenant2->id)->where('name', 'Snack & Camilan')->first();

        $catBahan = DB::table('categories')->where('tenant_id', $tenant3->id)->where('name', 'Bahan Baku')->first();
        $catProduk = DB::table('categories')->where('tenant_id', $tenant3->id)->where('name', 'Produk Jadi')->first();

        $now = now();

        $products = [
            // === Tenant 1 ===
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catMakmin->id, 'name' => 'Indomie Goreng', 'sku' => 'IMG-001', 'price' => 3500, 'cost_price' => 2800, 'stock' => 200, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catMakmin->id, 'name' => 'Aqua 600ml', 'sku' => 'AQU-001', 'price' => 4000, 'cost_price' => 3000, 'stock' => 150, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catElektro->id, 'name' => 'Charger USB-C 65W', 'sku' => 'CHG-065', 'price' => 150000, 'cost_price' => 95000, 'stock' => 30, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catElektro->id, 'name' => 'Kabel HDMI 2m', 'sku' => 'HDM-002', 'price' => 75000, 'cost_price' => 45000, 'stock' => 25, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catPakaian->id, 'name' => 'Kaos Polos Putih L', 'sku' => 'KPS-W-L', 'price' => 65000, 'cost_price' => 40000, 'stock' => 50, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catPakaian->id, 'name' => 'Kaos Polos Hitam M', 'sku' => 'KPS-B-M', 'price' => 65000, 'cost_price' => 40000, 'stock' => 45, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catRumah->id, 'name' => 'Sapu Lantai', 'sku' => 'SAP-001', 'price' => 35000, 'cost_price' => 22000, 'stock' => 20, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catAtk->id, 'name' => 'Pulpen Hitam (1 pak)', 'sku' => 'PEN-BLK', 'price' => 25000, 'cost_price' => 15000, 'stock' => 100, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'category_id' => $catAtk->id, 'name' => 'Buku Tulis 58 lembar', 'sku' => 'BUK-058', 'price' => 7500, 'cost_price' => 5000, 'stock' => 200, 'track_stock' => true, 'is_active' => true],

            // === Tenant 2 ===
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'category_id' => $catSembako->id, 'name' => 'Beras Premium 5kg', 'sku' => 'BRS-5KG', 'price' => 75000, 'cost_price' => 62000, 'stock' => 80, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'category_id' => $catSembako->id, 'name' => 'Minyak Goreng 1L', 'sku' => 'MNY-1LT', 'price' => 18000, 'cost_price' => 14500, 'stock' => 60, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'category_id' => $catSembako->id, 'name' => 'Gula Pasir 1kg', 'sku' => 'GUL-1KG', 'price' => 16000, 'cost_price' => 13000, 'stock' => 70, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'category_id' => $catMinuman->id, 'name' => 'Teh Botol 350ml', 'sku' => 'TEH-350', 'price' => 6000, 'cost_price' => 4500, 'stock' => 120, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'category_id' => $catSnack->id, 'name' => 'Chitato 68g', 'sku' => 'CHT-068', 'price' => 12000, 'cost_price' => 9000, 'stock' => 90, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'category_id' => $catSnack->id, 'name' => 'Oreo Original 137g', 'sku' => 'ORE-137', 'price' => 15000, 'cost_price' => 11500, 'stock' => 75, 'track_stock' => true, 'is_active' => true],

            // === Tenant 3 ===
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'category_id' => $catBahan->id, 'name' => 'Besi Hollow 4x4 6m', 'sku' => 'BSH-446', 'price' => 185000, 'cost_price' => 145000, 'stock' => 40, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'category_id' => $catBahan->id, 'name' => 'Cat Tembok 25kg', 'sku' => 'CAT-25K', 'price' => 420000, 'cost_price' => 340000, 'stock' => 15, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'category_id' => $catProduk->id, 'name' => 'Kusen Pintu Aluminium', 'sku' => 'KSP-ALU', 'price' => 850000, 'cost_price' => 600000, 'stock' => 10, 'track_stock' => true, 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'category_id' => $catProduk->id, 'name' => 'Pintu Besi Minimalis', 'sku' => 'PIN-BSI', 'price' => 1250000, 'cost_price' => 900000, 'stock' => 5, 'track_stock' => true, 'is_active' => true],
        ];

        $now = now();
        foreach ($products as &$p) {
            $p['description'] = null;
            $p['image'] = null;
            $p['created_at'] = $now;
            $p['updated_at'] = $now;
        }

        DB::table('products')->insert($products);
    }
}