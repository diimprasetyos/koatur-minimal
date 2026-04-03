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
        $suppliers = [];

        $add = function (int $tenantId, array $rows) use (&$suppliers, $now) {
            foreach ($rows as $r) {
                $r['uuid'] = Str::uuid();
                $r['tenant_id'] = $tenantId;
                $r['created_at'] = $now;
                $r['updated_at'] = $now;
                $r += ['email' => null, 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null];
                $suppliers[] = $r;
            }
        };

        // ── Toko 1 — Toko Maju Jaya ──
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $add($t->id, [
            ['name' => 'PT Indofood CBP', 'code' => 'SUP-001', 'phone' => '021-5550001', 'email' => 'sales@indofood.co.id', 'address' => 'Jl. Jend. Sudirman, Jakarta', 'contact_person' => 'Pak Slamet'],
            ['name' => 'CV Elektro Jaya', 'code' => 'SUP-002', 'phone' => '031-3334455', 'email' => 'order@elektrojaya.com', 'address' => 'Jl. Kapas Krampung No. 8, Surabaya', 'contact_person' => 'Pak Joko', 'payable_amount' => 350000, 'notes' => 'Min. order Rp 500.000'],
            ['name' => 'Distro Pakaian Surabaya', 'code' => 'SUP-003', 'phone' => '085123456789', 'address' => 'Jl. Tunjungan No. 15, Surabaya', 'contact_person' => 'Bu Yanti'],
            ['name' => 'Grosir ATK Murah', 'code' => 'SUP-004', 'phone' => '081234509876', 'email' => 'atk@grosirmurah.id', 'address' => 'Jl. Blauran No. 30, Surabaya'],
            ['name' => 'UD Tirta Segar', 'code' => 'SUP-005', 'phone' => '031-7779900', 'address' => 'Jl. Kalibokor No. 4, Surabaya', 'is_active' => false, 'notes' => 'Supplier tidak aktif'],
        ]);

        // ── Toko 2 — Warung Berkah Abadi ──
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $add($t->id, [
            ['name' => 'PT Bogasari', 'code' => 'S-001', 'phone' => '021-6660011', 'address' => 'Jl. Raya Tanjung Priok, Jakarta', 'contact_person' => 'Pak Hasan', 'payable_amount' => 500000],
            ['name' => 'Distributor Coca-Cola SBY', 'code' => 'S-002', 'phone' => '031-4445566', 'email' => 'order@cc-sby.co.id', 'address' => 'Jl. Margomulyo No. 8, Surabaya', 'contact_person' => 'Pak Ari', 'notes' => 'Pengiriman setiap Selasa'],
            ['name' => 'UD Sembako Makmur', 'code' => 'S-003', 'phone' => '086543210987', 'address' => 'Pasar Larangan, Sidoarjo', 'contact_person' => 'Bu Mimi', 'payable_amount' => 125000],
        ]);

        // ── Toko 3 — CV Sumber Rejeki ──
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $add($t->id, [
            ['name' => 'PT Krakatau Steel', 'code' => 'VND-001', 'phone' => '0254-392000', 'email' => 'sales@krakatausteel.id', 'address' => 'Jl. Industri No. 1, Cilegon', 'contact_person' => 'Pak Warno', 'payable_amount' => 7000000, 'notes' => 'Kontrak tahunan'],
            ['name' => 'Toko Cat Indah', 'code' => 'VND-002', 'phone' => '031-5557788', 'address' => 'Jl. Raya Waru No. 45, Sidoarjo'],
            ['name' => 'CV Baja Nusantara', 'code' => 'VND-003', 'phone' => '031-6661234', 'address' => 'Jl. Rungkut Industri No. 3, Surabaya', 'payable_amount' => 2000000],
        ]);

        // ── Toko 4 — Apotek Sehat Sentosa ──
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $add($t->id, [
            ['name' => 'PT Kimia Farma', 'code' => 'APT-S001', 'phone' => '021-3847748', 'email' => 'pbf@kimiafarma.co.id', 'address' => 'Jl. Veteran No. 9, Jakarta', 'contact_person' => 'Pak Iman', 'payable_amount' => 1500000],
            ['name' => 'PBF Rajawali Nusindo', 'code' => 'APT-S002', 'phone' => '031-3458800', 'address' => 'Jl. Kusuma Bangsa No. 7, Surabaya', 'contact_person' => 'Bu Deasy', 'notes' => 'Pengiriman tiap Rabu'],
            ['name' => 'PT Kalbe Farma', 'code' => 'APT-S003', 'phone' => '021-4287808', 'email' => 'trade@kalbe.co.id', 'address' => 'Jl. Let. Jend. Suprapto, Jakarta'],
        ]);

        // ── Toko 5 — Toko Bangunan Kokoh ──
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $add($t->id, [
            ['name' => 'PT Semen Indonesia', 'code' => 'BNG-S001', 'phone' => '031-3981732', 'email' => 'sales@semenindonesia.com', 'address' => 'Jl. Veteran, Gresik', 'payable_amount' => 3000000],
            ['name' => 'UD Besi Beton Makmur', 'code' => 'BNG-S002', 'phone' => '085388990011', 'address' => 'Jl. Margomulyo No. 22, Surabaya', 'payable_amount' => 1200000],
            ['name' => 'Distributor Toto Tile', 'code' => 'BNG-S003', 'phone' => '031-5673456', 'address' => 'Jl. Raya Darmo No. 8, Surabaya', 'contact_person' => 'Pak Sujito'],
        ]);

        // ── Toko 6 — UD Elektronik Mandiri ──
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $add($t->id, [
            ['name' => 'Distributor Samsung SBY', 'code' => 'ELK-S001', 'phone' => '031-5450000', 'email' => 'dist@samsung.co.id', 'address' => 'Jl. Pemuda No. 33, Surabaya', 'contact_person' => 'Pak Hendra', 'payable_amount' => 5000000],
            ['name' => 'PT Erafone Artha Retailindo', 'code' => 'ELK-S002', 'phone' => '021-5019555', 'address' => 'Jl. MT. Haryono, Jakarta', 'payable_amount' => 2500000, 'notes' => 'Resmi distributor HP'],
            ['name' => 'Grosir Aksesoris Surabaya', 'code' => 'ELK-S003', 'phone' => '085777888999', 'address' => 'Jl. Kapas Krampung No. 50, Surabaya', 'contact_person' => 'Mas Ferry'],
        ]);

        // ── Toko 7 — Butik Mode Terkini ──
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $add($t->id, [
            ['name' => 'CV Fashion Nusantara', 'code' => 'BUT-S001', 'phone' => '031-8885577', 'address' => 'Sentra Grosir Surabaya, Blok B2', 'payable_amount' => 2000000, 'notes' => 'Pengiriman tiap 2 minggu'],
            ['name' => 'Supplier Tas Import Murah', 'code' => 'BUT-S002', 'phone' => '08122334567', 'address' => 'ITC Mangga Dua, Jakarta', 'payable_amount' => 800000],
            ['name' => 'Pabrik Kaos Rajawali', 'code' => 'BUT-S003', 'phone' => '085800112233', 'email' => 'order@rajawalikaos.id', 'address' => 'Jl. Raya Candi No. 3, Sidoarjo', 'contact_person' => 'Bu Wati'],
        ]);

        // ── Toko 8 — Minimarket Segar Prima ──
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $add($t->id, [
            ['name' => 'PT Indomarco Adi Prima', 'code' => 'MNI-S001', 'phone' => '021-4607878', 'address' => 'Jl. Ancol Barat, Jakarta', 'payable_amount' => 4000000, 'notes' => 'Distributor resmi'],
            ['name' => 'UD Sumber Sari Segar', 'code' => 'MNI-S002', 'phone' => '031-7886655', 'address' => 'Pasar Induk Osowilangun, Surabaya', 'contact_person' => 'Pak Iman', 'payable_amount' => 600000],
            ['name' => 'CV Produk Kebersihan Jaya', 'code' => 'MNI-S003', 'phone' => '085566001122', 'address' => 'Jl. Raya Gedangan, Sidoarjo'],
        ]);

        // ── Toko 9 — Bengkel Motor Jaya ──
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $add($t->id, [
            ['name' => 'PT Astra Honda Motor', 'code' => 'BNK-S001', 'phone' => '021-6531888', 'email' => 'sparepart@ahm.co.id', 'address' => 'Jl. Yos Sudarso, Jakarta', 'payable_amount' => 3500000],
            ['name' => 'UD Oli Prima Surabaya', 'code' => 'BNK-S002', 'phone' => '031-3345566', 'address' => 'Jl. Dupak No. 8, Surabaya', 'contact_person' => 'Pak Samsul', 'payable_amount' => 750000],
            ['name' => 'Grosir Spare Part Motor', 'code' => 'BNK-S003', 'phone' => '085299887766', 'address' => 'Jl. Jagir Wonokromo No. 11, SBY', 'notes' => 'Harga bisa nego untuk qty besar'],
        ]);

        // ── Toko 10 — Toko Peralatan Dapur ──
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $add($t->id, [
            ['name' => 'PT Maspion Group', 'code' => 'DPR-S001', 'phone' => '031-8967888', 'email' => 'sales@maspion.co.id', 'address' => 'Jl. Kolonel Sugiono No. 99, Sidoarjo', 'contact_person' => 'Bu Ira', 'payable_amount' => 2500000],
            ['name' => 'UD Peralatan Dapur Murah', 'code' => 'DPR-S002', 'phone' => '085800445566', 'address' => 'Pasar Atom, Surabaya', 'payable_amount' => 400000],
            ['name' => 'Distributor Teflon & Pan', 'code' => 'DPR-S003', 'phone' => '081355667788', 'address' => 'Jl. Gembong No. 5, Surabaya', 'contact_person' => 'Mas Rian'],
        ]);

        // ── Toko 11 — Koperasi Makmur Bersama ──
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $add($t->id, [
            ['name' => 'Grosir Sembako Mojokerto', 'code' => 'KOP-S001', 'phone' => '0321-334455', 'address' => 'Pasar Kliwon, Mojokerto', 'contact_person' => 'Pak Walidi', 'payable_amount' => 1000000],
            ['name' => 'UD Alat Tulis Lengkap', 'code' => 'KOP-S002', 'phone' => '085122334456', 'address' => 'Jl. Benteng Pancasila, Mojokerto'],
        ]);

        // ── Toko 12 — Toko Olahraga Sporty ──
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $add($t->id, [
            ['name' => 'Distributor Nike Indonesia', 'code' => 'SPT-S001', 'phone' => '021-5200011', 'address' => 'Jl. Gatot Subroto, Jakarta', 'contact_person' => 'Pak Bayu', 'payable_amount' => 8000000, 'notes' => 'Minimum order Rp 5 juta'],
            ['name' => 'UD Olahraga Nasional', 'code' => 'SPT-S002', 'phone' => '031-3678900', 'address' => 'Jl. Bung Tomo No. 4, Surabaya', 'payable_amount' => 1500000],
            ['name' => 'Grosir Suplemen Sport', 'code' => 'SPT-S003', 'phone' => '085900112234', 'email' => 'info@sportsupp.id', 'address' => 'Jl. Raya Jemur Sari, Surabaya'],
        ]);

        // ── Toko 13 — Percetakan Digital Cepat ──
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $add($t->id, [
            ['name' => 'PT Indah Kiat Pulp & Paper', 'code' => 'PCT-S001', 'phone' => '021-3901234', 'email' => 'sales@indahkiat.co.id', 'address' => 'Jl. MH Thamrin, Jakarta', 'payable_amount' => 5000000, 'notes' => 'Supplier kertas utama'],
            ['name' => 'Distributor Tinta Epson SBY', 'code' => 'PCT-S002', 'phone' => '031-5453210', 'address' => 'Jl. Bubutan No. 22, Surabaya', 'contact_person' => 'Pak Daud', 'payable_amount' => 800000],
            ['name' => 'Grosir Bahan Souvenir', 'code' => 'PCT-S003', 'phone' => '085911223344', 'address' => 'Jl. Kedungdoro No. 5, Surabaya'],
        ]);

        DB::table('suppliers')->insert($suppliers);
    }
}