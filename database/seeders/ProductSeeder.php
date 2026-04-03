<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $products = [];

        $add = function (int $tenantId, int $catId, array $rows) use (&$products, $now) {
            foreach ($rows as $r) {
                $r['uuid'] = Str::uuid();
                $r['tenant_id'] = $tenantId;
                $r['category_id'] = $catId;
                $r += ['description' => null, 'image' => null, 'track_stock' => true, 'is_active' => true];
                $r['created_at'] = $now;
                $r['updated_at'] = $now;
                $products[] = $r;
            }
        };

        // ─── Toko 1 — Toko Maju Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-1')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Makanan & Minuman'), [
            ['name' => 'Indomie Goreng', 'sku' => 'IMG-001', 'price' => 3500, 'cost_price' => 2800, 'stock' => 200],
            ['name' => 'Aqua 600ml', 'sku' => 'AQU-001', 'price' => 4000, 'cost_price' => 3000, 'stock' => 150],
            ['name' => 'Teh Botol Sosro', 'sku' => 'TBS-001', 'price' => 5000, 'cost_price' => 3800, 'stock' => 100],
        ]);
        $add($t->id, $cat('Elektronik'), [
            ['name' => 'Charger USB-C 65W', 'sku' => 'CHG-065', 'price' => 150000, 'cost_price' => 95000, 'stock' => 30],
            ['name' => 'Kabel HDMI 2m', 'sku' => 'HDM-002', 'price' => 75000, 'cost_price' => 45000, 'stock' => 25],
        ]);
        $add($t->id, $cat('Pakaian'), [
            ['name' => 'Kaos Polos Putih L', 'sku' => 'KPS-W-L', 'price' => 65000, 'cost_price' => 40000, 'stock' => 50],
            ['name' => 'Kaos Polos Hitam M', 'sku' => 'KPS-B-M', 'price' => 65000, 'cost_price' => 40000, 'stock' => 45],
        ]);
        $add($t->id, $cat('Perlengkapan Rumah'), [
            ['name' => 'Sapu Lantai', 'sku' => 'SAP-001', 'price' => 35000, 'cost_price' => 22000, 'stock' => 20],
        ]);
        $add($t->id, $cat('Alat Tulis'), [
            ['name' => 'Pulpen Hitam (1 pak)', 'sku' => 'PEN-BLK', 'price' => 25000, 'cost_price' => 15000, 'stock' => 100],
            ['name' => 'Buku Tulis 58 lembar', 'sku' => 'BUK-058', 'price' => 7500, 'cost_price' => 5000, 'stock' => 200],
        ]);

        // ─── Toko 2 — Warung Berkah Abadi ───
        $t = DB::table('tenants')->where('slug', 'toko-2')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Sembako'), [
            ['name' => 'Beras Premium 5kg', 'sku' => 'BRS-5KG', 'price' => 75000, 'cost_price' => 62000, 'stock' => 80],
            ['name' => 'Minyak Goreng 1L', 'sku' => 'MNY-1LT', 'price' => 18000, 'cost_price' => 14500, 'stock' => 60],
            ['name' => 'Gula Pasir 1kg', 'sku' => 'GUL-1KG', 'price' => 16000, 'cost_price' => 13000, 'stock' => 70],
        ]);
        $add($t->id, $cat('Minuman'), [
            ['name' => 'Teh Botol 350ml', 'sku' => 'TEH-350', 'price' => 6000, 'cost_price' => 4500, 'stock' => 120],
            ['name' => 'Aqua Galon 19L', 'sku' => 'AQG-019', 'price' => 20000, 'cost_price' => 15000, 'stock' => 30],
        ]);
        $add($t->id, $cat('Snack & Camilan'), [
            ['name' => 'Chitato 68g', 'sku' => 'CHT-068', 'price' => 12000, 'cost_price' => 9000, 'stock' => 90],
            ['name' => 'Oreo Original 137g', 'sku' => 'ORE-137', 'price' => 15000, 'cost_price' => 11500, 'stock' => 75],
        ]);

        // ─── Toko 3 — CV Sumber Rejeki ───
        $t = DB::table('tenants')->where('slug', 'toko-3')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Bahan Baku'), [
            ['name' => 'Besi Hollow 4x4 6m', 'sku' => 'BSH-446', 'price' => 185000, 'cost_price' => 145000, 'stock' => 40],
            ['name' => 'Cat Tembok 25kg', 'sku' => 'CAT-25K', 'price' => 420000, 'cost_price' => 340000, 'stock' => 15],
        ]);
        $add($t->id, $cat('Produk Jadi'), [
            ['name' => 'Kusen Pintu Aluminium', 'sku' => 'KSP-ALU', 'price' => 850000, 'cost_price' => 600000, 'stock' => 10],
            ['name' => 'Pintu Besi Minimalis', 'sku' => 'PIN-BSI', 'price' => 1250000, 'cost_price' => 900000, 'stock' => 5],
        ]);

        // ─── Toko 4 — Apotek Sehat Sentosa ───
        $t = DB::table('tenants')->where('slug', 'toko-4')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Obat Bebas'), [
            ['name' => 'Paracetamol 500mg (strip)', 'sku' => 'OBT-PCT', 'price' => 5000, 'cost_price' => 3200, 'stock' => 200],
            ['name' => 'Antangin JRG (sachet)', 'sku' => 'OBT-ANT', 'price' => 3500, 'cost_price' => 2500, 'stock' => 150],
            ['name' => 'Promag (strip)', 'sku' => 'OBT-PMG', 'price' => 6500, 'cost_price' => 4500, 'stock' => 120],
        ]);
        $add($t->id, $cat('Vitamin & Suplemen'), [
            ['name' => 'Vitamin C 1000mg (botol)', 'sku' => 'VIT-C1K', 'price' => 45000, 'cost_price' => 32000, 'stock' => 80],
            ['name' => 'Suplemen Imun Enervon-C', 'sku' => 'VIT-ENV', 'price' => 35000, 'cost_price' => 24000, 'stock' => 60],
        ]);
        $add($t->id, $cat('Alat Kesehatan'), [
            ['name' => 'Termometer Digital', 'sku' => 'ALK-TRM', 'price' => 85000, 'cost_price' => 55000, 'stock' => 30],
            ['name' => 'Masker Medis (50pcs)', 'sku' => 'ALK-MSK', 'price' => 35000, 'cost_price' => 22000, 'stock' => 100],
        ]);
        $add($t->id, $cat('Perawatan Tubuh'), [
            ['name' => 'Hansaplast 100pcs', 'sku' => 'PRT-HSP', 'price' => 25000, 'cost_price' => 17000, 'stock' => 50],
        ]);

        // ─── Toko 5 — Toko Bangunan Kokoh ───
        $t = DB::table('tenants')->where('slug', 'toko-5')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Semen & Mortar'), [
            ['name' => 'Semen Tiga Roda 50kg', 'sku' => 'SMN-TR50', 'price' => 68000, 'cost_price' => 55000, 'stock' => 100],
            ['name' => 'Mortar Utama MU 380 40kg', 'sku' => 'MRT-MU38', 'price' => 95000, 'cost_price' => 78000, 'stock' => 50],
        ]);
        $add($t->id, $cat('Besi & Baja'), [
            ['name' => 'Besi Beton Ulir D10 12m', 'sku' => 'BSI-D10', 'price' => 125000, 'cost_price' => 98000, 'stock' => 60],
            ['name' => 'Besi Hollow 4x4 6m', 'sku' => 'HLW-446', 'price' => 195000, 'cost_price' => 155000, 'stock' => 40],
        ]);
        $add($t->id, $cat('Cat & Pelapis'), [
            ['name' => 'Cat Tembok Dulux 25kg', 'sku' => 'CAT-DLX', 'price' => 550000, 'cost_price' => 430000, 'stock' => 20],
            ['name' => 'Cat Kayu Avian 1kg', 'sku' => 'CAT-AVN', 'price' => 75000, 'cost_price' => 55000, 'stock' => 35],
        ]);
        $add($t->id, $cat('Pipa & Sanitasi'), [
            ['name' => 'Pipa PVC 4 inch 4m', 'sku' => 'PPA-4IN', 'price' => 115000, 'cost_price' => 88000, 'stock' => 30],
        ]);
        $add($t->id, $cat('Keramik & Granit'), [
            ['name' => 'Keramik Lantai 60x60 (dus)', 'sku' => 'KRM-6060', 'price' => 185000, 'cost_price' => 140000, 'stock' => 25],
        ]);

        // ─── Toko 6 — UD Elektronik Mandiri ───
        $t = DB::table('tenants')->where('slug', 'toko-6')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Handphone & Tablet'), [
            ['name' => 'Samsung Galaxy A15 4G', 'sku' => 'HP-SGA15', 'price' => 2199000, 'cost_price' => 1850000, 'stock' => 10],
            ['name' => 'Xiaomi Redmi 13C', 'sku' => 'HP-RDM13', 'price' => 1499000, 'cost_price' => 1250000, 'stock' => 15],
        ]);
        $add($t->id, $cat('Aksesoris HP'), [
            ['name' => 'Tempered Glass Universal', 'sku' => 'AKS-TG', 'price' => 25000, 'cost_price' => 12000, 'stock' => 200],
            ['name' => 'Case Silikon Anti-Crack', 'sku' => 'AKS-CSL', 'price' => 30000, 'cost_price' => 15000, 'stock' => 150],
            ['name' => 'Charger Micro USB 2A', 'sku' => 'AKS-CHG', 'price' => 35000, 'cost_price' => 22000, 'stock' => 80],
        ]);
        $add($t->id, $cat('Kabel & Adaptor'), [
            ['name' => 'Kabel Data USB-C 1m', 'sku' => 'KBL-USBC', 'price' => 45000, 'cost_price' => 28000, 'stock' => 100],
            ['name' => 'Adaptor 3-in-1', 'sku' => 'KBL-ADP3', 'price' => 55000, 'cost_price' => 35000, 'stock' => 60],
        ]);
        $add($t->id, $cat('Audio & Speaker'), [
            ['name' => 'Earphone JBL T110', 'sku' => 'AUD-JBL', 'price' => 195000, 'cost_price' => 145000, 'stock' => 25],
        ]);

        // ─── Toko 7 — Butik Mode Terkini ───
        $t = DB::table('tenants')->where('slug', 'toko-7')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Pakaian Wanita'), [
            ['name' => 'Dress Batik Tulis Premium', 'sku' => 'PWN-DBT', 'price' => 350000, 'cost_price' => 220000, 'stock' => 20],
            ['name' => 'Blouse Kekinian Crinkle', 'sku' => 'PWN-BLC', 'price' => 120000, 'cost_price' => 75000, 'stock' => 35],
        ]);
        $add($t->id, $cat('Pakaian Pria'), [
            ['name' => 'Kemeja Oxford Slim Fit', 'sku' => 'PPR-KMS', 'price' => 185000, 'cost_price' => 110000, 'stock' => 30],
            ['name' => 'Celana Chino Panjang', 'sku' => 'PPR-CLC', 'price' => 210000, 'cost_price' => 135000, 'stock' => 25],
        ]);
        $add($t->id, $cat('Pakaian Anak'), [
            ['name' => 'Setelan Anak Karakter', 'sku' => 'PAK-STL', 'price' => 95000, 'cost_price' => 60000, 'stock' => 40],
        ]);
        $add($t->id, $cat('Tas & Dompet'), [
            ['name' => 'Tas Selempang Wanita', 'sku' => 'TAS-SLM', 'price' => 275000, 'cost_price' => 165000, 'stock' => 15],
            ['name' => 'Dompet Kulit Pria', 'sku' => 'TAS-DPT', 'price' => 155000, 'cost_price' => 90000, 'stock' => 20],
        ]);

        // ─── Toko 8 — Minimarket Segar Prima ───
        $t = DB::table('tenants')->where('slug', 'toko-8')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Bahan Makanan Segar'), [
            ['name' => 'Telur Ayam (per 10)', 'sku' => 'SGR-TLR', 'price' => 28000, 'cost_price' => 22000, 'stock' => 100],
            ['name' => 'Ayam Broiler 1kg', 'sku' => 'SGR-AYM', 'price' => 35000, 'cost_price' => 28000, 'stock' => 50],
        ]);
        $add($t->id, $cat('Minuman Kemasan'), [
            ['name' => 'Aqua 1500ml', 'sku' => 'MNK-AQ15', 'price' => 7000, 'cost_price' => 5000, 'stock' => 150],
            ['name' => 'Pocari Sweat 500ml', 'sku' => 'MNK-POC', 'price' => 8500, 'cost_price' => 6500, 'stock' => 120],
        ]);
        $add($t->id, $cat('Makanan Instan'), [
            ['name' => 'Indomie Goreng', 'sku' => 'MKI-IMG', 'price' => 3500, 'cost_price' => 2800, 'stock' => 300],
            ['name' => 'Pop Mie Ayam', 'sku' => 'MKI-PMI', 'price' => 6000, 'cost_price' => 4500, 'stock' => 200],
        ]);
        $add($t->id, $cat('Kebersihan & Sanitasi'), [
            ['name' => 'Sabun Cuci Sunlight 800ml', 'sku' => 'KBS-SUN', 'price' => 22000, 'cost_price' => 16000, 'stock' => 80],
            ['name' => 'Wipol Karbol 750ml', 'sku' => 'KBS-WPL', 'price' => 18000, 'cost_price' => 13000, 'stock' => 60],
        ]);

        // ─── Toko 9 — Bengkel Motor Jaya ───
        $t = DB::table('tenants')->where('slug', 'toko-9')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Oli & Pelumas'), [
            ['name' => 'Oli Mesin Federal 10W-40 1L', 'sku' => 'OLI-FDR', 'price' => 48000, 'cost_price' => 37000, 'stock' => 80],
            ['name' => 'Oli Mesin Shell 20W-50 1L', 'sku' => 'OLI-SHL', 'price' => 55000, 'cost_price' => 43000, 'stock' => 60],
        ]);
        $add($t->id, $cat('Spare Part Motor'), [
            ['name' => 'Kampas Rem Depan Honda Beat', 'sku' => 'SPR-KRM', 'price' => 65000, 'cost_price' => 45000, 'stock' => 50],
            ['name' => 'Filter Udara Honda Beat', 'sku' => 'SPR-FLT', 'price' => 45000, 'cost_price' => 32000, 'stock' => 40],
            ['name' => 'Busi NGK Motor Matic', 'sku' => 'SPR-BSI', 'price' => 35000, 'cost_price' => 24000, 'stock' => 60],
        ]);
        $add($t->id, $cat('Ban & Velg'), [
            ['name' => 'Ban Tubeless IRC 80/90-14', 'sku' => 'BAN-IRC', 'price' => 195000, 'cost_price' => 150000, 'stock' => 20],
        ]);
        $add($t->id, $cat('Aksesoris Motor'), [
            ['name' => 'Kaca Spion Variasi', 'sku' => 'AKM-SPR', 'price' => 85000, 'cost_price' => 55000, 'stock' => 25],
        ]);

        // ─── Toko 10 — Toko Peralatan Dapur ───
        $t = DB::table('tenants')->where('slug', 'toko-10')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Peralatan Masak'), [
            ['name' => 'Wajan Anti Lengket 28cm', 'sku' => 'MSK-WJN', 'price' => 185000, 'cost_price' => 125000, 'stock' => 30],
            ['name' => 'Panci Presto 5 Liter', 'sku' => 'MSK-PRS', 'price' => 350000, 'cost_price' => 240000, 'stock' => 15],
            ['name' => 'Spatula Kayu Set (3pcs)', 'sku' => 'MSK-SPT', 'price' => 45000, 'cost_price' => 28000, 'stock' => 50],
        ]);
        $add($t->id, $cat('Wadah & Penyimpanan'), [
            ['name' => 'Toples Kaca Tutup Klip 1L', 'sku' => 'WDH-TPS', 'price' => 55000, 'cost_price' => 35000, 'stock' => 40],
            ['name' => 'Wadah Plastik Sealware Set', 'sku' => 'WDH-SWR', 'price' => 75000, 'cost_price' => 50000, 'stock' => 35],
        ]);
        $add($t->id, $cat('Peralatan Makan'), [
            ['name' => 'Piring Melamin Set 6pcs', 'sku' => 'MKN-PLR', 'price' => 95000, 'cost_price' => 65000, 'stock' => 25],
        ]);
        $add($t->id, $cat('Peralatan Listrik Dapur'), [
            ['name' => 'Rice Cooker Cosmos 1.8L', 'sku' => 'LST-RC18', 'price' => 245000, 'cost_price' => 180000, 'stock' => 12],
            ['name' => 'Blender Philips 2L', 'sku' => 'LST-BLD', 'price' => 395000, 'cost_price' => 290000, 'stock' => 8],
        ]);

        // ─── Toko 11 — Koperasi Makmur Bersama ───
        $t = DB::table('tenants')->where('slug', 'toko-11')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Kebutuhan Pokok'), [
            ['name' => 'Beras Pandan Wangi 5kg', 'sku' => 'KOP-BRS', 'price' => 78000, 'cost_price' => 65000, 'stock' => 60],
            ['name' => 'Gula Pasir 1kg', 'sku' => 'KOP-GUL', 'price' => 16500, 'cost_price' => 13500, 'stock' => 80],
            ['name' => 'Minyak Goreng 2L', 'sku' => 'KOP-MNY', 'price' => 32000, 'cost_price' => 26000, 'stock' => 50],
        ]);
        $add($t->id, $cat('Peralatan Rumah'), [
            ['name' => 'Ember Plastik 15L', 'sku' => 'KOP-EMB', 'price' => 28000, 'cost_price' => 18000, 'stock' => 30],
            ['name' => 'Sapu Ijuk', 'sku' => 'KOP-SPU', 'price' => 22000, 'cost_price' => 14000, 'stock' => 25],
        ]);
        $add($t->id, $cat('Alat Tulis Kantor'), [
            ['name' => 'Buku Tulis 100 lembar', 'sku' => 'KOP-BUK', 'price' => 12000, 'cost_price' => 8500, 'stock' => 100],
            ['name' => 'Pulpen Pilot Hitam', 'sku' => 'KOP-PLN', 'price' => 7000, 'cost_price' => 4500, 'stock' => 150],
        ]);

        // ─── Toko 12 — Toko Olahraga Sporty ───
        $t = DB::table('tenants')->where('slug', 'toko-12')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Pakaian Olahraga'), [
            ['name' => 'Jersey Futsal Dry-Fit', 'sku' => 'SPT-JRS', 'price' => 155000, 'cost_price' => 95000, 'stock' => 40],
            ['name' => 'Celana Training Panjang', 'sku' => 'SPT-CLT', 'price' => 125000, 'cost_price' => 78000, 'stock' => 35],
        ]);
        $add($t->id, $cat('Sepatu Olahraga'), [
            ['name' => 'Sepatu Badminton RS X-Lite', 'sku' => 'SPT-SBD', 'price' => 450000, 'cost_price' => 320000, 'stock' => 15],
            ['name' => 'Sepatu Running Mizuno', 'sku' => 'SPT-SRN', 'price' => 680000, 'cost_price' => 490000, 'stock' => 10],
        ]);
        $add($t->id, $cat('Peralatan Gym'), [
            ['name' => 'Dumbbell 5kg (pasang)', 'sku' => 'SPT-DBL', 'price' => 195000, 'cost_price' => 140000, 'stock' => 20],
            ['name' => 'Matras Yoga 6mm', 'sku' => 'SPT-MAT', 'price' => 165000, 'cost_price' => 115000, 'stock' => 25],
        ]);
        $add($t->id, $cat('Suplemen Olahraga'), [
            ['name' => 'Whey Protein 1kg', 'sku' => 'SPT-WHY', 'price' => 380000, 'cost_price' => 270000, 'stock' => 12],
        ]);

        // ─── Toko 13 — Percetakan Digital Cepat ───
        $t = DB::table('tenants')->where('slug', 'toko-13')->first();
        $cat = fn(string $n) => DB::table('categories')->where('tenant_id', $t->id)->where('name', $n)->first()->id;

        $add($t->id, $cat('Bahan Cetak'), [
            ['name' => 'Kertas HVS A4 70gr (rim)', 'sku' => 'BCT-HVS', 'price' => 55000, 'cost_price' => 42000, 'stock' => 100],
            ['name' => 'Kertas Foto Glossy A4', 'sku' => 'BCT-GLS', 'price' => 5000, 'cost_price' => 3200, 'stock' => 500],
            ['name' => 'Tinta Epson Pigment (set)', 'sku' => 'BCT-TNT', 'price' => 185000, 'cost_price' => 135000, 'stock' => 30],
        ]);
        $add($t->id, $cat('Produk Cetak'), [
            ['name' => 'Brosur A5 full color (100)', 'sku' => 'PCT-BRS', 'price' => 150000, 'cost_price' => 90000, 'stock' => 0, 'track_stock' => false],
            ['name' => 'Spanduk Banner 1x2m', 'sku' => 'PCT-SPD', 'price' => 85000, 'cost_price' => 50000, 'stock' => 0, 'track_stock' => false],
            ['name' => 'Kartu Nama 100pcs', 'sku' => 'PCT-KNM', 'price' => 45000, 'cost_price' => 25000, 'stock' => 0, 'track_stock' => false],
        ]);
        $add($t->id, $cat('Souvenir & Merchandise'), [
            ['name' => 'Mug Sablon Custom', 'sku' => 'SOU-MUG', 'price' => 55000, 'cost_price' => 35000, 'stock' => 50],
            ['name' => 'Kaos Sablon DTF', 'sku' => 'SOU-KOS', 'price' => 95000, 'cost_price' => 60000, 'stock' => 30],
        ]);

        DB::table('products')->insert($products);
    }
}