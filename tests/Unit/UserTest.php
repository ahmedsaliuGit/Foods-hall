<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Carbon\Carbon;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_fetch_thier_most_recent_reply()
    {
        $user = create('App\User');

        $reply = create('App\Models\Reply', ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /** @test */
    function it_knows_if_it_was_just_published ()
    {
        $reply = create('App\Models\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    function a_can_determined_their_avatar()
    {
        $user = create('App\User');
        
        $this->assertEquals(asset('/images/avatars/default.jpg'), $user->avatar_path);

        $user->avatar_path = '/storage/avatars/me.jpg';

        $this->assertEquals(asset('/storage/avatars/me.jpg'), $user->avatar_path);
    }
}
