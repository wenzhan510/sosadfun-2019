<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\RegistrationApplication::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'ip_address' => '127.0.0.1',
        'email_token' => str_random(10),
        'token' => str_random(35),
    ];
});
