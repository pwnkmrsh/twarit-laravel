<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends AbstractModel
{
    protected $fillable = ['title', 'description', 'text', 'slug', 'footer'];
}
