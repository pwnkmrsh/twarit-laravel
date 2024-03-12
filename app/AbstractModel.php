<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class AbstractModel extends Model
{
    use QueryCacheable;

    public $cacheFor = 3600;

    // @todo remove this when we have model specific cache tags
    protected static $flushCacheOnUpdate = true;
}
