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
        $tenant1 = DB::table('tenants')->where('slug', 'toko-maju-jaya')->first();
        $tenant2 = DB::table('tenants')->where('slug', 'warung-berkah-abadi')->first();
        $tenant3 = DB::table('tenants')->where('slug', 'cv-sumber-rejeki')->first();

        $users = [
            [
                'uuid' => Str::uuid(),
                'current_tenant_id' => $tenant1->id,
                'name' => 'Super Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('admin123'),
                'subscription_plan' => 'pro',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'current_tenant_id' => $tenant1->id,
                'name' => 'Admin Utama',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'subscription_plan' => 'pro',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'current_tenant_id' => $tenant1->id,
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => Hash::make('password'),
                'subscription_plan' => 'basic',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'current_tenant_id' => $tenant2->id,
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'password' => Hash::make('password'),
                'subscription_plan' => 'basic',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'current_tenant_id' => $tenant3->id,
                'name' => 'Hendra Wijaya',
                'email' => 'hendra@example.com',
                'password' => Hash::make('password'),
                'subscription_plan' => 'pro',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        // Pivot tenant_user
        $user1 = DB::table('users')->where('email', 'admin@example.com')->first();
        $user2 = DB::table('users')->where('email', 'budi@example.com')->first();
        $user3 = DB::table('users')->where('email', 'siti@example.com')->first();
        $user4 = DB::table('users')->where('email', 'hendra@example.com')->first();

        DB::table('tenant_user')->insert([
            ['tenant_id' => $tenant1->id, 'user_id' => $user1->id],
            ['tenant_id' => $tenant1->id, 'user_id' => $user2->id],
            ['tenant_id' => $tenant2->id, 'user_id' => $user3->id],
            ['tenant_id' => $tenant3->id, 'user_id' => $user4->id],
            // Admin juga punya akses ke tenant 2 (contoh multi-tenant)
            ['tenant_id' => $tenant2->id, 'user_id' => $user1->id],
        ]);
    }
}