<?php

namespace App\Models\Return;

use App\Models\Product\Product;
use App\Models\Purchases\PurchaseItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'purchase_return_id',
        'product_id',
        'purchase_item_id',
        'qty',
        'cost_price',
        'subtotal',
        'reason',
    ];

    protected $casts = [
        'qty'        => 'integer',
        'cost_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->subtotal = $item->qty * $item->cost_price;
        });
    }

    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}
