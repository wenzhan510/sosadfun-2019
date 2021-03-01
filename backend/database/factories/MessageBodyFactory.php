<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MessageBody::class, function (Faker $faker) {
    $body = $faker->paragraph;
    return [
        'body' => $body,
        'bulk' => false,
    ];
});
