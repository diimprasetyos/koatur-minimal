<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();

        $customers = [];
        foreach ($this->data() as $r) {
            $customers[] = array_merge([
                'uuid'           => Str::uuid(),
                'tenant_id'      => $t->id,
                'email'          => null,
                'address'        => null,
                'contact_person' => null,
                'is_active'      => true,
                'notes'          => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ], $r);
        }

        DB::table('customers')->insert($customers);
    }

    private function data(): array
    {
        return [
            ['name' => 'Rendra Kusuma',      'code' => 'CUST-001', 'phone' => '081234567890', 'email' => 'rendra@gmail.com',  'address' => 'Jl. Rungkut Industri No. 8, Surabaya'],
            ['name' => 'Sari Dewi',          'code' => 'CUST-002', 'phone' => '082345678901', 'email' => 'sari@yahoo.com',    'address' => 'Jl. Dharmahusada No. 14, Surabaya'],
            ['name' => 'CV Maju Bersama',    'code' => 'CUST-003', 'phone' => '031-7654321',  'email' => null,                'address' => 'Jl. Ngagel Jaya No. 33, Surabaya', 'contact_person' => 'Pak Hendra'],
            ['name' => 'Bagas Firmansyah',   'code' => 'CUST-004', 'phone' => '083456789012', 'email' => 'bagas@gmail.com',   'address' => 'Jl. Kenjeran No. 21, Surabaya'],
            ['name' => 'Linda Wulandari',    'code' => 'CUST-005', 'phone' => '084567890123', 'is_active' => false,           'notes' => 'Tidak aktif — pindah kota'],
        ];
    }
}
