<?php

namespace App;

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

    public function addReply($replies)
    {
        $reply = $this->replies()->create($replies);

        $this->subscriptions->filter(function ($sub) use ($reply) {
            return $sub->user_id != $reply->user_id;
        })
        ->each->notify($reply);

        return $reply;
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
}
