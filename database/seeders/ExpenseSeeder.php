<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $cat = fn(string $name) => DB::table('expense_categories')
            ->where('tenant_id', $t->id)
            ->where('name', $name)
            ->value('id');

        $expenses = [];
        foreach ($this->data($cat) as $r) {
            $expenses[] = array_merge([
                'uuid'       => Str::uuid(),
                'tenant_id'  => $t->id,
                'user_id'    => $u->id,
                'attachment' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $r);
        }

        DB::table('expenses')->insert($expenses);
    }

    private function data(callable $cat): array
    {
        return [
            ['expense_category_id' => $cat('Gaji & Upah'),       'reference_number' => 'EXP-T1-001', 'title' => 'Gaji Karyawan Maret 2026',    'amount' => 3500000, 'expense_date' => '2026-03-31', 'payment_method' => 'transfer', 'notes' => null],
            ['expense_category_id' => $cat('Sewa & Utilitas'),   'reference_number' => 'EXP-T1-002', 'title' => 'Sewa Ruko Maret 2026',         'amount' => 2500000, 'expense_date' => '2026-03-01', 'payment_method' => 'transfer', 'notes' => 'Pembayaran bulanan'],
            ['expense_category_id' => $cat('Transportasi'),      'reference_number' => 'EXP-T1-003', 'title' => 'Bensin Antar Barang',          'amount' => 150000,  'expense_date' => '2026-03-10', 'payment_method' => 'cash',     'notes' => null],
            ['expense_category_id' => $cat('Sewa & Utilitas'),   'reference_number' => 'EXP-T1-004', 'title' => 'Tagihan Listrik Maret',        'amount' => 450000,  'expense_date' => '2026-03-20', 'payment_method' => 'transfer', 'notes' => null],
            ['expense_category_id' => $cat('Perlengkapan Toko'), 'reference_number' => 'EXP-T1-005', 'title' => 'Plastik & Kantong Belanja',    'amount' => 85000,   'expense_date' => '2026-03-15', 'payment_method' => 'cash',     'notes' => 'Beli di supplier lokal'],
            ['expense_category_id' => $cat('Lain-lain'),         'reference_number' => 'EXP-T1-006', 'title' => 'Perbaikan Etalase',            'amount' => 200000,  'expense_date' => '2026-03-25', 'payment_method' => 'cash',     'notes' => null],
        ];
    }
}
