<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Status::class, function (Faker $faker) {
    $body = $faker->paragraph;
    return [
        'body' => $body,
        'brief' => App\Helpers\StringProcess::simpletrim($body, 45),
        'user_id' => function(){
            return \App\Models\User::inRandomOrder()->first()->id;
        },
    ];
});
