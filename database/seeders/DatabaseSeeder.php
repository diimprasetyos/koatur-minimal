<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── 1. Roles & Permissions ────────────────────────────
        $this->call(RoleSeeder::class);

        // ── 2. Super Admin (TANPA tenant) ─────────────────────
        // super_admin hanya akses /superadmin, tidak perlu tenant_id
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );
        $superAdmin->syncRoles('super_admin');

        // ── 3. Tenant Demo ────────────────────────────────────
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo-store'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Demo Store',
                'slug' => 'demo-store',
                'phone' => '081234567890',
                'subscription_plan' => 'basic',
                'is_active' => true,
            ]
        );

        // ── 4. Users Tenant ───────────────────────────────────
        $owner = User::firstOrCreate(
            ['email' => 'owner@test.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Owner',
                'password' => Hash::make('owner123'),
                'is_active' => true,
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Manager',
                'password' => Hash::make('manager123'),
                'is_active' => true,
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@test.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Kasir',
                'password' => Hash::make('kasir123'),
                'is_active' => true,
            ]
        );

        // ── 5. Attach user tenant ke pivot (BUKAN super_admin) ─
        $tenant->users()->syncWithoutDetaching([
            $owner->id,
            $manager->id,
            $kasir->id,
        ]);

        // ── 6. Assign Roles ───────────────────────────────────
        $owner->syncRoles('owner');
        $manager->syncRoles('manager');
        $kasir->syncRoles('kasir');

        // ── Summary ───────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Seeder berhasil!');
        $this->command->table(
            ['Role', 'Panel', 'Email', 'Password'],
            [
                ['super_admin', '/superadmin', 'admin@test.com', 'admin123'],
                ['owner', '/admin', 'owner@test.com', 'owner123'],
                ['manager', '/admin', 'manager@test.com', 'manager123'],
                ['kasir', '/admin', 'kasir@test.com', 'kasir123'],
            ]
        );
    }
}
