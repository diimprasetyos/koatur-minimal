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
        $tenants = DB::table('tenants')
            ->where('slug', 'like', 'toko-%')
            ->orderBy('id')
            ->get();

        $now = now();
        $users = [];

        // ─────────────────────────────────────────────
        // SUPER ADMIN (tidak terikat tenant tertentu)
        // ─────────────────────────────────────────────
        $users[] = [
            'uuid' => Str::uuid(),
            'current_tenant_id' => $tenants[0]->id,
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'subscription_plan' => 'pro',
            'is_active' => true,
            'remember_token' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // ─────────────────────────────────────────────
        // 13 OWNER — masing-masing milik 1 tenant
        // ─────────────────────────────────────────────
        foreach ($tenants as $index => $tenant) {
            $no = $index + 1;
            $users[] = [
                'uuid' => Str::uuid(),
                'current_tenant_id' => $tenant->id,
                'name' => 'Owner Toko ' . $no,
                'email' => 'owner.toko' . $no . '@test.com',
                'password' => Hash::make('owner123'),
                'subscription_plan' => 'pro',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($users);

        // ─────────────────────────────────────────────
        // PIVOT tenant_user
        // ─────────────────────────────────────────────
        $pivots = [];
        foreach ($tenants as $tenant) {
            $owner = DB::table('users')
                ->where('current_tenant_id', $tenant->id)
                ->where('email', 'like', 'owner.%')
                ->first();

            if ($owner) {
                $pivots[] = ['tenant_id' => $tenant->id, 'user_id' => $owner->id];
            }
        }

        DB::table('tenant_user')->insert($pivots);
    }
}