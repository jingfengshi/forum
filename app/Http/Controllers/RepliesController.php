<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostForm;
use App\Inspections\Spam;
use App\Notifications\YouWereMentioned;
use App\Reply;
use App\Thread;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }


    public function index($channelId,Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @param Spam $spam
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store($channelId, Thread $thread,CreatePostForm $form)
    {
        if($thread->locked){
         return response('Thread is locked',422);
        }
         return  $thread->addReply([
            'body'=>request('body'),
            'user_id'=>auth()->id()
        ])->load('owner');



    }

    public function update(Reply $reply)
    {
        $this->authorize('update',$reply);
        $this->validate(request(),[
            'body'=>'required|spamfree'
        ]);
        $reply->update(['body'=>request('body')]);

    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update',$reply);

        $reply->delete();
        if(request()->wantsJson()){
            return response(['status'=>'Reply deleted']);
        }
        return back();
    }
}
