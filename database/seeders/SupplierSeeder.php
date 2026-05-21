<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();

        $suppliers = [];
        foreach ($this->data() as $r) {
            $suppliers[] = array_merge([
                'uuid'           => Str::uuid(),
                'tenant_id'      => $t->id,
                'email'          => null,
                'contact_person' => null,
                'payable_amount' => 0,
                'is_active'      => true,
                'notes'          => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ], $r);
        }

        DB::table('suppliers')->insert($suppliers);
    }

    private function data(): array
    {
        return [
            ['name' => 'PT Indofood CBP',         'code' => 'SUP-001', 'phone' => '021-5550001', 'email' => 'sales@indofood.co.id', 'address' => 'Jl. Jend. Sudirman, Jakarta',          'contact_person' => 'Pak Slamet'],
            ['name' => 'CV Elektro Jaya',          'code' => 'SUP-002', 'phone' => '031-3334455', 'email' => 'order@elektrojaya.com', 'address' => 'Jl. Kapas Krampung No. 8, Surabaya', 'contact_person' => 'Pak Joko', 'payable_amount' => 350000, 'notes' => 'Min. order Rp 500.000'],
            ['name' => 'Distro Pakaian Surabaya',  'code' => 'SUP-003', 'phone' => '085123456789', 'address' => 'Jl. Tunjungan No. 15, Surabaya',                                          'contact_person' => 'Bu Yanti'],
            ['name' => 'Grosir ATK Murah',         'code' => 'SUP-004', 'phone' => '081234509876', 'email' => 'atk@grosirmurah.id', 'address' => 'Jl. Blauran No. 30, Surabaya'],
            ['name' => 'UD Tirta Segar',           'code' => 'SUP-005', 'phone' => '031-7779900', 'address' => 'Jl. Kalibokor No. 4, Surabaya',                                          'is_active' => false, 'notes' => 'Supplier tidak aktif'],
        ];
    }
}
