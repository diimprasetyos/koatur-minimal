<?php

namespace App\Filament\Superadmin\Resources\Users\Utils;

use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionPlan;

trait HandleSubscription
{
    /**
     * Isi field subscription di form dari data subscription aktif user.
     */
    protected function fillSubscriptionData(array $data, $record): array
    {
        $sub = $record->subscriptions()->latest()->first();

        if ($sub) {
            $data['subscription_plan_id']  = $sub->subscription_plan_id;
            $data['subscription_status']   = $sub->status;
            $data['subscription_started_at'] = $sub->started_at?->format('Y-m-d H:i:s');
            $data['subscription_expires_at'] = $sub->expires_at?->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Simpan perubahan subscription setelah form disimpan.
     * Kalau sudah ada subscription → update. Kalau belum → buat baru.
     */
    protected function saveSubscription(array $data, $record): void
    {
        // Kalau plan tidak dipilih, skip
        if (blank($data['subscription_plan_id'] ?? null)) {
            return;
        }

        $sub = $record->subscriptions()->latest()->first();

        $payload = [
            'subscription_plan_id' => $data['subscription_plan_id'],
            'status'               => $data['subscription_status'] ?? Subscription::STATUS_ACTIVE,
            'started_at'           => $data['subscription_started_at'] ?? now(),
            'expires_at'           => $data['subscription_expires_at'] ?? null,
        ];

        if ($sub) {
            $sub->update($payload);
        } else {
            $record->subscriptions()->create($payload);
        }
    }
}
