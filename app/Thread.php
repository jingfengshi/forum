<?php

namespace App;

use App\Events\ThreadHasReply;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Thread extends Model
{

    use RecordActivity;
    protected $guarded=[];

    protected $with=['creator','channel'];


    protected $appends=['isSubscribedTo'];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount',function($builder){
            $builder->withCount('replies');
        });

//        static::addGlobalScope('creator',function($builder){
//            $builder->with('creator');
//        });
        static::deleting(function($thread){
            $thread->replies->each(function($reply){
                $reply->delete();
            });

            Reputation::demote($thread->creator,Reputation::THREAD_WAS_PUBLISHED);
        });

        static::created(function($thread){
            $thread->update(['slug'=>$thread->title]);

            Reputation::award($thread->creator,Reputation::THREAD_WAS_PUBLISHED);
            //$thread->creator->increment('reputation',10);
        });


    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }


    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }



    public function creator()
    {
        return  $this->belongsTo(User::class,'user_id');
    }


    public function addReply($reply)
    {

        $reply= $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        $this->notifySubscribers($reply);

        return $reply;

    }

    public function lock()
    {
        $this->update(['locked'=>true]);
    }
    public function unlock()
    {
        $this->update(['locked'=>false]);
    }


    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id','!=',$reply->user_id)
            ->each->notify($reply);
    }


    public function scopeFilter($query,$filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId=null)
    {
        $this->subscriptions()->create([
            'user_id'=>$userId?:auth()->id()
        ]);

        return $this;


    }

    public function unsubscribe($userId=null)
    {
        $this->subscriptions()->where('user_id',$userId?:auth()->id())->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id',auth()->id())
            ->exists();
    }

    public function hasUpdatesFor($user= null)
    {
        //Look in the cache for the proper key

        //compare that carbon instance with the $thread->updated_at

        $user =$user?:auth()->user();

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }


    public function  getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        $original = $slug;
        $count =2 ;
        while (static::whereSlug($slug)->exists()){
            $slug ="{$original}-".$count++;
        }
        $this->attributes['slug'] = $slug;
    }



    public function markBestReply(Reply $reply)
    {
        $this->best_reply_id = $reply->id;

        $this->save();

        Reputation::award($reply->owner,Reputation::BEST_REPLY_AWARDED);
    }
}
