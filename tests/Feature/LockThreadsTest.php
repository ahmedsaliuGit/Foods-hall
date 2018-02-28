<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;



class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function non_administrator_cannot_lock_thread()
    {
        $this->withExceptionHandling();
        
        $this->signIn();

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $this->post(route('lock-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function an_administrator_can_lock_thread()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $this->post(route('lock-threads.store', $thread));

        $this->assertTrue($thread->fresh()->locked, 'Fail asserting that the thread was locked.');
    }

    /** @test */
    public function an_administrator_can_unlock_thread()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Models\Thread', ['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('lock-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked, 'Fail asserting that the thread was unlocked.');
    }

    /** @test */
    public function if_a_thread_is_locked_it_cannot_be_replied_to()
    {
        // $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Models\Thread', ['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'user_id' => auth()->id(),
            'body' => 'Some unwanted reply'
        ])->assertStatus(422);
    }

}
