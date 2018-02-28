<?php

namespace App\Models;

use App\Models\Favorite;

use Carbon\Carbon;


class Reply extends Model
{
    use Favoritable, RecordActivity;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function ($reply) {
            // if ($reply->isBest()) {
            //     $reply->thread->update(['best_reply_id' => null]);
            // }

            $reply->thread->decrement('replies_count');
        });
    }

    protected $with = ['owner', 'favorites'];

    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    public function owner()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Mentioned Users
     *
     * @return $matches user after @
     */
    public function mentionedUsers()
    {
        preg_match_all('/\@([\w\-\_]+)/', $this->body, $matches);

        return $matches[1];
    }
    /**
     * Was just published
     * 
     * @return $this->created_at
     */
    public function wasJustPublished()          
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    /**
     * Path
     *
     * @return $thread
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    /**
     * SetBodyAttribute
     *
     * @param array $body Body from the input
     * 
     * @return void
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-\_]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }
}
