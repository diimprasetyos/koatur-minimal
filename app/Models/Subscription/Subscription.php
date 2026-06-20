<?php

namespace App\Models\Subscription;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'started_at',
        'expires_at',
        'cancelled_at',
        'payment_reference',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'expires_at'   => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ─── Status Constants ─────────────────────────────────────────

    const STATUS_TRIAL     = 'trial';
    const STATUS_ACTIVE    = 'active';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PENDING   = 'pending';

    // ─── Relations ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Apakah subscription masih aktif atau trial yang belum expired?
     * Ini method utama yang dipakai middleware.
     */
    public function isActive(): bool
    {
        if (in_array($this->status, [self::STATUS_EXPIRED, self::STATUS_CANCELLED])) {
            return false;
        }

        // Kalau expires_at null, berarti tidak pernah expire (lifetime/manual)
        if ($this->expires_at === null) {
            return true;
        }

        return $this->expires_at->isFuture();
    }

    /**
     * Apakah sedang dalam masa trial?
     */
    public function isTrial(): bool
    {
        return $this->status === self::STATUS_TRIAL;
    }

    /**
     * Apakah sudah expired?
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Berapa hari tersisa? Null kalau tidak ada batas.
     */
    public function daysRemaining(): ?int
    {
        if ($this->expires_at === null) {
            return null;
        }

        if ($this->isExpired()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at);
    }

    /**
     * Aktifkan subscription setelah pembayaran berhasil.
     * Dipanggil dari webhook payment gateway.
     */
    public function activate(int $durationDays = 30, ?string $paymentReference = null): void
    {
        $this->update([
            'status'            => self::STATUS_ACTIVE,
            'started_at'        => now(),
            'expires_at'        => now()->addDays($durationDays),
            'payment_reference' => $paymentReference ?? $this->payment_reference,
        ]);
    }

    /**
     * Buat subscription trial baru untuk user yang baru register.
     * Dipanggil dari RegisterController.
     */
    public static function createTrial(User $user, int $trialDays = 7): self
    {
        $trialPlan = SubscriptionPlan::where('slug', 'trial')->firstOrFail();

        return self::create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $trialPlan->id,
            'status'               => self::STATUS_TRIAL,
            'started_at'           => now(),
            'expires_at'           => now()->addDays($trialDays),
        ]);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_TRIAL, self::STATUS_ACTIVE])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}