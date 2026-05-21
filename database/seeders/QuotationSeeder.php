<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $t   = DB::table('tenants')->where('slug', 'toko-1')->first();
        $u   = DB::table('users')->where('email', 'owner@test.com')->first();

        $prod = fn(string $sku)  => DB::table('products')->where('sku', $sku)->first();
        $cust = fn(string $name) => DB::table('customers')
            ->where('tenant_id', $t->id)->where('name', $name)->first();

        // ── Quotation 1 — Toko Sinar Mas, ATK bulk, menunggu konfirmasi ──
        $q1 = DB::table('quotations')->insertGetId([
            'uuid'            => Str::uuid(),
            'tenant_id'       => $t->id,
            'user_id'         => $u->id,
            'customer_id'     => $cust('Toko Sinar Mas')->id,
            'code'            => 'QUO-T1-20260318-001',   // kolom: code (bukan quotation_number)
            'status'          => 'sent',
            'valid_until'     => '2026-04-18',             // kolom: valid_until (bukan expiry_date)
            'notes'           => 'Harga berlaku 30 hari, diskon grosir 4%',
            'discount_amount' => 25000,                    // kolom: discount_amount (bukan discount)
            'tax_amount'      => 0,                        // kolom: tax_amount (bukan tax)
            'total_amount'    => 600000,                   // kolom: total_amount (bukan total)
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        DB::table('quotation_items')->insert([
            [
                'quotation_id' => $q1,
                'product_id'   => $prod('PEN-BLK')->id,
                'product_name' => $prod('PEN-BLK')->name,
                'quantity'     => 15,                      // kolom: quantity (bukan qty)
                'price'        => 25000,
                'subtotal'     => 375000,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'quotation_id' => $q1,
                'product_id'   => $prod('BUK-058')->id,
                'product_name' => $prod('BUK-058')->name,
                'quantity'     => 25,
                'price'        => 7500,
                'subtotal'     => 187500,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'quotation_id' => $q1,
                'product_id'   => $prod('SAP-001')->id,
                'product_name' => $prod('SAP-001')->name,
                'quantity'     => 2,
                'price'        => 35000,
                'subtotal'     => 70000,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);

        // ── Quotation 2 — Fitria Handayani, elektronik, draft ──
        $q2 = DB::table('quotations')->insertGetId([
            'uuid'            => Str::uuid(),
            'tenant_id'       => $t->id,
            'user_id'         => $u->id,
            'customer_id'     => $cust('Fitria Handayani')->id,
            'code'            => 'QUO-T1-20260323-001',
            'status'          => 'draft',
            'valid_until'     => '2026-04-23',
            'notes'           => null,
            'discount_amount' => 0,
            'tax_amount'      => 0,
            'total_amount'    => 225000,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        DB::table('quotation_items')->insert([
            [
                'quotation_id' => $q2,
                'product_id'   => $prod('CHG-065')->id,
                'product_name' => $prod('CHG-065')->name,
                'quantity'     => 1,
                'price'        => 150000,
                'subtotal'     => 150000,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'quotation_id' => $q2,
                'product_id'   => $prod('HDM-002')->id,
                'product_name' => $prod('HDM-002')->name,
                'quantity'     => 1,
                'price'        => 75000,
                'subtotal'     => 75000,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);
    }
}
