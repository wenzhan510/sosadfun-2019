<?php

use Faker\Generator as Faker;

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

$factory->define(App\Models\Post::class, function (Faker $faker) {
    $body = $faker->paragraph;
    return [
        'title' => $faker->sentence,
        'brief' => App\Helpers\StringProcess::simpletrim($body, 20),
        'body' => $body,
        'user_id' => function(){
            return \App\Models\User::inRandomOrder()->first()->id;
        },
        'thread_id' => function(){
            return \App\Models\Thread::inRandomOrder()->first()->id;
        },
        'type' => 'post',
        'char_count' => mb_strlen($body),
    ];
});
