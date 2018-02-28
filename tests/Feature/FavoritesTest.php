<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGuestCannotFavoriteAReply()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')->assertRedirect('login');
    }

    public function testAuthenticatedUserCanFavoriteAReply()
    {
        $this->signIn();

        $reply = create('App\Models\Reply');

        $this->post('replies/'. $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    public function testAuthenticatedUserCanUnfavoriteAReply()
    {
        $this->signIn();

        $reply = create('App\Models\Reply');

        $reply->favorite();

        $this->delete('replies/'. $reply->id . '/favorites');

        $this->assertCount(0, $reply->favorites);
    }

    public function testAuthenticatedUserCanOnlyFavoriteAReplyOnce()
    {
        $this->signIn();

        $reply = create('App\Models\Reply');

        try {
            $this->post('replies/'. $reply->id . '/favorites');
            $this->post('replies/'. $reply->id . '/favorites');
        } catch (\Exception $ex) {
            $this->fail('Did not expect to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }

}
