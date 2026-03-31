<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Toko Maju Jaya',
                'slug' => 'toko-maju-jaya',
                'phone' => '08123456789',
                'address' => 'Jl. Raya Darmo No. 10, Surabaya',
                'logo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Warung Berkah Abadi',
                'slug' => 'warung-berkah-abadi',
                'phone' => '08987654321',
                'address' => 'Jl. Pemuda No. 55, Sidoarjo',
                'logo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'CV Sumber Rejeki',
                'slug' => 'cv-sumber-rejeki',
                'phone' => '031-7654321',
                'address' => 'Jl. Kertajaya Indah No. 88, Surabaya',
                'logo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tenants')->insert($tenants);
    }
}