<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = empty($value) ? null : $value;
    }
}
