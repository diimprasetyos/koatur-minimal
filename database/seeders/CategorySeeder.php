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
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();

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
            ['Smartphone & Tablet', '#3b82f6'],
            ['Laptop & Komputer',   '#6366f1'],
            ['Aksesoris HP',        '#f59e0b'],
            ['Audio & Speaker',     '#10b981'],
            ['Kabel & Charger',     '#ec4899'],
        ];
    }
}
