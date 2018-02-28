<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        
        // preg_match_all('/\@([^\s\.]+)/', $event->reply->body, $matches);
        // $mentionedUsers = $event->reply->mentionedUsers();

        User::whereIn('name', $event->reply->mentionedUsers())->get()
        ->each(function ( $user ) use ($event) {
            $user->notify(new YouWereMentioned($event->reply));
        });

        // collect( $event->reply->mentionedUsers() )
        // ->map(function ( $name ) {
        //     return User::where('name', $name)->first();
        // })
        // ->filter()
        // ->each(function ( $user ) use ($event) {
        //     $user->notify(new YouWereMentioned($event->reply));
        // });
        
        // foreach($mentionedUsers as $name ){
        //     $user = User::whereName($name)->first();

        //     if ($user) {
        //         $user->notify(new YouWereMentioned($event->reply));
        //     }
        // }
    }
}
