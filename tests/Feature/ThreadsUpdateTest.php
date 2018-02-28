<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsUpdateTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->withExceptionHandling();
        
        $this->signIn();
    }

    /** @test */
    public function a_thread_can_be_updated_by_its_creator()
    {

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
            'body' => 'Changed body'
        ]);

        tap($thread->fresh(), function($thread) {
            $this->assertEquals('Changed', $thread->title);
            $this->assertEquals('Changed body', $thread->body);
        });
    }

    /** @test */
    public function a_thread_requires_a_title_and_body_be_updated()
    {

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed'
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'Changed'
        ])->assertSessionHasErrors('title');
    }

    /** @test */
    public function unauthorized_user_may_not_update_threads()
    {

        $thread = create('App\Models\Thread', ['user_id' => create('App\User')->id]);

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    public function test_and_test()
    {
        return null;
    }
}
