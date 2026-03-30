<?php

namespace App\Models;

use App\Models\Adjustment\StockMovement;
use App\Models\Parties\Customer;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Sales\Sale;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'phone',
        'address',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot(); // wajib dipanggil

        static::creating(function (self $model) {
            // Generate slug dari name jika belum diisi.
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function (self $model) {
            // Jika name berubah dan slug masih kosong/null, regenerate.
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
