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
            ['uuid' => Str::uuid(), 'name' => 'Toko 2', 'slug' => 'toko-2', 'phone' => '031-5551002', 'address' => 'Jl. Ahmad Yani No. 5, Sidoarjo', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 3', 'slug' => 'toko-3', 'phone' => '031-5551003', 'address' => 'Jl. Industri No. 12, Gresik', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 4', 'slug' => 'toko-4', 'phone' => '031-5551004', 'address' => 'Jl. Darmo No. 20, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 5', 'slug' => 'toko-5', 'phone' => '031-5551005', 'address' => 'Jl. Margomulyo No. 8, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 6', 'slug' => 'toko-6', 'phone' => '031-5551006', 'address' => 'Jl. Kapas Krampung No. 33, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 7', 'slug' => 'toko-7', 'phone' => '031-5551007', 'address' => 'Jl. Tunjungan No. 99, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 8', 'slug' => 'toko-8', 'phone' => '031-5551008', 'address' => 'Jl. Gubeng No. 15, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 9', 'slug' => 'toko-9', 'phone' => '031-5551009', 'address' => 'Jl. Kenjeran No. 7, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 10', 'slug' => 'toko-10', 'phone' => '031-5551010', 'address' => 'Jl. Wonokromo No. 44, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 11', 'slug' => 'toko-11', 'phone' => '031-5551011', 'address' => 'Jl. Pahlawan No. 3, Mojokerto', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 12', 'slug' => 'toko-12', 'phone' => '031-5551012', 'address' => 'Jl. Pemuda No. 18, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['uuid' => Str::uuid(), 'name' => 'Toko 13', 'slug' => 'toko-13', 'phone' => '031-5551013', 'address' => 'Jl. Embong Malang No. 6, Surabaya', 'logo' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('tenants')->insert($tenants);
    }
}