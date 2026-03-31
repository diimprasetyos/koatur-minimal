<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $categories = [
            // Tenant 1
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Makanan & Minuman', 'color' => '#f59e0b', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Elektronik', 'color' => '#3b82f6', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Pakaian', 'color' => '#ec4899', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Perlengkapan Rumah', 'color' => '#10b981', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Alat Tulis', 'color' => '#8b5cf6', 'is_active' => true],

            // Tenant 2
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Sembako', 'color' => '#f97316', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Minuman', 'color' => '#06b6d4', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Snack & Camilan', 'color' => '#eab308', 'is_active' => true],

            // Tenant 3
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'Bahan Baku', 'color' => '#64748b', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'Produk Jadi', 'color' => '#22c55e', 'is_active' => true],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'Spare Part', 'color' => '#ef4444', 'is_active' => false],
        ];

        $now = now();
        foreach ($categories as &$cat) {
            $cat['created_at'] = $now;
            $cat['updated_at'] = $now;
        }

        DB::table('categories')->insert($categories);
    }
}