<?php

namespace App\Models\Expenses;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = ['tenant_id', 'name', 'color'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,  // model Tenant
            $this->getTable(),          // pakai tabel model itu sendiri sebagai "pivot"
            'id',                       // FK ke model ini di "pivot"
            'tenant_id',                // FK ke tenant di "pivot"
            'id',                       // PK model ini
            'id',                       // PK tenant
        );
    }
}
