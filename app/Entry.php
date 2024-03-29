<?php

namespace App;

use App\Managers\UploadManager;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'entries';

    protected $fillable = ['post_id', 'user_id', 'order', 'type', 'title', 'image', 'video', 'body', 'source', 'deleted_at'];

    protected $dates = ['deleted_at'];

    protected $softDelete = true;

    public function owner()
    {
        return $this->belongsTo("App\User");
    }

    /**
     * Post belongs to a posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    /**
     * Poll option has many votes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pollvotes()
    {
        return $this->hasMany('App\PollVotes', 'post_id');
    }

    /**
     * Poll option has many votes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vote()
    {
        return $this->hasMany('App\PollVotes', 'option_id');
    }

    /**
     * Entry has many files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMan
     */
    public function files()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function scopeByOrder($query, $type)
    {
        return $query->latest($type);
    }

    /**
     * Get entries by type
     *
     * @return mixed
     */
    public function scopeByType($query, $type)
    {
        if ($type == 'all') {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function forceDelete()
    {
        $this->forceDeleting = true;

        // @TODO move this to repository
        if (! empty($this->image)) {
            $imageM = new UploadManager();
            if ($this->type == 'answer') {
                $imageM->delete(makepreview($this->image, null, 'answers'));
            } else {
                $imageM->delete(makepreview($this->image, null, 'entries'));
            }
        }

        // delete answers
        if ($this->type == 'quizquestion' || $this->type == 'poll') {
            $entries = \App\Entry::withTrashed()->where('type', 'answer')->where('source', $this->id)->get();
            foreach ($entries as $entryh) {
                $entryh->forceDelete();
            }
        }

        $this->delete();

        $this->forceDeleting = false;
    }
}
