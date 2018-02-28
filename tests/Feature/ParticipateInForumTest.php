<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Models\Thread');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthenticatedUserCanReplyToAThread()
    {
        $this->signIn();

        $reply = make('App\Models\Reply');

        $this->post($this->thread->path() .'/replies', $reply->toArray());
        
        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $this->thread->fresh()->replies_count);
    }

    public function testUnauthenticatedUserCannotReplyToAThread()
    {
        $this->withExceptionHandling()->post('/threads/php/1/replies', [])
            ->assertRedirect('login');
    }

    public function testValidateReplyRequireBody()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Models\Thread');
        $reply = make('App\Models\Reply', ['body' => null]);

        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    public function testUnauthorizedUserCannotDeleteReply()
    {
        $this->withExceptionHandling();

        $reply = create('App\Models\Reply');

        $this->delete('replies/'.$reply->id)
            ->assertRedirect('login');

        $this->signIn();

        $this->delete('replies/'.$reply->id)
            ->assertStatus(403);
    }

    public function testAuthorizedUserCanDeleteReply()
    {
        $this->signIn();

        $reply = create('App\Models\Reply', ['user_id' => auth()->id()]);

        $this->delete('replies/'.$reply->id)->assertStatus(302);
            
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    public function testUnauthorizedUserCannotUpdateReply()
    {
        $this->withExceptionHandling();

        $reply = create('App\Models\Reply');

        $this->patch('replies/'.$reply->id)
            ->assertRedirect('login');

        $this->signIn();

        $this->patch('replies/'.$reply->id)
            ->assertStatus(403);
    }

    public function testAuthorizedUserCanUpdateReply()
    {
        $this->signIn();

        $reply = create('App\Models\Reply', ['user_id' => auth()->id()]);

        $updatedReply = 'You have been changed. Bar';

        $this->patch('replies/'.$reply->id, ['body' => $updatedReply]);
            
        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }

    public function testReplyThatContainSpamCannotBeCreated()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Models\Thread');

        $reply = make('App\Models\Reply', ['body' => 'Yahoo Customer Support']);

        $this->json('post', $thread->path().'/replies', $reply->toArray())->assertStatus(422);
    }

    public function testUsersCanOnlyReplyAMaximumOncePerMinute()
    {
        $this->withExceptionHandling();

        $this->signIn();
        
        $thread = create('App\Models\Thread');

        $reply = make('App\Models\Reply', ['body' => 'A simple reply']);
        
        $this->post($thread->path().'/replies', $reply->toArray())->assertStatus(200);

        $this->post($thread->path().'/replies', $reply->toArray())->assertStatus(429);
    }
}
