<?php

use App\Models\Thread;
use App\Models\Reply;
use App\Models\Channel;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'confirmed' => true,
    ];
});

$factory->state(App\User::class, 'unconfirmed', function () {

    return [
        'confirmed' => false,
    ];
});

$factory->state(App\User::class, 'administrator', function () {
    
    return [
        'name' => 'johnDoe',
    ];
});

$factory->define(Thread::class, function (Faker\Generator $faker) {

    $title = $faker->sentence;
    
    return [
        'user_id' => function (){
            return factory('App\User')->create()->id;
        },
        'channel_id' => function (){
            return factory('App\Models\Channel')->create()->id;
        },
        'title' => $title,
        'body' => $faker->paragraph,
        'visits' => 0,
        'slug' => str_slug($title),
        'locked' => false
    ];
});

$factory->define(Channel::class, function (Faker\Generator $faker) {
    $name = $faker->word;

    return [
        'name' => $name,
        'slug' => $name,
    ];
});

$factory->define(Reply::class, function (Faker\Generator $faker) {

    return [
        'thread_id' => function (){
            return factory('App\Models\Thread')->create()->id;
        },
        'user_id' => function (){
            return factory('App\User')->create()->id;
        },
        'body' => $faker->paragraph,
    ];
});

$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function (Faker\Generator $faker) {
    
    return [
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function() {
            return auth()->user()->id ?: factory('App\User')->create()->id;
        },
        'notifiable_type' => 'App\User',
        'data' => ['foo' => 'bar']
    ];
});
    

