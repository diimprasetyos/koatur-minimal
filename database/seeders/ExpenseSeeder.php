<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $user1 = DB::table('users')->where('email', 'admin@example.com')->first();
        $user2 = DB::table('users')->where('email', 'budi@example.com')->first();
        $user3 = DB::table('users')->where('email', 'siti@example.com')->first();
        $user4 = DB::table('users')->where('email', 'hendra@example.com')->first();

        // Kategori Tenant 1
        $catGaji = DB::table('expense_categories')->where('tenant_id', $tenant1->id)->where('name', 'Gaji & Upah')->first();
        $catSewa = DB::table('expense_categories')->where('tenant_id', $tenant1->id)->where('name', 'Sewa & Utilitas')->first();
        $catTransp = DB::table('expense_categories')->where('tenant_id', $tenant1->id)->where('name', 'Transportasi')->first();
        $catPerlkp = DB::table('expense_categories')->where('tenant_id', $tenant1->id)->where('name', 'Perlengkapan Toko')->first();
        $catLain = DB::table('expense_categories')->where('tenant_id', $tenant1->id)->where('name', 'Lain-lain')->first();

        // Kategori Tenant 2
        $catOps = DB::table('expense_categories')->where('tenant_id', $tenant2->id)->where('name', 'Operasional')->first();
        $catListrik = DB::table('expense_categories')->where('tenant_id', $tenant2->id)->where('name', 'Listrik & Air')->first();

        // Kategori Tenant 3
        $catOverhd = DB::table('expense_categories')->where('tenant_id', $tenant3->id)->where('name', 'Overhead Pabrik')->first();
        $catPerawt = DB::table('expense_categories')->where('tenant_id', $tenant3->id)->where('name', 'Perawatan Alat')->first();

        $now = now();

        $expenses = [
            // === Tenant 1 ===
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'user_id' => $user1->id, 'expense_category_id' => $catGaji->id, 'reference_number' => 'EXP-T1-001', 'title' => 'Gaji Karyawan Maret 2026', 'amount' => 3500000, 'expense_date' => '2026-03-31', 'payment_method' => 'transfer', 'notes' => null, 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'user_id' => $user1->id, 'expense_category_id' => $catSewa->id, 'reference_number' => 'EXP-T1-002', 'title' => 'Sewa Ruko Maret 2026', 'amount' => 2500000, 'expense_date' => '2026-03-01', 'payment_method' => 'transfer', 'notes' => 'Pembayaran bulanan', 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'user_id' => $user2->id, 'expense_category_id' => $catTransp->id, 'reference_number' => 'EXP-T1-003', 'title' => 'Bensin Antar Barang', 'amount' => 150000, 'expense_date' => '2026-03-10', 'payment_method' => 'cash', 'notes' => null, 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'user_id' => $user1->id, 'expense_category_id' => $catSewa->id, 'reference_number' => 'EXP-T1-004', 'title' => 'Tagihan Listrik Maret', 'amount' => 450000, 'expense_date' => '2026-03-20', 'payment_method' => 'transfer', 'notes' => null, 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'user_id' => $user2->id, 'expense_category_id' => $catPerlkp->id, 'reference_number' => 'EXP-T1-005', 'title' => 'Plastik & Kantong Belanja', 'amount' => 85000, 'expense_date' => '2026-03-15', 'payment_method' => 'cash', 'notes' => 'Beli di supplier lokal', 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'user_id' => $user1->id, 'expense_category_id' => $catLain->id, 'reference_number' => 'EXP-T1-006', 'title' => 'Perbaikan Etalase', 'amount' => 200000, 'expense_date' => '2026-03-25', 'payment_method' => 'cash', 'notes' => null, 'attachment' => null],

            // === Tenant 2 ===
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'user_id' => $user3->id, 'expense_category_id' => $catOps->id, 'reference_number' => 'EXP-T2-001', 'title' => 'Plastik Kresek Stok Bulan Ini', 'amount' => 60000, 'expense_date' => '2026-03-05', 'payment_method' => 'cash', 'notes' => null, 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'user_id' => $user3->id, 'expense_category_id' => $catListrik->id, 'reference_number' => 'EXP-T2-002', 'title' => 'Tagihan Listrik Maret', 'amount' => 280000, 'expense_date' => '2026-03-20', 'payment_method' => 'transfer', 'notes' => null, 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'user_id' => $user3->id, 'expense_category_id' => $catOps->id, 'reference_number' => 'EXP-T2-003', 'title' => 'Biaya Kebersihan Pasar', 'amount' => 50000, 'expense_date' => '2026-03-01', 'payment_method' => 'cash', 'notes' => 'Bayar iuran pasar', 'attachment' => null],

            // === Tenant 3 ===
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'user_id' => $user4->id, 'expense_category_id' => $catOverhd->id, 'reference_number' => 'EXP-T3-001', 'title' => 'Biaya Listrik Gudang Maret', 'amount' => 1200000, 'expense_date' => '2026-03-20', 'payment_method' => 'transfer', 'notes' => null, 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'user_id' => $user4->id, 'expense_category_id' => $catPerawt->id, 'reference_number' => 'EXP-T3-002', 'title' => 'Service Mesin Las', 'amount' => 350000, 'expense_date' => '2026-03-12', 'payment_method' => 'cash', 'notes' => 'Servis rutin 3 bulan sekali', 'attachment' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'user_id' => $user1->id, 'expense_category_id' => $catOverhd->id, 'reference_number' => 'EXP-T3-003', 'title' => 'Sewa Gudang Q1 2026', 'amount' => 4500000, 'expense_date' => '2026-03-01', 'payment_method' => 'transfer', 'notes' => 'Sewa 3 bulan Jan-Mar', 'attachment' => null],
        ];

        foreach ($expenses as &$e) {
            $e['created_at'] = $now;
            $e['updated_at'] = $now;
        }

        DB::table('expenses')->insert($expenses);
    }
}