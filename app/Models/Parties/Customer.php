<?php

namespace App\Models\Parties;

use App\Models\Sales\Sale;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasUuid, BelongsToTenant;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'phone',
        'email',
        'address',
        'loyalty_points',
    ];

    protected $casts = [
        'loyalty_points' => 'integer',
    ];

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
