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

$factory->define(App\Models\Thread::class, function (Faker $faker) {
    return [
        'channel_id' => 1,//默认的channel作为1
        'title' => $faker->word,
        'brief' => $faker->sentence,
        'body' => $faker->paragraph,
        'user_id' => function(){
            return \App\Models\User::inRandomOrder()->first()->id;
        },
    ];
});
