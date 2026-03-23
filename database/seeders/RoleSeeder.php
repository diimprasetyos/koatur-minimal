<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Buat Roles ────────────────────────────────────────
        // Permissions di-handle oleh Shield via:
        // php artisan shield:generate --all
        // Assign permissions lewat UI /superadmin → Roles

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'owner',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'manager',     'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir',       'guard_name' => 'web']);

        $this->command->info('✅ Roles berhasil dibuat.');

    }
}
