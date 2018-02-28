<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;



class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_best()
    {
        // $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Models\Reply', ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->json('POST', route('best-reply.store', $replies[1]->id));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function only_the_creator_can_mark_as_best()
    {
        $this->withExceptionHandling();

        $this->signIn();
        
        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Models\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn(create('App\User'));

        $this->json('POST', route('best-reply.store', $replies[1]->id))->assertStatus(403);
        
        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function deleting_reply_should_remove_the_reply_id_from_threads_best_reply(Type $var = null)
    {
        $this->signIn();

        $reply = create('App\Models\Reply', ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy', [$reply]));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }

}
