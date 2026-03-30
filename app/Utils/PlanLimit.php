<?php

namespace App\Utils;

class PlanLimit
{
    /**
     * Definisi semua limit per plan.
     * -1 = unlimited
     */
    const LIMITS = [
        'basic' => [
            'max_tenants' => 1,
            'max_users' => 2,
            'label' => 'Basic',
            'color' => 'gray',
        ],
        'advance' => [
            'max_tenants' => 5,
            'max_users' => 15,
            'label' => 'Advance',
            'color' => 'info',
        ],
        'pro' => [
            'max_tenants' => -1,  // unlimited
            'max_users' => -1,  // unlimited
            'label' => 'Pro',
            'color' => 'success',
        ],
    ];

    public static function maxTenants(string $plan): int
    {
        return self::LIMITS[$plan]['max_tenants'] ?? 1;
    }

    public static function maxUsers(string $plan): int
    {
        return self::LIMITS[$plan]['max_users'] ?? 2;
    }

    public static function label(string $plan): string
    {
        return self::LIMITS[$plan]['label'] ?? 'Basic';
    }

    public static function color(string $plan): string
    {
        return self::LIMITS[$plan]['color'] ?? 'gray';
    }

    public static function isUnlimited(string $plan, string $key): bool
    {
        return (self::LIMITS[$plan][$key] ?? 1) === -1;
    }

    /**
     * Cek apakah user masih boleh buat tenant baru.
     */
    public static function canCreateTenant(\App\Models\User $user): bool
    {
        $max = self::maxTenants($user->subscription_plan ?? 'basic');

        if ($max === -1)
            return true;

        return $user->tenants()->count() < $max;
    }

    /**
     * Sisa kuota tenant.
     * Mengembalikan null jika unlimited.
     */
    public static function remainingTenants(\App\Models\User $user): ?int
    {
        $max = self::maxTenants($user->subscription_plan ?? 'basic');

        if ($max === -1)
            return null;

        return max(0, $max - $user->tenants()->count());
    }
}