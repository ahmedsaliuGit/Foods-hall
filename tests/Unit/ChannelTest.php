<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChannelTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testThreadAssociateWithChannel()
    {
        $channel = create('App\Models\Channel');

        $threadHaveChannel = create('App\Models\Thread', ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($threadHaveChannel));
    }
}
