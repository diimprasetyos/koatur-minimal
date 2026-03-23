<?php

namespace App\Models\Product;

use App\Models\Product\Product;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasUuid, BelongsToTenant;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
