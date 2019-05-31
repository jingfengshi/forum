<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;
class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        //Given I have a user , JohnDoe, who is signIn
        $john=create('App\User',['name'=>'JohnDoe']);

        $this->signIn($john);
        //and another user ,JaneDoe.
        $jane =create('App\User',['name'=>'JaneDoe']);
        //If we have a thread


        //And JohnDoe replies and mentions @JaneDoe.
        $thread=create('App\Thread');


        $reply =make('App\Reply',[
            'body'=>'@JaneDoe look at this. Also @flankDoe'
        ]);

        $this->json('post',$thread->path().'/replies',$reply->toArray());
        //Then , JaneDoe should be notified
        $this->assertCount(1,$jane->notifications);
    }
    
    
    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {

        create('App\User',['name'=>'johnDoe']);
        create('App\User',['name'=>'johnDoe2']);
        create('App\User',['name'=>'janeDoe']);

        $results = $this->json('GET','/api/users',['name' => 'john']);

        $this->assertCount(2,$results->json());
    }

   
  
}
