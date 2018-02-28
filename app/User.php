<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Carbon\Carbon;

use App\Models\Thread;
use App\Models\Reply;
use App\Models\Activity;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'confirmed', 'confirmation_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email',
    ];

    protected $casts = [
        'confirmed' => 'boolean'
    ];

    public function confirm()
    {
        $this->confirmed = true;

        $this->confirmation_token = NULL;

        $this->save();
    }

    public function isAdmin()
    {
        return in_array($this->name, ['johnDoe', 'JaneDoe']);
    }

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }
    
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function read($thread)
    {
        // simulate that the user visited the thread
        cache()->forever($this->visitedThreadCacheKey($thread), Carbon::now());
    }

    // public function avatar()
    // {
    //     return asset($this->avatar_path ?: 'images/avatars/default.jpg');
    // }

    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ?: 'images/avatars/default.jpg');
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf("User.%s.visits.%s", $this->id, $thread->id);
    }
}
