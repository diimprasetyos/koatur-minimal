<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $tenants = [
            ['uuid' => Str::uuid(), 'name' => 'Toko 1', 'slug' => 'toko-1', 'phone' => '031-5551001', 'address' => 'Jl. Rungkut No. 1, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('tenants')->insert($tenants);
    }
}
