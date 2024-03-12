<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends AbstractModel
{
    protected $guarded = [""];

    protected $dates = ['checked_at'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where("active", true);
    }
}
