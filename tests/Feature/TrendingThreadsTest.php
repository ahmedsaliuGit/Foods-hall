<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Trending;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();
        
        $this->trending->reset();
    }

    /** @test */
    public function it_can_increment_when_a_user_the_thread()
    {
        $this->assertEmpty($this->trending->get());

        $thread = create('App\Models\Thread');

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending = $this->trending->get());

        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
