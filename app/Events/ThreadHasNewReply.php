<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ThreadHasNewReply
{
    use SerializesModels;

    public $thread;

    public $reply;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Thread $thread
     * @param \App\Models\Reply $reply
     * @return void
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }
}
