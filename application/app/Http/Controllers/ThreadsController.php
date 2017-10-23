<?php

namespace App\Http\Controllers;

use App\User;
use App\Thread;
use App\Channel;
use Carbon\Carbon;
use App\Inspections\Spam;
use Illuminate\Http\Request;
use App\Filters\ThreadFilter;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilter $filter)
    {
        $threads = $this->getThreads($channel, $filter);
        if (request()->wantsJson()) {
            return $threads;
        }
        return view('threads.index', compact('threads'));
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
    public function store(Request $request, Spam $spam)
    {
        $this->validate($request, [
            'title' => 'required',
            'channel_id' => 'required|exists:channels,id',
            'body' => 'required'
        ]);
        
        $spam->detect(request('body'));
        $spam->detect(request('title'));

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => $request->get('channel_id'),
            'title' => $request->get('title'),
            'body' => $request->get('body')
        ]);

        return redirect($thread->path())
            ->with('flash', 'Your thread was created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel_id, Thread $thread)
    {
        optional(auth()->user())->read($thread);
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
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }
        return redirect('/threads');
    }

    protected function getThreads($channel, $filter) {
        $threads = Thread::filter($filter)->latest();

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id)->latest();
        }
        
        return $threads->get();
    }
}
