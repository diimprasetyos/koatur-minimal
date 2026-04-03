<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $categories = [];

        $add = function (int $tenantId, array $rows) use (&$categories, $now) {
            foreach ($rows as [$name, $color]) {
                $categories[] = ['tenant_id' => $tenantId, 'name' => $name, 'color' => $color, 'created_at' => $now, 'updated_at' => $now];
            }
        };

        // Toko 1
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $add($t->id, [
            ['Gaji & Upah', '#6366f1'],
            ['Sewa & Utilitas', '#f59e0b'],
            ['Transportasi', '#10b981'],
            ['Perlengkapan Toko', '#3b82f6'],
            ['Marketing & Promosi', '#ec4899'],
            ['Lain-lain', '#94a3b8'],
        ]);

        // Toko 2
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $add($t->id, [
            ['Operasional', '#f97316'],
            ['Listrik & Air', '#06b6d4'],
            ['Tenaga Kerja', '#8b5cf6'],
        ]);

        // Toko 3
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $add($t->id, [
            ['Overhead Pabrik', '#64748b'],
            ['Perawatan Alat', '#ef4444'],
            ['Administrasi', '#22c55e'],
        ]);

        // Toko 4
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $add($t->id, [
            ['Gaji Apoteker & Staff', '#6366f1'],
            ['Sewa & Listrik', '#f59e0b'],
            ['Pembelian Resep', '#10b981'],
            ['Peralatan Apotek', '#3b82f6'],
        ]);

        // Toko 5
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $add($t->id, [
            ['Gaji Karyawan', '#6366f1'],
            ['Sewa Gudang', '#f59e0b'],
            ['Transportasi Barang', '#10b981'],
            ['Listrik & Air', '#06b6d4'],
        ]);

        // Toko 6
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $add($t->id, [
            ['Gaji Teknisi', '#6366f1'],
            ['Sewa & Utilitas', '#f59e0b'],
            ['Iklan & Promosi', '#ec4899'],
            ['Perlengkapan Servis', '#3b82f6'],
        ]);

        // Toko 7
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $add($t->id, [
            ['Gaji Karyawan', '#6366f1'],
            ['Sewa Toko', '#f59e0b'],
            ['Display & Dekorasi', '#ec4899'],
            ['Sosial Media Ads', '#8b5cf6'],
        ]);

        // Toko 8
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $add($t->id, [
            ['Gaji Kasir & Staff', '#6366f1'],
            ['Listrik & Pendingin', '#06b6d4'],
            ['Sewa Ruko', '#f59e0b'],
            ['Kantong & Kemasan', '#10b981'],
        ]);

        // Toko 9
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $add($t->id, [
            ['Gaji Mekanik', '#6366f1'],
            ['Perawatan Peralatan', '#ef4444'],
            ['Sewa Bengkel', '#f59e0b'],
            ['Listrik & Air', '#06b6d4'],
        ]);

        // Toko 10
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $add($t->id, [
            ['Gaji Karyawan', '#6366f1'],
            ['Sewa & Listrik', '#f59e0b'],
            ['Pengiriman Barang', '#10b981'],
        ]);

        // Toko 11
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $add($t->id, [
            ['Administrasi Koperasi', '#22c55e'],
            ['Listrik & Air', '#06b6d4'],
            ['Transportasi', '#10b981'],
        ]);

        // Toko 12
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $add($t->id, [
            ['Gaji Karyawan', '#6366f1'],
            ['Sewa Toko', '#f59e0b'],
            ['Event Sponsorship', '#ec4899'],
            ['Listrik & Air', '#06b6d4'],
        ]);

        // Toko 13
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $add($t->id, [
            ['Gaji Operator', '#6366f1'],
            ['Sewa Ruang Produksi', '#f59e0b'],
            ['Perawatan Mesin', '#ef4444'],
            ['Listrik & Air', '#06b6d4'],
        ]);

        DB::table('expense_categories')->insert($categories);
    }
}