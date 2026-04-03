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
        $expenses = [];

        // Helper: ambil user owner per tenant
        $owner = fn(string $slug) => DB::table('users')
            ->where('email', 'like', 'owner.%')
            ->whereIn('id', function ($q) use ($slug) {
                $q->select('user_id')->from('tenant_user')
                    ->where('tenant_id', DB::table('tenants')->where('slug', $slug)->value('id'));
            })->first();

        $cat = fn(int $tenantId, string $name) => DB::table('expense_categories')
            ->where('tenant_id', $tenantId)->where('name', $name)->value('id');

        $add = function (string $slug, array $rows) use (&$expenses, $now, $owner, $cat) {
            $t = DB::table('tenants')->where('slug', $slug)->first();
            $u = $owner($slug);
            foreach ($rows as $r) {
                $expenses[] = array_merge([
                    'uuid' => Str::uuid(),
                    'tenant_id' => $t->id,
                    'user_id' => $u->id,
                    'expense_category_id' => $cat($t->id, $r['cat']),
                    'reference_number' => $r['ref'],
                    'title' => $r['title'],
                    'amount' => $r['amount'],
                    'expense_date' => $r['date'],
                    'payment_method' => $r['pay'] ?? 'transfer',
                    'notes' => $r['notes'] ?? null,
                    'attachment' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        };

        // ── Toko 1 ──
        $add('toko-1', [
            ['cat' => 'Gaji & Upah', 'ref' => 'EXP-T1-001', 'title' => 'Gaji Karyawan Maret 2026', 'amount' => 3500000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa & Utilitas', 'ref' => 'EXP-T1-002', 'title' => 'Sewa Ruko Maret 2026', 'amount' => 2500000, 'date' => '2026-03-01', 'notes' => 'Pembayaran bulanan'],
            ['cat' => 'Transportasi', 'ref' => 'EXP-T1-003', 'title' => 'Bensin Antar Barang', 'amount' => 150000, 'date' => '2026-03-10', 'pay' => 'cash'],
            ['cat' => 'Sewa & Utilitas', 'ref' => 'EXP-T1-004', 'title' => 'Tagihan Listrik Maret', 'amount' => 450000, 'date' => '2026-03-20'],
            ['cat' => 'Perlengkapan Toko', 'ref' => 'EXP-T1-005', 'title' => 'Plastik & Kantong Belanja', 'amount' => 85000, 'date' => '2026-03-15', 'pay' => 'cash', 'notes' => 'Beli di supplier lokal'],
            ['cat' => 'Lain-lain', 'ref' => 'EXP-T1-006', 'title' => 'Perbaikan Etalase', 'amount' => 200000, 'date' => '2026-03-25', 'pay' => 'cash'],
        ]);

        // ── Toko 2 ──
        $add('toko-2', [
            ['cat' => 'Operasional', 'ref' => 'EXP-T2-001', 'title' => 'Plastik Kresek Stok Bulan Ini', 'amount' => 60000, 'date' => '2026-03-05', 'pay' => 'cash'],
            ['cat' => 'Listrik & Air', 'ref' => 'EXP-T2-002', 'title' => 'Tagihan Listrik Maret', 'amount' => 280000, 'date' => '2026-03-20'],
            ['cat' => 'Operasional', 'ref' => 'EXP-T2-003', 'title' => 'Biaya Kebersihan Pasar', 'amount' => 50000, 'date' => '2026-03-01', 'pay' => 'cash', 'notes' => 'Bayar iuran pasar'],
        ]);

        // ── Toko 3 ──
        $add('toko-3', [
            ['cat' => 'Overhead Pabrik', 'ref' => 'EXP-T3-001', 'title' => 'Biaya Listrik Gudang Maret', 'amount' => 1200000, 'date' => '2026-03-20'],
            ['cat' => 'Perawatan Alat', 'ref' => 'EXP-T3-002', 'title' => 'Service Mesin Las', 'amount' => 350000, 'date' => '2026-03-12', 'pay' => 'cash', 'notes' => 'Servis rutin 3 bulan sekali'],
            ['cat' => 'Overhead Pabrik', 'ref' => 'EXP-T3-003', 'title' => 'Sewa Gudang Q1 2026', 'amount' => 4500000, 'date' => '2026-03-01', 'notes' => 'Sewa 3 bulan Jan-Mar'],
        ]);

        // ── Toko 4 ──
        $add('toko-4', [
            ['cat' => 'Gaji Apoteker & Staff', 'ref' => 'EXP-T4-001', 'title' => 'Gaji Apoteker Maret 2026', 'amount' => 5000000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa & Listrik', 'ref' => 'EXP-T4-002', 'title' => 'Sewa Ruko Apotek Maret', 'amount' => 3000000, 'date' => '2026-03-01'],
            ['cat' => 'Sewa & Listrik', 'ref' => 'EXP-T4-003', 'title' => 'Tagihan Listrik & Air', 'amount' => 600000, 'date' => '2026-03-20'],
            ['cat' => 'Peralatan Apotek', 'ref' => 'EXP-T4-004', 'title' => 'Kertas Struk & Kantong Obat', 'amount' => 120000, 'date' => '2026-03-10', 'pay' => 'cash'],
        ]);

        // ── Toko 5 ──
        $add('toko-5', [
            ['cat' => 'Gaji Karyawan', 'ref' => 'EXP-T5-001', 'title' => 'Gaji Karyawan Maret 2026', 'amount' => 4500000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa Gudang', 'ref' => 'EXP-T5-002', 'title' => 'Sewa Gudang & Toko Maret', 'amount' => 3500000, 'date' => '2026-03-01'],
            ['cat' => 'Transportasi Barang', 'ref' => 'EXP-T5-003', 'title' => 'Ongkir Material dari Gresik', 'amount' => 450000, 'date' => '2026-03-08', 'pay' => 'cash'],
            ['cat' => 'Listrik & Air', 'ref' => 'EXP-T5-004', 'title' => 'Tagihan Listrik Maret', 'amount' => 750000, 'date' => '2026-03-20'],
        ]);

        // ── Toko 6 ──
        $add('toko-6', [
            ['cat' => 'Gaji Teknisi', 'ref' => 'EXP-T6-001', 'title' => 'Gaji Teknisi & Kasir Maret', 'amount' => 4000000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa & Utilitas', 'ref' => 'EXP-T6-002', 'title' => 'Sewa Toko Maret 2026', 'amount' => 2800000, 'date' => '2026-03-01'],
            ['cat' => 'Iklan & Promosi', 'ref' => 'EXP-T6-003', 'title' => 'Iklan Instagram Ads Maret', 'amount' => 350000, 'date' => '2026-03-15', 'notes' => 'Boost post produk HP baru'],
            ['cat' => 'Perlengkapan Servis', 'ref' => 'EXP-T6-004', 'title' => 'Beli Solder & Flux', 'amount' => 185000, 'date' => '2026-03-05', 'pay' => 'cash'],
        ]);

        // ── Toko 7 ──
        $add('toko-7', [
            ['cat' => 'Gaji Karyawan', 'ref' => 'EXP-T7-001', 'title' => 'Gaji Karyawan Butik Maret', 'amount' => 3500000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa Toko', 'ref' => 'EXP-T7-002', 'title' => 'Sewa Toko Tunjungan Maret', 'amount' => 4500000, 'date' => '2026-03-01'],
            ['cat' => 'Display & Dekorasi', 'ref' => 'EXP-T7-003', 'title' => 'Manekin & Gantungan Baju', 'amount' => 650000, 'date' => '2026-03-10', 'pay' => 'cash'],
            ['cat' => 'Sosial Media Ads', 'ref' => 'EXP-T7-004', 'title' => 'Facebook & IG Ads Maret', 'amount' => 500000, 'date' => '2026-03-15'],
        ]);

        // ── Toko 8 ──
        $add('toko-8', [
            ['cat' => 'Gaji Kasir & Staff', 'ref' => 'EXP-T8-001', 'title' => 'Gaji Kasir & Staff Maret', 'amount' => 5500000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa Ruko', 'ref' => 'EXP-T8-002', 'title' => 'Sewa Ruko Minimarket Maret', 'amount' => 5000000, 'date' => '2026-03-01'],
            ['cat' => 'Listrik & Pendingin', 'ref' => 'EXP-T8-003', 'title' => 'Listrik & AC Minimarket', 'amount' => 1200000, 'date' => '2026-03-20'],
            ['cat' => 'Kantong & Kemasan', 'ref' => 'EXP-T8-004', 'title' => 'Kantong Plastik & Kresek', 'amount' => 175000, 'date' => '2026-03-07', 'pay' => 'cash'],
        ]);

        // ── Toko 9 ──
        $add('toko-9', [
            ['cat' => 'Gaji Mekanik', 'ref' => 'EXP-T9-001', 'title' => 'Gaji 2 Mekanik Maret 2026', 'amount' => 4000000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa Bengkel', 'ref' => 'EXP-T9-002', 'title' => 'Sewa Bengkel Maret 2026', 'amount' => 2000000, 'date' => '2026-03-01'],
            ['cat' => 'Perawatan Peralatan', 'ref' => 'EXP-T9-003', 'title' => 'Servis Kompresor Udara', 'amount' => 300000, 'date' => '2026-03-14', 'pay' => 'cash'],
            ['cat' => 'Listrik & Air', 'ref' => 'EXP-T9-004', 'title' => 'Tagihan Listrik Bengkel', 'amount' => 550000, 'date' => '2026-03-20'],
        ]);

        // ── Toko 10 ──
        $add('toko-10', [
            ['cat' => 'Gaji Karyawan', 'ref' => 'EXP-T10-001', 'title' => 'Gaji Karyawan Maret 2026', 'amount' => 3000000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa & Listrik', 'ref' => 'EXP-T10-002', 'title' => 'Sewa Toko & Listrik Maret', 'amount' => 2200000, 'date' => '2026-03-01'],
            ['cat' => 'Pengiriman Barang', 'ref' => 'EXP-T10-003', 'title' => 'Ongkir Pengiriman Pelanggan', 'amount' => 120000, 'date' => '2026-03-22', 'pay' => 'cash'],
        ]);

        // ── Toko 11 ──
        $add('toko-11', [
            ['cat' => 'Administrasi Koperasi', 'ref' => 'EXP-T11-001', 'title' => 'Biaya Admin & ATK Koperasi', 'amount' => 350000, 'date' => '2026-03-05', 'pay' => 'cash'],
            ['cat' => 'Listrik & Air', 'ref' => 'EXP-T11-002', 'title' => 'Tagihan Listrik & Air', 'amount' => 220000, 'date' => '2026-03-20'],
            ['cat' => 'Transportasi', 'ref' => 'EXP-T11-003', 'title' => 'Ongkos Antar Barang Anggota', 'amount' => 100000, 'date' => '2026-03-15', 'pay' => 'cash'],
        ]);

        // ── Toko 12 ──
        $add('toko-12', [
            ['cat' => 'Gaji Karyawan', 'ref' => 'EXP-T12-001', 'title' => 'Gaji Karyawan Maret 2026', 'amount' => 3500000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa Toko', 'ref' => 'EXP-T12-002', 'title' => 'Sewa Toko Olahraga Maret', 'amount' => 3000000, 'date' => '2026-03-01'],
            ['cat' => 'Event Sponsorship', 'ref' => 'EXP-T12-003', 'title' => 'Sponsor Turnamen Futsal', 'amount' => 500000, 'date' => '2026-03-20', 'notes' => 'Branding di lapangan'],
            ['cat' => 'Listrik & Air', 'ref' => 'EXP-T12-004', 'title' => 'Tagihan Listrik Maret', 'amount' => 400000, 'date' => '2026-03-20'],
        ]);

        // ── Toko 13 ──
        $add('toko-13', [
            ['cat' => 'Gaji Operator', 'ref' => 'EXP-T13-001', 'title' => 'Gaji 2 Operator Mesin Maret', 'amount' => 4500000, 'date' => '2026-03-31'],
            ['cat' => 'Sewa Ruang Produksi', 'ref' => 'EXP-T13-002', 'title' => 'Sewa Ruang Produksi Maret', 'amount' => 3500000, 'date' => '2026-03-01'],
            ['cat' => 'Listrik & Air', 'ref' => 'EXP-T13-003', 'title' => 'Tagihan Listrik Mesin Cetak', 'amount' => 1800000, 'date' => '2026-03-20'],
            ['cat' => 'Perawatan Mesin', 'ref' => 'EXP-T13-004', 'title' => 'Servis & Kalibrasi Printer', 'amount' => 650000, 'date' => '2026-03-12', 'pay' => 'cash', 'notes' => 'Servis rutin 3 bulan'],
        ]);

        DB::table('expenses')->insert($expenses);
    }
}