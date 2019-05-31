<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadsFilters;
use App\Rules\Recaptcha;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;



class ThreadsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth'])->except(['index','show']);
    }


    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel ,ThreadsFilters $filters, Trending $trending)
    {

        $threads = $this->getThreads($channel, $filters);
        if(request()->wantsJson()){
            return $threads;
        }



        return view('threads.index',[
            'threads' => $threads,
            'trending'=> $trending->get()
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
    public function store(Request $request,Recaptcha $recaptcha)
    {


        $this->validate($request,[
            'title'=>'required|spamfree',
            'body'=>'required|spamfree',
            'channel_id'=>'required|exists:channels,id',
            'g-recaptcha-response'=>['required',$recaptcha]
        ]);

        //Guzzle

         $thread=Thread::create([
             'user_id'=>auth()->id(),
             'channel_id'=>request('channel_id'),
             'title'=>request('title'),
             'body'=>request('body'),
         ]);

         return redirect($thread->path())->with('flash','Your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param $channel
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel,Thread $thread, Trending $trending)
    {
        if(auth()->check()){
            auth()->user()->read($thread);
        }

        $trending ->push($thread);

        $thread->increment('visits');

       return view('threads.show',[
           'thread'=>$thread,
           'replies'=>$thread->replies()->paginate(20),
       ]);
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel,Thread $thread)
    {

        $this->authorize('update',$thread);
//        if($thread->user_id != auth()->id()){
//            if(request()->wantsJson()){
//                return response(['status'=>'Permission Denied'],403);
//            }
//            abort(403,'You do not have permission to do this.');
//        }

        $thread->delete();
        if(request()->wantsJson()){
            return response([],204);
        }

        return redirect('/threads');

    }

    /**
     * @param Channel $channel
     * @param ThreadsFilters $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadsFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);
        if ($channel->exists) {
            $threads = $threads->where('channel_id', $channel->id);
        }
        return $threads->paginate(5);
    }
}
