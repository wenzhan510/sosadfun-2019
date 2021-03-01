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

$factory->define(App\Models\PostInfo::class, function (Faker $faker) {
    return [
        'post_id' => rand(10000, 20000),
        'warning' => $faker->paragraph,
        'annotation' => $faker->paragraph,
        'abstract' => $faker->sentence,
        'order_by' => rand(1, 200),
        'reviewee_id' => function(){
            return \App\Models\Thread::inRandomOrder()->first()->id;
        },
        'reviewee_type' => 'thread',
        'summary' => 'recommend',
        'rating' => rand(1, 10),
    ];
});
