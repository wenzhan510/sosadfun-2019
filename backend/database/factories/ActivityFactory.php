<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Activity::class, function (Faker $faker) {
    $body = $faker->paragraph;
    return [
        'kind' => 1,
        'item_type' => 'post',
        'item_id' => function(){
            return \App\Models\Post::inRandomOrder()->first()->id;
        },
        'user_id' => function(){
            return \App\Models\User::inRandomOrder()->first()->id;
        },
        'seen' => false,
    ];
});
