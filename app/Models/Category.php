<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = empty($value) ? null : $value;
    }
}
