<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $now = now();

        $customers = [
            // Tenant 1
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Agus Prasetyo', 'code' => 'CUST-001', 'phone' => '081234567890', 'email' => 'agus@gmail.com', 'address' => 'Jl. Rungkut No. 12, Surabaya', 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Dewi Rahayu', 'code' => 'CUST-002', 'phone' => '082345678901', 'email' => 'dewi@yahoo.com', 'address' => 'Jl. Gubeng No. 7, Surabaya', 'contact_person' => null, 'payable_amount' => 150000, 'is_active' => true, 'notes' => 'Pelanggan langganan bulanan'],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Toko Sinar Mas', 'code' => 'CUST-003', 'phone' => '031-5551234', 'email' => null, 'address' => 'Jl. Semolowaru No. 55, Surabaya', 'contact_person' => 'Pak Mas', 'payable_amount' => 500000, 'is_active' => true, 'notes' => 'Bayar di akhir bulan'],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Fitria Handayani', 'code' => 'CUST-004', 'phone' => '083456789012', 'email' => 'fitria@gmail.com', 'address' => 'Jl. Kenjeran No. 3, Surabaya', 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant1->id, 'name' => 'Yusuf Maulana', 'code' => 'CUST-005', 'phone' => '084567890123', 'email' => null, 'address' => null, 'contact_person' => null, 'payable_amount' => 0, 'is_active' => false, 'notes' => 'Tidak aktif'],

            // Tenant 2
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Rina Marlina', 'code' => 'C-001', 'phone' => '085678901234', 'email' => 'rina@gmail.com', 'address' => 'Jl. Ahmad Yani No. 99, Sidoarjo', 'contact_person' => null, 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Bambang Susilo', 'code' => 'C-002', 'phone' => '086789012345', 'email' => null, 'address' => 'Jl. Pahlawan No. 14, Sidoarjo', 'contact_person' => null, 'payable_amount' => 75000, 'is_active' => true, 'notes' => null],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant2->id, 'name' => 'Kantin Sekolah SMK 1', 'code' => 'C-003', 'phone' => '031-8889900', 'email' => null, 'address' => 'Jl. Mojopahit No. 5, Sidoarjo', 'contact_person' => 'Bu Ani', 'payable_amount' => 250000, 'is_active' => true, 'notes' => 'Order mingguan tiap Senin'],

            // Tenant 3
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'PT Graha Bangun Jaya', 'code' => 'CLT-001', 'phone' => '031-7778880', 'email' => 'procurement@graha.co.id', 'address' => 'Jl. HR Muhammad No. 10, Surabaya', 'contact_person' => 'Ibu Sari', 'payable_amount' => 2500000, 'is_active' => true, 'notes' => 'Klien kontrak tahunan'],
            ['uuid' => Str::uuid(), 'tenant_id' => $tenant3->id, 'name' => 'CV Mitra Konstruksi', 'code' => 'CLT-002', 'phone' => '085890123456', 'email' => null, 'address' => 'Jl. Wonorejo No. 22, Surabaya', 'contact_person' => 'Pak Dodi', 'payable_amount' => 0, 'is_active' => true, 'notes' => null],
        ];

        foreach ($customers as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }

        DB::table('customers')->insert($customers);
    }
}