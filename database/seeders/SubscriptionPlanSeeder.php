<?php

namespace Database\Seeders;

use App\Models\Subscription\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Seed data plan langganan.
     *
     * Jalankan dengan: php artisan db:seed --class=SubscriptionPlanSeeder
     * Atau tambahkan ke DatabaseSeeder.php supaya jalan bareng php artisan db:seed
     */
    public function run(): void
    {
        $plans = [
            [
                'slug'                 => 'trial',
                'name'                 => 'Trial',
                'description'          => 'Coba semua fitur gratis selama 3 hari',
                'price'                => 0,
                'billing_cycle'        => 'monthly',
                'max_tenants'          => 1,
                'max_users_per_tenant' => 3,
                'max_products'         => 50,
                'features'             => [
                    'pos'          => true,
                    'reports'      => true,
                    'export'       => false,
                    'multi_tenant' => false,
                    'priority_support' => false,
                ],
                'is_active' => true,
            ],
            [
                'slug'                 => 'basic',
                'name'                 => 'Basic',
                'description'          => 'Untuk UMKM dengan 1 toko',
                'price'                => 99000, // Rp 99.000/bulan
                'billing_cycle'        => 'monthly',
                'max_tenants'          => 1,
                'max_users_per_tenant' => 5,
                'max_products'         => 500,
                'features'             => [
                    'pos'              => true,
                    'reports'          => true,
                    'export'           => false,
                    'multi_tenant'     => false,
                    'priority_support' => false,
                ],
                'is_active' => true,
            ],
            [
                'slug'                 => 'pro',
                'name'                 => 'Pro',
                'description'          => 'Untuk bisnis dengan banyak toko dan fitur lengkap',
                'price'                => 149000, // Rp 149.000/bulan
                'billing_cycle'        => 'monthly',
                'max_tenants'          => 5,
                'max_users_per_tenant' => 20,
                'max_products'         => 0,
                'features'             => [
                    'pos'              => true,
                    'reports'          => true,
                    'export'           => true,
                    'multi_tenant'     => true,
                    'priority_support' => true,
                ],
                'is_active' => false,
            ],
        ];

        foreach ($plans as $plan) {
            // updateOrCreate supaya aman dijalankan berkali-kali
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('✓ SubscriptionPlan seeded: trial, basic, pro');
    }
}
