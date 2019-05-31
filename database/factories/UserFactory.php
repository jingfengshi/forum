<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'confirmed'=>true,
    ];
});

$factory->state(User::class,'unconfirmed', function (Faker $faker) {
    return [
        'confirmed'=>false,
    ];
});

$factory->state(User::class,'administrator', function (Faker $faker) {
    return [
        'name'=>'jasper',
    ];
});

$factory->define(\App\Thread::class, function (Faker $faker) {
    $title = $faker->sentence;
    return [
        'user_id'=>function(){
            return factory('App\User')->create()->id;
        },
        'channel_id'=>function(){
            return factory('App\Channel')->create()->id;
        },
        'title' => $title,
        'body' => $faker->paragraph,
        'visits'=> 0,
        'slug'=>str_slug($title),
        'locked'=>false
    ];
});


$factory->define(\App\Reply::class, function (Faker $faker) {
    return [
        'user_id'=>function(){
            return factory('App\User')->create()->id;
        },
        'thread_id'=>function(){
            return factory('App\Thread')->create()->id;
        },
        'body' => $faker->paragraph,
    ];
});

$factory->define(\App\Channel::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'slug' => $faker->word,
    ];
});
$factory->define(Illuminate\Notifications\DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => Uuid::uuid4()->toString(),
        'type' =>'App\Notifications\ThreadWasUpdated',
        'notifiable_id'=>function(){
            return auth()->id()?:factory('App\User')->create()->id;
        },
        'notifiable_type'=>'App\User',
        'data'=>['foo'=>'bar']
    ];
});