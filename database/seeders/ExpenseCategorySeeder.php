<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $now = now();

        $categories = [
            // Tenant 1
            ['tenant_id' => $tenant1->id, 'name' => 'Gaji & Upah', 'color' => '#6366f1', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant1->id, 'name' => 'Sewa & Utilitas', 'color' => '#f59e0b', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant1->id, 'name' => 'Transportasi', 'color' => '#10b981', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant1->id, 'name' => 'Perlengkapan Toko', 'color' => '#3b82f6', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant1->id, 'name' => 'Marketing & Promosi', 'color' => '#ec4899', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant1->id, 'name' => 'Lain-lain', 'color' => '#94a3b8', 'created_at' => $now, 'updated_at' => $now],

            // Tenant 2
            ['tenant_id' => $tenant2->id, 'name' => 'Operasional', 'color' => '#f97316', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant2->id, 'name' => 'Listrik & Air', 'color' => '#06b6d4', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant2->id, 'name' => 'Tenaga Kerja', 'color' => '#8b5cf6', 'created_at' => $now, 'updated_at' => $now],

            // Tenant 3
            ['tenant_id' => $tenant3->id, 'name' => 'Overhead Pabrik', 'color' => '#64748b', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant3->id, 'name' => 'Perawatan Alat', 'color' => '#ef4444', 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => $tenant3->id, 'name' => 'Administrasi', 'color' => '#22c55e', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('expense_categories')->insert($categories);
    }
}