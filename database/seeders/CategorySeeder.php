<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();

        $categories = [];
        foreach ($this->data() as [$name, $color]) {
            $categories[] = [
                'uuid'       => Str::uuid(),
                'tenant_id'  => $t->id,
                'name'       => $name,
                'color'      => $color,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('categories')->insert($categories);
    }

    private function data(): array
    {
        return [
            ['Makanan & Minuman',   '#f59e0b'],
            ['Elektronik',          '#3b82f6'],
            ['Pakaian',             '#ec4899'],
            ['Perlengkapan Rumah',  '#10b981'],
            ['Alat Tulis',          '#8b5cf6'],
        ];
    }
}
