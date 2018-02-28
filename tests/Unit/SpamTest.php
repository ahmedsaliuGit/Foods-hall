<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    public function it_check_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Your spam detect'));

        $this->expectException('Exception');

        $spam->detect('Yahoo Customer Support');
    }

    /** @test */
    public function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');
        
        $spam->detect('Hello world aaaaaaaa');
    }
}
