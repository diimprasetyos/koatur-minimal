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
            ['name' => 'Agus Prasetyo',  'code' => 'CUST-001', 'phone' => '081234567890', 'email' => 'agus@gmail.com', 'address' => 'Jl. Rungkut No. 12, Surabaya'],
            ['name' => 'Dewi Rahayu',    'code' => 'CUST-002', 'phone' => '082345678901', 'email' => 'dewi@yahoo.com', 'address' => 'Jl. Gubeng No. 7, Surabaya'],
            ['name' => 'Toko Sinar Mas', 'code' => 'CUST-003', 'phone' => '031-5551234',                               'address' => 'Jl. Semolowaru No. 55, Surabaya', 'contact_person' => 'Pak Mas'],
            ['name' => 'Fitria Handayani','code' => 'CUST-004', 'phone' => '083456789012', 'email' => 'fitria@gmail.com', 'address' => 'Jl. Kenjeran No. 3, Surabaya'],
            ['name' => 'Yusuf Maulana',  'code' => 'CUST-005', 'phone' => '084567890123', 'is_active' => false, 'notes' => 'Tidak aktif'],
        ];
    }
}
