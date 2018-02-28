<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Thread;

class LockThreadsController extends Controller
{
    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);
    }

    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);
    }
}
