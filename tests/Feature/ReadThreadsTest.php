<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadThreadsTest extends TestCase
{

    use DatabaseMigrations;


    protected $thread;

    public function setUp():void
    {
        parent::setUp();
        $this->thread=factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {

       $this->get('/threads')
            ->assertSee($this->thread->title);

    }

    /** @test */
    public function a_user_can_view_single_thread()
    {

       $this->get($this->thread->path())
                    ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        //Given we have a thread

        //And that thread include  replies

        //When we visit a thread page

        //Then we should see the replies
//        $reply=create('App\Reply',['thread_id' =>$this->thread->id]);
//
//            $this->get($this->thread->path())
//            ->assertSee($reply->body);

    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel=create('App\Channel');
        $threadInChannel=create('App\Thread',['channel_id'=>$channel->id]);
        $threadNotInChannle=create('App\Thread');
        $this->get('/threads/'.$channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannle->title);
    }
    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User',['name'=>'jasper']));

        $threadByJasper=create('App\Thread',['user_id'=>auth()->id()]);

        $thread=create('App\Thread');


        $this->get('threads?by=jasper')
            ->assertSee($threadByJasper->title)
            ->assertDontSee($thread->title);

    }


    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        //Give we have three threads

        //with 2 replies ,3 replies ,0 replies, respectively
        $threadWithTwoReplies=create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithTwoReplies->id],2);


        $threadWithThreeReplies=create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithThreeReplies->id],3);

        $threadWithNoReplies=$this->thread;

        //When I filter all threads by popularity
        $response = $this->getJson('threads?popular=1')->json();

        //Then they should be returned from most replies to least

        $this->assertEquals([3,2,0],array_column($response['data'],'replies_count'));

    }
    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread=create('App\Thread');
        create('App\Reply',['thread_id'=>$thread->id]);

        $response=$this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response['data']);
    }



    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread=create('App\Thread');
        create('App\Reply',['thread_id'=>$thread->id],2);

        $response=$this->getJson($thread->path().'/replies')->json();

        $this->assertCount(2,$response['data']);
        $this->assertEquals(2,$response['total']);
    }
    
    /** @test */
    public function we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread=create('App\Thread');


        $this->assertEquals(0,$thread->visits);

        $this->call('GET',$thread->path());

        $this->assertEquals(1,$thread->fresh()->visits);
    }


}
