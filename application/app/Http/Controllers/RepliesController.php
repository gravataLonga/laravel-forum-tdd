<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use App\Channel;
use App\Rules\Testing;
use App\Rules\Spamfree;
use Illuminate\Http\Request;


class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index(Channel $channel, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }
    
    public function store(Request $request, $channel_id, Thread $thread)
    {
        $request->validate([
            'body' => ['required', new SpamFree],
        ]);

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);
        
        if (request()->expectsJson()) {
            return response($reply->load('owner'), 200);
        }

        return back()->with('flash', 'A reply was added');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        $this->validate(request(), [
            'body' => ['required', new Spamfree]
        ]);
        $reply->update(request(['body']));
    }

    public function destroy (Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->wantsJson()) {
            return response(['status' => 'Your reply has been deleted']);
        }

        return back()->with('flash', 'Reply was deleted with success');
    }
}
