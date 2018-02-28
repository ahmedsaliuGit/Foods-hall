<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Carbon\Carbon;

use App\Models\Activity;

class ActivitiesTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRecordActivitiesWhenThreadIsCreated()
    {
        $this->signIn();

        $thread = create('App\Models\Thread');

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Models\Thread',
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject_id, $thread->id);
    }

    public function testRecordActivitiesWhenThereIsAReplyToAThread()
    {
        $this->signIn();

        $reply = create('App\Models\Reply');

        $this->assertEquals(2, Activity::count());
    }

    public function testCanFetchFeedForAnyUser()
    {
        $this->signIn();

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()], 2);

        auth()->user()->activities()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed(auth()->user());

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));
    }
}
