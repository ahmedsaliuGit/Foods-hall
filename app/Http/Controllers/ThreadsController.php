<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\Recaptcha;

use App\User;
use App\Models\Thread;
use App\Models\Channel;
use App\Filters\ThreadFilter;
use App\Trending;

class ThreadsController extends Controller
{
    /**
     * Create a new ThreadsController instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilter $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson()) return $threads;

        // $trending = array_map('json_decode', Redis::zrevrange('trending_thread', 0, 4));
        
        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()  
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id',
            'body' => 'required|spamfree',
            'g-recaptcha-response' => 'required|recaptcha'
        ]);

        $thread = Thread::create([
            'user_id' => auth()->user()->id,
            'channel_id' => request('channel_id'),
            'title' => request()->title,
            'body' => request()->body,
        ]);

        if( request()->wantsJson() ) {
            return response($thread, 201);
        }
        
        return redirect()->to($thread->path())
            ->with('flash', 'Your thread has been published successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel, Thread $thread, Trending $trending)
    {
        // Record that a user visited this page
        // Record a timestamp
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->increment('visits');

        // $thread->visits()->record();

        // Redis::zincrby('trending_thread', 1, json_encode([
        //     'title' => $thread->title,
        //     'path' => $thread->path()
        // ]));
        
        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update($channel, Thread $thread)
    {
        // authorization
        $this->authorize('update', $thread);

        // validation
        $this->validate(request(), [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
        ]);

        // update
        $thread->update(request(['title', 'body']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('delete', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect('threads');
    }

    protected function getThreads($channel, $filters)
    {
        $threads = Thread::latest()->filters($filters);

        if ($channel->exists)
        {
            $threads->where('channel_id', $channel->id);
        }

        // return $threads->get();

        return $threads->paginate(15);


    }

}
