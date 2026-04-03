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
        $customers = [];

        $add = function (int $tenantId, array $rows) use (&$customers, $now) {
            foreach ($rows as $r) {
                $r['uuid'] = Str::uuid();
                $r['tenant_id'] = $tenantId;
                $r['created_at'] = $now;
                $r['updated_at'] = $now;
                $r += ['email' => null, 'address' => null, 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null];
                $customers[] = $r;
            }
        };

        // ── Toko 1 — Toko Maju Jaya ──
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $add($t->id, [
            ['name' => 'Agus Prasetyo', 'code' => 'CUST-001', 'phone' => '081234567890', 'email' => 'agus@gmail.com', 'address' => 'Jl. Rungkut No. 12, Surabaya'],
            ['name' => 'Dewi Rahayu', 'code' => 'CUST-002', 'phone' => '082345678901', 'email' => 'dewi@yahoo.com', 'address' => 'Jl. Gubeng No. 7, Surabaya', 'payable_amount' => 150000, 'notes' => 'Pelanggan langganan bulanan'],
            ['name' => 'Toko Sinar Mas', 'code' => 'CUST-003', 'phone' => '031-5551234', 'address' => 'Jl. Semolowaru No. 55, Surabaya', 'contact_person' => 'Pak Mas', 'payable_amount' => 500000, 'notes' => 'Bayar akhir bulan'],
            ['name' => 'Fitria Handayani', 'code' => 'CUST-004', 'phone' => '083456789012', 'email' => 'fitria@gmail.com', 'address' => 'Jl. Kenjeran No. 3, Surabaya'],
            ['name' => 'Yusuf Maulana', 'code' => 'CUST-005', 'phone' => '084567890123', 'is_active' => false, 'notes' => 'Tidak aktif'],
        ]);

        // ── Toko 2 — Warung Berkah Abadi ──
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $add($t->id, [
            ['name' => 'Rina Marlina', 'code' => 'C-001', 'phone' => '085678901234', 'email' => 'rina@gmail.com', 'address' => 'Jl. Ahmad Yani No. 99, Sidoarjo'],
            ['name' => 'Bambang Susilo', 'code' => 'C-002', 'phone' => '086789012345', 'address' => 'Jl. Pahlawan No. 14, Sidoarjo', 'payable_amount' => 75000],
            ['name' => 'Kantin Sekolah SMK 1', 'code' => 'C-003', 'phone' => '031-8889900', 'address' => 'Jl. Mojopahit No. 5, Sidoarjo', 'contact_person' => 'Bu Ani', 'payable_amount' => 250000, 'notes' => 'Order mingguan tiap Senin'],
        ]);

        // ── Toko 3 — CV Sumber Rejeki ──
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $add($t->id, [
            ['name' => 'PT Graha Bangun Jaya', 'code' => 'CLT-001', 'phone' => '031-7778880', 'email' => 'procurement@graha.co.id', 'address' => 'Jl. HR Muhammad No. 10, Surabaya', 'contact_person' => 'Ibu Sari', 'payable_amount' => 2500000, 'notes' => 'Klien kontrak tahunan'],
            ['name' => 'CV Mitra Konstruksi', 'code' => 'CLT-002', 'phone' => '085890123456', 'address' => 'Jl. Wonorejo No. 22, Surabaya', 'contact_person' => 'Pak Dodi'],
            ['name' => 'UD Karya Mandiri', 'code' => 'CLT-003', 'phone' => '087801234567', 'address' => 'Jl. Raya Driyorejo, Gresik', 'payable_amount' => 750000],
        ]);

        // ── Toko 4 — Apotek Sehat Sentosa ──
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $add($t->id, [
            ['name' => 'Hendra Wijaya', 'code' => 'APT-001', 'phone' => '081122334455', 'email' => 'hendra@gmail.com', 'address' => 'Jl. Darmo Permai No. 5, Surabaya'],
            ['name' => 'Klinik Husada Sehat', 'code' => 'APT-002', 'phone' => '031-4441234', 'address' => 'Jl. Mayjend Sungkono No. 11, Surabaya', 'contact_person' => 'Dr. Anita', 'payable_amount' => 300000, 'notes' => 'Langganan resep bulanan'],
            ['name' => 'Puskesmas Wonokromo', 'code' => 'APT-003', 'phone' => '031-5678900', 'address' => 'Jl. Wonokromo No. 2, Surabaya', 'contact_person' => 'Bu Sinta'],
        ]);

        // ── Toko 5 — Toko Bangunan Kokoh ──
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $add($t->id, [
            ['name' => 'PT Griya Indah Properti', 'code' => 'BNG-001', 'phone' => '031-6660001', 'email' => 'order@griyaindah.co.id', 'address' => 'Jl. Citraland, Surabaya', 'contact_person' => 'Pak Rudi', 'payable_amount' => 1500000, 'notes' => 'Developer perumahan'],
            ['name' => 'Tukang Bangunan Pak Sabar', 'code' => 'BNG-002', 'phone' => '082211223344', 'address' => 'Jl. Wiyung No. 5, Surabaya', 'payable_amount' => 200000],
            ['name' => 'CV Karya Prima', 'code' => 'BNG-003', 'phone' => '085566778899', 'address' => 'Jl. Raya Menganti, Gresik', 'contact_person' => 'Pak Bondan'],
        ]);

        // ── Toko 6 — UD Elektronik Mandiri ──
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $add($t->id, [
            ['name' => 'Rizky Pratama', 'code' => 'ELK-001', 'phone' => '081987654321', 'email' => 'rizky@gmail.com', 'address' => 'Jl. Mulyorejo No. 8, Surabaya'],
            ['name' => 'Toko Pulsa Kencana', 'code' => 'ELK-002', 'phone' => '085123445566', 'address' => 'Jl. Kapas Krampung No. 5, Surabaya', 'payable_amount' => 450000, 'notes' => 'Reseller aksesoris'],
            ['name' => 'Warnet Cyber Speed', 'code' => 'ELK-003', 'phone' => '031-7892345', 'address' => 'Jl. Ngagel No. 20, Surabaya', 'contact_person' => 'Mas Deni'],
        ]);

        // ── Toko 7 — Butik Mode Terkini ──
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $add($t->id, [
            ['name' => 'Sari Dewi Lestari', 'code' => 'BUT-001', 'phone' => '082233445566', 'email' => 'sari@gmail.com', 'address' => 'Jl. Raya Darmo No. 44, Surabaya'],
            ['name' => 'Nurul Hidayah', 'code' => 'BUT-002', 'phone' => '083344556677', 'address' => 'Jl. Ketintang No. 9, Surabaya', 'payable_amount' => 85000],
            ['name' => 'Toko Grosir Mama', 'code' => 'BUT-003', 'phone' => '031-8887766', 'address' => 'Jl. Ampel No. 3, Surabaya', 'contact_person' => 'Bu Mama', 'payable_amount' => 600000, 'notes' => 'Pembeli grosir'],
        ]);

        // ── Toko 8 — Minimarket Segar Prima ──
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $add($t->id, [
            ['name' => 'Indah Permatasari', 'code' => 'MNI-001', 'phone' => '081555667788', 'email' => 'indah@gmail.com', 'address' => 'Jl. Manyar No. 5, Surabaya'],
            ['name' => 'Panti Asuhan Al-Ikhlas', 'code' => 'MNI-002', 'phone' => '031-7773344', 'address' => 'Jl. Karang Asem No. 1, Surabaya', 'contact_person' => 'Pak Ustadz', 'notes' => 'Pembelian bulk bulanan'],
            ['name' => 'Restoran Padang Minang', 'code' => 'MNI-003', 'phone' => '085677889900', 'address' => 'Jl. Basuki Rahmat No. 15, Surabaya', 'payable_amount' => 120000],
        ]);

        // ── Toko 9 — Bengkel Motor Jaya ──
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $add($t->id, [
            ['name' => 'Doni Setiawan', 'code' => 'BNK-001', 'phone' => '082111223344', 'email' => 'doni@gmail.com', 'address' => 'Jl. Kenjeran No. 22, Surabaya'],
            ['name' => 'Ahmad Fauzi', 'code' => 'BNK-002', 'phone' => '081333445566', 'address' => 'Jl. Sidotopo No. 7, Surabaya', 'payable_amount' => 95000],
            ['name' => 'Rental Motor Ceria', 'code' => 'BNK-003', 'phone' => '031-3334455', 'address' => 'Jl. Kertajaya No. 12, Surabaya', 'contact_person' => 'Pak Bowo', 'payable_amount' => 350000, 'notes' => 'Servis armada bulanan'],
        ]);

        // ── Toko 10 — Toko Peralatan Dapur ──
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $add($t->id, [
            ['name' => 'Catering Bu Lastri', 'code' => 'DPR-001', 'phone' => '082566778899', 'email' => 'lastri@gmail.com', 'address' => 'Jl. Wonorejo No. 5, Surabaya', 'payable_amount' => 250000, 'notes' => 'Pelanggan setia catering'],
            ['name' => 'Hotel Bintang Timur', 'code' => 'DPR-002', 'phone' => '031-5559988', 'address' => 'Jl. Tunjungan No. 50, Surabaya', 'contact_person' => 'Bu Ratna', 'payable_amount' => 1200000],
            ['name' => 'Wulandari Susanti', 'code' => 'DPR-003', 'phone' => '085899001122', 'address' => 'Jl. Semolowaru No. 8, Surabaya'],
        ]);

        // ── Toko 11 — Koperasi Makmur Bersama ──
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $add($t->id, [
            ['name' => 'Anggota Koperasi 001', 'code' => 'KOP-001', 'phone' => '081234000001', 'address' => 'Desa Mojosari, Mojokerto'],
            ['name' => 'Anggota Koperasi 002', 'code' => 'KOP-002', 'phone' => '081234000002', 'address' => 'Desa Sooko, Mojokerto', 'payable_amount' => 50000],
            ['name' => 'Warung Pak Trimo', 'code' => 'KOP-003', 'phone' => '085677000003', 'address' => 'Desa Ngoro, Mojokerto', 'payable_amount' => 150000],
        ]);

        // ── Toko 12 — Toko Olahraga Sporty ──
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $add($t->id, [
            ['name' => 'Eko Budianto', 'code' => 'SPT-001', 'phone' => '081777889900', 'email' => 'eko@gmail.com', 'address' => 'Jl. Pemuda No. 5, Surabaya'],
            ['name' => 'Klub Futsal Garuda', 'code' => 'SPT-002', 'phone' => '085111222333', 'address' => 'GOR Delta, Sidoarjo', 'contact_person' => 'Pak Agung', 'payable_amount' => 400000, 'notes' => 'Order jersey tim'],
            ['name' => 'Sekolah SMA 5', 'code' => 'SPT-003', 'phone' => '031-5552233', 'address' => 'Jl. Gubernur Suryo, Surabaya', 'contact_person' => 'Pak Guru OR'],
        ]);

        // ── Toko 13 — Percetakan Digital Cepat ──
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $add($t->id, [
            ['name' => 'PT Cahaya Media Utama', 'code' => 'PCT-001', 'phone' => '031-3335544', 'email' => 'order@cahayamedia.co.id', 'address' => 'Jl. Basuki Rahmat No. 8, Surabaya', 'contact_person' => 'Pak Handi', 'payable_amount' => 850000, 'notes' => 'Klien korporat bulanan'],
            ['name' => 'EO Spektakuler Event', 'code' => 'PCT-002', 'phone' => '085222333444', 'address' => 'Jl. Embong Cerme No. 3, Surabaya', 'payable_amount' => 500000],
            ['name' => 'Rudi Santoso', 'code' => 'PCT-003', 'phone' => '082333444555', 'email' => 'rudi@gmail.com', 'address' => 'Jl. Dharmahusada No. 9, Surabaya'],
        ]);

        DB::table('customers')->insert($customers);
    }
}