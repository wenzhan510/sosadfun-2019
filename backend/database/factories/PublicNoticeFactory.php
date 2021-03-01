<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Models\PublicNotice::class, function (Faker $faker) {
    $body = $faker->text($maxNbChars = 1000);
    return [
        'title' => $faker->sentence,
        'body' => $body,
        'created_at' => Carbon::now()->subDays(rand(0, 60))
    ];
});
