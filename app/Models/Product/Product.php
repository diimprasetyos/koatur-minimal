<?php

namespace App\Models\Product;

use App\Models\Adjustment\StockMovement;
use App\Models\Sales\SaleItem;
use App\Models\Tenant;
use App\Traits\BelongsToTenant;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ─── Auto-generate SKU ────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $product) {
            if (empty($product->sku)) {
                $product->sku = static::generateSku();
            }
        });
    }

    public static function generateSku(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('sku', $sku)->exists());

        return $sku;
    }

    // ─── Stock Helpers ────────────────────────────────────────────

    public function isInStock(): bool
    {
        return !$this->track_stock || $this->stock > 0;
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

    // ─── Relationships ────────────────────────────────────────────

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

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,
            $this->getTable(),
            'id',
            'tenant_id',
            'id',
            'id',
        );
    }
}