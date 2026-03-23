<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // route model binding via UUID cth Route::get('/products/{product:uuid}', ...)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
