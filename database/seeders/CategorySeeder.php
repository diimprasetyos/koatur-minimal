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
        $categories = [];

        // Helper closure
        $addCats = function (int $tenantId, array $cats) use (&$categories, $now) {
            foreach ($cats as [$name, $color, $active]) {
                $categories[] = [
                    'uuid' => Str::uuid(),
                    'tenant_id' => $tenantId,
                    'name' => $name,
                    'color' => $color,
                    'is_active' => $active,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        };

        // Toko 1 — Toko Maju Jaya (serba ada)
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $addCats($t->id, [
            ['Makanan & Minuman', '#f59e0b', true],
            ['Elektronik', '#3b82f6', true],
            ['Pakaian', '#ec4899', true],
            ['Perlengkapan Rumah', '#10b981', true],
            ['Alat Tulis', '#8b5cf6', true],
        ]);

        // Toko 2 — Warung Berkah Abadi (sembako & snack)
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $addCats($t->id, [
            ['Sembako', '#f97316', true],
            ['Minuman', '#06b6d4', true],
            ['Snack & Camilan', '#eab308', true],
        ]);

        // Toko 3 — CV Sumber Rejeki (bahan bangunan)
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $addCats($t->id, [
            ['Bahan Baku', '#64748b', true],
            ['Produk Jadi', '#22c55e', true],
            ['Spare Part', '#ef4444', false],
        ]);

        // Toko 4 — Apotek Sehat Sentosa
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $addCats($t->id, [
            ['Obat Bebas', '#10b981', true],
            ['Obat Keras', '#ef4444', true],
            ['Vitamin & Suplemen', '#f59e0b', true],
            ['Alat Kesehatan', '#3b82f6', true],
            ['Perawatan Tubuh', '#ec4899', true],
        ]);

        // Toko 5 — Toko Bangunan Kokoh
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $addCats($t->id, [
            ['Semen & Mortar', '#78716c', true],
            ['Besi & Baja', '#64748b', true],
            ['Cat & Pelapis', '#f97316', true],
            ['Pipa & Sanitasi', '#06b6d4', true],
            ['Keramik & Granit', '#8b5cf6', true],
        ]);

        // Toko 6 — UD Elektronik Mandiri
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $addCats($t->id, [
            ['Handphone & Tablet', '#3b82f6', true],
            ['Komputer & Laptop', '#6366f1', true],
            ['Aksesoris HP', '#ec4899', true],
            ['Audio & Speaker', '#f59e0b', true],
            ['Kabel & Adaptor', '#10b981', true],
        ]);

        // Toko 7 — Butik Mode Terkini
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $addCats($t->id, [
            ['Pakaian Wanita', '#ec4899', true],
            ['Pakaian Pria', '#3b82f6', true],
            ['Pakaian Anak', '#f59e0b', true],
            ['Tas & Dompet', '#8b5cf6', true],
            ['Aksesoris Mode', '#10b981', true],
        ]);

        // Toko 8 — Minimarket Segar Prima
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $addCats($t->id, [
            ['Bahan Makanan Segar', '#22c55e', true],
            ['Minuman Kemasan', '#06b6d4', true],
            ['Makanan Instan', '#f97316', true],
            ['Kebersihan & Sanitasi', '#10b981', true],
            ['Produk Bayi', '#ec4899', true],
        ]);

        // Toko 9 — Bengkel Motor Jaya
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $addCats($t->id, [
            ['Oli & Pelumas', '#78716c', true],
            ['Spare Part Motor', '#ef4444', true],
            ['Ban & Velg', '#64748b', true],
            ['Aksesoris Motor', '#f59e0b', true],
        ]);

        // Toko 10 — Toko Peralatan Dapur
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $addCats($t->id, [
            ['Peralatan Masak', '#f97316', true],
            ['Wadah & Penyimpanan', '#3b82f6', true],
            ['Peralatan Makan', '#10b981', true],
            ['Peralatan Listrik Dapur', '#6366f1', true],
        ]);

        // Toko 11 — Koperasi Makmur Bersama
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $addCats($t->id, [
            ['Kebutuhan Pokok', '#f59e0b', true],
            ['Peralatan Rumah', '#10b981', true],
            ['Alat Tulis Kantor', '#8b5cf6', true],
        ]);

        // Toko 12 — Toko Olahraga Sporty
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $addCats($t->id, [
            ['Pakaian Olahraga', '#3b82f6', true],
            ['Sepatu Olahraga', '#ef4444', true],
            ['Peralatan Gym', '#f59e0b', true],
            ['Alat Outdoor', '#22c55e', true],
            ['Suplemen Olahraga', '#8b5cf6', true],
        ]);

        // Toko 13 — Percetakan Digital Cepat
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $addCats($t->id, [
            ['Bahan Cetak', '#64748b', true],
            ['Produk Cetak', '#22c55e', true],
            ['Souvenir & Merchandise', '#ec4899', true],
            ['Alat Desain', '#6366f1', true],
        ]);

        DB::table('categories')->insert($categories);
    }
}