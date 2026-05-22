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
        ];
    }
}
