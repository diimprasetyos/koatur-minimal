<?php

namespace App\Models\Return;

use App\Models\Product\Product;
use App\Models\Sales\SaleItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sale_return_id',
        'product_id',
        'sale_item_id',
        'qty',
        'price',
        'subtotal',
        'reason',
    ];

    protected $casts = [
        'qty'      => 'integer',
        'price'    => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->subtotal = $item->qty * $item->price;
        });
    }

    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }
}
