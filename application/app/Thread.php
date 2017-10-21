<?php

namespace App;

use App\Events\ThreadHasNewReply;
use App\Notifications\ThreadWasUpdated;
use App\RecordActivity;
use App\ThreadSubscription;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordActivity;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];
    
    protected static function boot()
    {
        parent::boot();
        // Now is a column on Database
        // static::addGlobalScope('replyCount', function ($builder) {
        //     $builder->withCount('replies');
        // });

        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });
    }

    public function path()
    {
    	return '/threads/' . $this->channel->slug . '/' . $this->id;
    }

    public function replies()
    {
    	return $this->hasMany('App\Reply');
            // ->withCount('favorites')
            // ->with('owner');
    }

    public function getReplyCountAttribute ()
    {
        return $this->replies()->count();
    }

    public function  channel()
    {
        return $this->belongsTo('App\Channel');
    }

    public function creator()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function addReply($reply)
    {
        // (new \App\Spam)->detect(request('body'));

        $reply = $this->replies()->create($reply);
        $this->notifySubscribers($reply);
        // event(new ThreadHasNewReply($this, $reply));
        return $reply;
    }

    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each
            ->notify($reply);
    }

    /**
     * Apply Filter : Refactoring
     */ 
    public function scopeFilter ($query, $filters)
    {
        $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()->where('user_id', $userId ?: auth()->id())->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdateFor($user)
    {
        $key = optional($user)->visitedThreadCacheKey($this) ?: '';
        return $this->updated_at > cache($key);
    }
}
