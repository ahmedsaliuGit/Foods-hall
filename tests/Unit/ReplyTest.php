<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserThatOwnReply()
    {
        $replies = create('App\Models\Reply');

        $this->assertInstanceOf('App\User', $replies->owner);
    }

    public function testItCanDetectMentionedUsersInTheBody()
    {
        
        $reply = create('App\Models\Reply', ['body' => '@JohnDoe wants to talk to @AhmedSaliu']);

        $this->assertEquals(['JohnDoe', 'AhmedSaliu'], $reply->mentionedUsers());
    }

    public function testItWrapMentionedUsernamesWithinAAnchorTag()
    {
        $reply = new \App\Models\Reply(
            ['body' => 'Hello @AhmedSaliu.']
        );

        $this->assertEquals(
            'Hello <a href="/profiles/AhmedSaliu">@AhmedSaliu</a>.',
            $reply->body
        );
    }

    public function testItKnowsBestReply()
    {
        $reply = create('App\Models\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->isBest());
    }
}
