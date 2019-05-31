<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;
class LockThreadTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function once_locked_a_thread_may_not_receive_new_reply()
    {

        $this->signIn();
        $thread = create('App\Thread');

       $thread->lock();


       $this->post($thread->path().'/replies',[
          'body' => 'Foobar',
           'user_id' =>auth()->id()

       ])->assertStatus(422);


    }
    
    
    /** @test */
    public function non_administrators_may_not_lock_threads()
    {
         $this->signIn();

         $thread = create('App\Thread',['user_id'=>auth()->id()]);

         //hit the endpoint, that will update the 'locked' attribute to true for the thread

        $this->post(route('locked-threads.store',$thread),[
            'locked' =>true
        ])->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);

    }

    /** @test */
    public function administrator_can_lock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread',['user_id'=>auth()->id()]);

        $this->post(route('locked-threads.store',$thread),[
           'locked'=>true
        ]);

        $this->assertTrue(!!$thread->fresh()->locked,'Failed asserting that the thread was locked');
    }

    /** @test */
    public function administrator_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread',['user_id'=>auth()->id(),'locked'=>true]);

        $this->delete(route('locked-threads.destroy',$thread));

        $this->assertFalse(!!$thread->fresh()->locked,'Failed asserting that the thread was unlocked');
    }
  
}
