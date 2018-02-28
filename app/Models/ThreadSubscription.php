<?php

namespace App\Models;

use App\User;
use App\Notifications\ThreadWasUpdated;

class ThreadSubscription extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function notify($reply)
    {
        $this->user->notify(new ThreadWasUpdated($this->thread, $reply));
    }
}
