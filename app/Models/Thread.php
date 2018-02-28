<?php

namespace App\Models;

use Laravel\Scout\Searchable;

use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
// use App\Visits;
use App\Models\Reply;

class Thread extends Model
{
    use RecordActivity, Searchable;

    protected $with = ['creator', 'channel'];

    protected $append = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update([ 'slug' => $thread->title ]);
        });

    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * add a reply to the thread
     *
     * @param array $reply
     * @return model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        // $this->notifySubscriber($reply);

        // event(new ThreadHasNewReply($this, $reply));

        // $this->subscriptions->filter(function($sub) use ($reply){
        //     return $sub->user_id != $reply->user_id;
        // })->each->notify($reply);

        return $reply;
    }
    
    // public function notifySubscriber($reply)
    // {
    //     // prepare notifications for all subscribers

    //     $this->subscriptions
    //     ->where('user_id', '!=', $reply->user_id)
    //     ->each->notify($reply);
    // }

    public function path()
    {
        return '/threads/'. $this->channel->name .'/'. $this->slug; 
    }

    public function scopeFilters($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * subscribe to the thread
     *
     * @param $userId
     * @return this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()->where(
            'user_id', $userId ?: auth()->id()
        )->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdatesFor($user)
    {
        // look inside the cache for proper key
        // compare the the key with carbon instance
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    // public function visits()
    // {
    //     return new Visits($this);
    // }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if(static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-". $this->id;
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
}
