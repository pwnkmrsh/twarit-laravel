<?php

namespace App;

class Language extends AbstractModel
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where("active", 1);
    }
}
