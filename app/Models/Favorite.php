<?php

namespace App\Models;

class Favorite extends Model
{
    use RecordActivity;

    public function favorited()
    {
        return $this->morphTo(); 
    }
}
