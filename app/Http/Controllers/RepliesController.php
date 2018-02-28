<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use Gate;

use App\Models\Thread;
use App\Models\Reply;
use App\Http\Requests\CreatePostRequest;
use App\User;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(15);
    }

    public function store($channelId, Thread $thread, CreatePostRequest $createPost)
    {
        if ($thread->locked) {
            return response('Thread is locked', 422);
        }
        // if ( stripos( request('body'), 'yahoo Customer support') !== false) {
        //     throw new \Exception('Your reply contains spam.');
        // }

        // if (Gate::denies('create', new Reply)) {
        //     return response('You are posting to frequently. Hope is not a mistake, hold for a minute.', 422);
        // }
        
        // try {
            // $this->validate(request(), ['body' => 'required|spamfree']);
            
        return $thread->addReply([
            'user_id' => auth()->user()->id,
            'body' => request('body')
        ])->load('owner');;
    
            // if ( request()->expectsJson()) {
            //     return $reply->load('owner');
            // }
        // } catch(\Exception $ex) {
        //     return response('Sorry, your reply cannot be saved at this time.', 422);
        // }

        // return back()->with('flash','Your reply has been left');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        // try {
        $this->validate(request(), ['body' => 'required|spamfree']);
        
        $reply->update(request(['body']));
        // } catch(\Exception $ex) {
        //     return response('Sorry, your reply cannot be saved at this time.', 422);
        // }

        // return back(); 
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if ( request()->expectsJson())
        {
            return response(['status' => 'Reply deleted']);
        }

        return back(); 
    }
}
