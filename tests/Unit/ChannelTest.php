<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;
class ChannelTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function a_channel_consists_of_threads()
    {
        $channel=create('App\Channel');

        $thread =create('App\Thread',['channel_id'=>$channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }

   
  
}
