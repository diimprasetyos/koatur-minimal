<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();

        $categories = [];
        foreach ($this->data() as [$name, $color]) {
            $categories[] = [
                'tenant_id'  => $t->id,
                'name'       => $name,
                'color'      => $color,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('expense_categories')->insert($categories);
    }

    private function data(): array
    {
        return [
            ['Gaji & Upah',        '#6366f1'],
            ['Sewa & Utilitas',    '#f59e0b'],
            ['Transportasi',       '#10b981'],
            ['Perlengkapan Toko',  '#3b82f6'],
            ['Marketing & Promosi','#ec4899'],
            ['Lain-lain',          '#94a3b8'],
        ];
    }
}
