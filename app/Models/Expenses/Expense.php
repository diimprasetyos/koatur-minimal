<?php

namespace App\Models\Expenses;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Expense extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'expense_category_id',
        'uuid',
        'reference_number',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'notes',
        'attachment',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid             ??= Str::uuid();
            $model->reference_number ??= self::generateReferenceNumber($model->tenant_id);
        });
    }

    // ─── Relations ────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExepenseCategory::class, 'expense_category_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateReferenceNumber(int $tenantId): string
    {
        $date  = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'EXP-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
