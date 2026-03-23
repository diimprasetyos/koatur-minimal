<?php

namespace App\Models\Product;

use App\Models\Adjustment\StockMovement;
use App\Models\Sales\SaleItem;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasUuid, BelongsToTenant;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'category_id',
        'name',
        'sku',
        'description',
        'image',
        'price',
        'cost_price',
        'stock',
        'track_stock',
        'is_active',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'cost_price'  => 'decimal:2',
        'track_stock' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function isInStock(): bool
    {
        return ! $this->track_stock || $this->stock > 0;
    }

    public function decreaseStock(int $qty): void
    {
        if ($this->track_stock) {
            $this->decrement('stock', $qty);
        }
    }

    public function increaseStock(int $qty): void
    {
        if ($this->track_stock) {
            $this->increment('stock', $qty);
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
