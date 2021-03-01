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

$factory->define(App\Models\Homework::class, function (Faker $faker) {
    return [
        'title' => "第".rand(100,300)."次作业",
        'topic' => App\Helpers\StringProcess::simpletrim($faker->word, 10),
        'worker_registration_limit' => rand(5,15),
        'critic_registration_limit' => rand(5,15),
    ];
});
