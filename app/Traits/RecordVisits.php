<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;

use App\Visits;

/**
 * 
 */
trait RecordVisits
{
    // public function recordVisit()
    // {
    //     Redis::incr($this->visitsCacheKey());

    //     return $this;
    // }

    // public function visits()
    // {
    //     return new Visits($this);
    // }

    // public function resetVisits()
    // {
    //     Redis::del($this->visitsCacheKey());

    //     return $this;
    // }

    // protected function visitsCacheKey()
    // {
    //     return "thread.{$this->id}.visit";
    // }
}