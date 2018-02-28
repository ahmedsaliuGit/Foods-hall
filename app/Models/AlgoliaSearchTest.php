<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class AlgoliaSearchTest extends Model
{
    use Searchable;
    
    protected $fillable = [
        'name'
    ];
}
