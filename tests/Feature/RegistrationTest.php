<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
class RegistrationTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();
        $this->post('/register',[
            'name' => 'John',
            'email' => 'john@example.com',
            'password' =>'12345678',
            'password_confirmation' =>'12345678'

        ]);


        Mail::assertQueued(PleaseConfirmYourEmail::class);
       
    }


    /** @test */
    public function user_can_fully_confirm_their_email_addresses()
    {

        Mail::fake();
        $this->post('/register',[
            'name' => 'John',
            'email' => 'john@example.com',
            'password' =>'12345678',
            'password_confirmation' =>'12345678'

        ]);

        $user = User::whereName('John')->first();


        $this->assertFalse($user->confirmed);

        $this->assertNotNull($user->confirmation_token);


        //Let the user confirm their account .

        $response = $this->get('/register/confirm?token='.$user->confirmation_token);

        $this->assertTrue($user->fresh()->confirmed);

        $this->assertNull($user->fresh()->confirmation_token);

        $response->assertRedirect('/threads');
    }


    /** @test */
    public function confirming_an_invalid_token()
    {
        $response = $this->get('/register/confirm?token=invalid')
            ->assertRedirect('/threads')
            ->assertSessionHas('flash');


    }
   
  
}
