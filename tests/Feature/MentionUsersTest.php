<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        // given that we have user, johndoe, that is signedin.
        $john = create('App\User', ['name' => 'johnDoe']);

        $this->signIn($john);

        // given that we have user, janedoe.
        $jane = create('App\User', ['name' => 'JaneDoe']);

        // given that we have thread
        $thread = create('App\Models\Thread');

        // given that we have reply to a thread
        $reply = make('App\Models\Reply', ['body' => '@JaneDoe look it this. @FrankDoe']);

        // all mentions user should be notified
        $this->json('post', $thread->path().'/replies', $reply->toArray());
        
        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_the_given_character()
    {
        create('App\User', ['name' => 'johnDoe']);
        // create('App\User', ['name' => 'johnDoe']);
        create('App\User', ['name' => 'JaneDoe']);

        $result = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(1, $result->json());
    }
}
