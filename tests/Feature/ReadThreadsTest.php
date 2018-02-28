<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Models\Thread');
    }

    public function testAUserCanViewAllThreads()
    {
        $this->get('/threads')->assertSee($this->thread->title);

    }

    public function testAUserCanViewOneThreads()
    {
        $this->get($this->thread->path())->assertSee($this->thread->title);
    }

    public function testGuestCannotCreateThread()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')->assertRedirect('/login');
    }

    public function testAUserCanFilterAllThreadsAccordingToAChannel()
    {
        $channel = create('App\Models\Channel');

        $threadInChannel = create('App\Models\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Models\Thread');

        $this->get('threads/' . $channel->name)->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    public function testASignInUserCanFilterThreadsByUsername()
    {
        $this->signIn(create('App\User', ['name' => 'Ahmed Saliu']));

        $threadsByUser = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $threads = create('App\Models\Thread');

        $this->get('threads?by=Ahmed Saliu')->assertSee($threadsByUser->title)
            ->assertDontSee($threads->title);
    }

    public function testAUserCanFilterThreadsByPopularity()
    {
        $threadWithThreeReplies = create('App\Models\Thread');
        create('App\Models\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithTwoReplies = create('App\Models\Thread');
        create('App\Models\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithNoReply = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3,2,0], array_column($response['data'], 'replies_count'));
    }

    public function testAUserCanFilterThreadsByUnanswer()
    {
        $thread = create('App\Models\Thread');
        create('App\Models\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswer=1')->json();

        $this->assertCount(1, $response['data']);
    }

    public function testAUserCanRequestForRepliesWithAGivenThread()
    {
        $thread = create('App\Models\Thread');

        create('App\Models\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();
        
        $this->assertCount(1, $response['data']);

        $this->assertEquals(1, $response['total']);
    }

    public function testANewVisitIsRecordedEachTimeAUserVisitAThread()
    {
        $thread = create('App\Models\Thread');

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
