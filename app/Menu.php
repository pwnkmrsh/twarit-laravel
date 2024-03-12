<?php

namespace App;

class Menu extends AbstractModel
{
    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }
}
