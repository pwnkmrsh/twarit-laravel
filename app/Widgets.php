<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class Widgets extends AbstractModel
{
    protected $table = 'widgets';

    protected $fillable = ['key', 'text', 'display', 'showweb', 'showmobile', 'type'];

    /**
     * Get cached widgets.
     */
    public function scopeGetCached($query, $key, $cacheTime = 600000)
    {
        $cache_key = 'widget_' . $key;
        $widget = Cache::get($cache_key);

        if (!empty($widget)) {
            return $widget;
        }

        $widget = $query->get();

        Cache::put($cache_key, $widget, $cacheTime);

        return $widget;
    }
}
