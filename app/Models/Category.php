<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = [];

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = empty($value) ? null : $value;
    }

    public function products(): HasMany|Category
    {
        return $this->hasMany(Product::class);
    }
}
