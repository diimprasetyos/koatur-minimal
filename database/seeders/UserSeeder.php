<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = DB::table('tenants')->where('slug', 'toko-elektronik')->first();

        $now = now();

        $users = [
            // ── Super Admin ───────────────────────────
            [
                'uuid'              => Str::uuid(),
                'current_tenant_id' => $tenant->id,
                'name'              => 'Super Admin',
                'email'             => 'admin@test.com',
                'password'          => Hash::make('admin123'),
                'subscription_plan' => 'pro',
                'is_active'         => true,
                'remember_token'    => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            // ── Owner ─────────────────────────────────
            [
                'uuid'              => Str::uuid(),
                'current_tenant_id' => $tenant->id,
                'name'              => 'Owner Toko 1',
                'email'             => 'owner@test.com',
                'password'          => Hash::make('owner123'),
                'subscription_plan' => 'trial',
                'is_active'         => true,
                'remember_token'    => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        DB::table('users')->insert($users);

        // ── Pivot tenant_user untuk owner ─────────────
        $owner = DB::table('users')->where('email', 'owner@test.com')->first();

        DB::table('tenant_user')->insert([
            'tenant_id' => $tenant->id,
            'user_id'   => $owner->id,
        ]);
    }
}
