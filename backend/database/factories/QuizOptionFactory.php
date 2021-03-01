<?php

use Faker\Generator as Faker;

$factory->define(App\Models\QuizOption::class, function (Faker $faker) {
    return [
        'quiz_id' => rand(1, 1000),
        'body' => str_random(10),
        'explanation' => str_random(10),
        'is_correct' => $faker->boolean
    ];
});
