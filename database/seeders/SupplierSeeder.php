<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $now = now();

        $suppliers = [
            // Tenant 1
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'PT Indofood CBP', 'code' => 'SUP-001', 'phone' => '021-5550001', 'email' => 'sales@indofood.co.id', 'address' => 'Jl. Jend. Sudirman, Jakarta', 'contact_person' => 'Pak Slamet', 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'CV Elektro Jaya', 'code' => 'SUP-002', 'phone' => '031-3334455', 'email' => 'order@elektrojaya.com', 'address' => 'Jl. Kapas Krampung No. 8, SBY', 'contact_person' => 'Pak Joko', 'payable_amount' => 350000, 'is_active' => true, 'notes' => 'Min. order Rp 500.000'],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Distro Pakaian Surabaya', 'code' => 'SUP-003', 'phone' => '085123456789', 'email' => null, 'address' => 'Jl. Tunjungan No. 15, Surabaya', 'contact_person' => 'Bu Yanti', 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Grosir ATK Murah', 'code' => 'SUP-004', 'phone' => '081234509876', 'email' => 'atk@grosirmurah.id', 'address' => 'Jl. Blauran No. 30, Surabaya', 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'UD Tirta Segar', 'code' => 'SUP-005', 'phone' => '031-7779900', 'email' => null, 'address' => 'Jl. Kalibokor No. 4, Surabaya', 'contact_person' => null, 'payable_amount' => 0, 'is_active' => false, 'notes' => 'Supplier tidak aktif'],

            // Tenant 2
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'PT Bogasari', 'code' => 'S-001', 'phone' => '021-6660011', 'email' => null, 'address' => 'Jl. Raya Tanjung Priok, Jakarta', 'contact_person' => 'Pak Hasan', 'payable_amount' => 500000, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Distributor Coca-Cola SBY', 'code' => 'S-002', 'phone' => '031-4445566', 'email' => 'order@cc-sby.co.id', 'address' => 'Jl. Margomulyo No. 8, Surabaya', 'contact_person' => 'Pak Ari', 'payable_amount' => 0, 'is_active' => true, 'notes' => 'Pengiriman setiap Selasa'],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'UD Sembako Makmur', 'code' => 'S-003', 'phone' => '086543210987', 'email' => null, 'address' => 'Pasar Larangan, Sidoarjo', 'contact_person' => 'Bu Mimi', 'payable_amount' => 125000, 'is_active' => true, 'notes' => null],

            // Tenant 3
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'PT Krakatau Steel', 'code' => 'VND-001', 'phone' => '0254-392000', 'email' => 'sales@krakatausteel.id', 'address' => 'Jl. Industri No. 1, Cilegon', 'contact_person' => 'Pak Warno', 'payable_amount' => 7500000, 'is_active' => true, 'notes' => 'Kontrak tahunan'],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'Toko Cat Indah', 'code' => 'VND-002', 'phone' => '031-5557788', 'email' => null, 'address' => 'Jl. Raya Waru No. 45, Sidoarjo', 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
        ];

        foreach ($suppliers as &$s) {
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
        }

        DB::table('suppliers')->insert($suppliers);
    }
}