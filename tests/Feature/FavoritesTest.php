<?php

namespace Tests\Feature;

use App\Favorite;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;
class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')
            ->assertRedirect('/login');
    }


    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        //If I post to a favorite endpoint
        $this->post('replies/'.$reply->id.'/favorites');
        //It should be recorded in the database.
        $this->assertCount(1,$reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavorite_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        //If I post to a favorite endpoint
        $reply->favorites();
        $this->delete('replies/'.$reply->id.'/favorites');
        //It should be recorded in the database.
        $this->assertCount(0,$reply->fresh()->favorites);
    }

    /** @test */
    public function an_authenticated_user_may_only_favoirte_a_reply_once()
    {
        $this->signIn();
        $reply = create('App\Reply');
        //If I post to a favorite endpoint
        try{
            $this->post('replies/'.$reply->id.'/favorites');
            $this->post('replies/'.$reply->id.'/favorites');
        }catch (\Exception $exception){
            $this->fail('Did not expect to insert the same record set twice.');
        }

        //It should be recorded in the database.
        $this->assertCount(1,$reply->favorites);
    }

   
  
}
