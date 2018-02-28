<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Rules\Recaptcha;

use App\Models\Activity;
use App\Models\Thread;

class CreateThreadTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNewUserMustConfirmTheirAccountBeforeCreatingThread()
    {
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);
        
        $thread = make('App\Models\Thread');

        $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    public function testUserCanCreateForumThread()
    {
        $this->signIn();

        $thread = make('App\Models\Thread');

        $response = $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        // $response = $this->publishThread(['title' => 'Some Tile', 'body' => 'Some body']);

        $this->get($response->headers->get('Location'))->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    public function testGuestCannotCreateForumThread()
    {
        $this->withExceptionHandling()->post(route('threads'), []);
    }

    public function testValidateThreadRequireTitle()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function testValidateThreadRequireBody()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    // public function testAThreadRequiresRecaptchaVerification()
    // {        
    //     $this->publishThread(['g-recaptcha-response' => 'test'])
    //         ->assertSessionHasErrors('g-recaptcha-response');
    // }

    public function testAThreadRequiresAUniqueSlug()
    {
        $this->signIn();

        $thread = create('App\Models\Thread', ['title' => 'Foo Bar']);

        $this->assertEquals($thread->fresh()->slug, 'foo-bar');

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("foo-bar-{$thread['id']}", $thread['slug']);
    }

    public function testAThreadWithTitleThatEndWithNumberGetUniqueSlug()
    {
        $this->signIn();
        
        $thread = create('App\Models\Thread', ['title' => 'Some Title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();
        
        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
    }

    public function testAuthorizedUserMayDeleteAThread()
    {
        $this->signIn();

        $thread = create('App\Models\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Models\Reply', ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path())->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());

        // $this->assertDatabaseMissing('activities', [
        //     'subject_id' => $thread->id,
        //     'subject_type' => get_class($thread),
        // ]);
    }

    public function testUnAuthorizedUserCannotDeleteThread()
    {
        $this->withExceptionHandling();

        $thread = create('App\Models\Thread');

        $this->delete($thread->path())->assertRedirect(route('login'));

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    public function testValidateThreadRequireChannelId()
    {
        factory('App\Models\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    protected function publishThread($override = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Models\Thread', $override);

        return $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}
