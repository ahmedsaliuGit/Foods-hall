<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;

use App\Notifications\ThreadWasUpdated;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

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
    public function testAThreadCanHaveManyReplies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    public function testAThreadHasOwner()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    public function testAThreadHasPath()
    {
        $thread = create('App\Models\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    public function testCanCreateReplyToThread()
    {
        $this->thread->addReply(['body' => 'Foobar', 'user_id' => 1]);
        
        $this->assertCount(1, $this->thread->replies);
    }

     public function testThreadHaveChannel()
    {
        $thread = create('App\Models\Thread');
        
        $this->assertInstanceOf('App\Models\Channel', $thread->channel);
    }

    public function testAThreadCanBeSubscribedTo()
    {
        $thread = create('App\Models\Thread');

        $thread->subscribe($userId = 1);

        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    public function testAThreadCanBeUnsubscribedFrom()
    {
        $thread = create('App\Models\Thread');

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId = 1);

        $this->assertCount(0, $thread->subscriptions);
    }

    public function testCanDetermineIfAuthenticatedUserSubscribeToThread()
    {
        $thread = create('App\Models\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    public function testAThreadNotifiesAllRegisteredSubscribersWhenAReplyIsAdded()
    {
       Notification::fake();

       $this->signIn()->thread->subscribe()->addReply(['body' => 'Foobar', 'user_id' => 1]);

       Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    public function testAThreadCanCheckIfTheAuthenticatedUserHasReadAllReplies()
    {
       $this->signIn();

       $thread = create('App\Models\Thread');

       tap(auth()->user(), function($user) use ($thread) {
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);
            
            $this->assertFalse($thread->hasUpdatesFor($user));
       });
    }

    // public function testAThreadMayBeLocked()
    // {
    //     $this->assertFalse($this->thread->locked);

    //     $this->thread->lock();

    //     $this->assertTrue($this->thread->locked);
    // }

    // public function testAThreadRecordsEachVisit()
    // {
    //     $thread = make('App\Models\Thread', ['id' => 1]);

    //     $thread->visits()->reset();

    //     $this->assertSame(0, $thread->visits()->count());

    //     $thread->visits()->record();

    //     $this->assertEquals(1, $thread->visits()->count());

    //     $thread->visits()->record();
        
    //     $this->assertEquals(2, $thread->visits()->count());
    // }
}
