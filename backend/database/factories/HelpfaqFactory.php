<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Helpfaq::class, function (Faker $faker) {
    $question = $faker->text($maxNbChars = 150);
    $answer = $faker->paragraph;
    $k1 = rand(1,7);
    $k2 = rand(1,5);
    return [
        'key'=> "$k1-$k2",
        'question' => $question,
        'answer' => $answer
    ];
});
