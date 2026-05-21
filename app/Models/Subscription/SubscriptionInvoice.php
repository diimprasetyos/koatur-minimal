<?php

namespace App\Models\Subscription;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'subscription_id',
        'user_id',
        'invoice_number',
        'amount',
        'status',
        'payment_method',
        'payment_token',
        'payment_url',
        'payment_response',
        'paid_at',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'amount'           => 'integer',
        'payment_response' => 'array',
        'paid_at'          => 'datetime',
        'period_start'     => 'datetime',
        'period_end'       => 'datetime',
    ];

    // ─── Status Constants ─────────────────────────────────────────

    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_FAILED   = 'failed';
    const STATUS_REFUNDED = 'refunded';

    // ─── Relations ───────────────────────────────────────────────

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Generate nomor invoice otomatis.
     * Format: INV-20260501-0001
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $sequence = $lastInvoice
            ? ((int) substr($lastInvoice->invoice_number, -4)) + 1
            : 1;

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Tandai invoice sebagai sudah dibayar.
     * Dipanggil dari webhook payment gateway.
     */
    public function markAsPaid(array $paymentResponse = []): void
    {
        $this->update([
            'status'           => self::STATUS_PAID,
            'paid_at'          => now(),
            'payment_response' => $paymentResponse,
        ]);
    }

    /**
     * Format jumlah ke rupiah.
     */
    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Apakah invoice ini sudah dibayar?
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
