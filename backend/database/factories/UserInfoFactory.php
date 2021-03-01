<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserInfo::class, function (Faker $faker) {
    return [
        'user_id' => rand(10000, 20000),
        'message_limit' => 50,
    ];
});
