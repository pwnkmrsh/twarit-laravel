<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacts extends AbstractModel
{
    protected $table = 'contacts';

    protected $fillable = ['user_id', 'category_id', 'label_id', 'name', 'email', 'subject', 'display', 'read', 'stared', 'important'];

    public function category()
    {
        return $this->belongsTo('App\Tag', 'category_id');
    }

    public function label()
    {
        return $this->belongsTo('App\Tag', 'label_id');
    }

    public function getEmailAttribute($value)
    {
        if (env('APP_DEMO')) {
            $valore = explode("@", $value);
            return 'secretfordemo@' . $valore[1];
        }

        return $value;
    }
}
