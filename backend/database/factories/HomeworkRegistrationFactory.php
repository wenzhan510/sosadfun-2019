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


$factory->define(App\Models\HomeworkRegistration::class, function (Faker $faker) {
    return [
        'user_id' => function(){
            return \App\Models\User::inRandomOrder()->first()->id;
        },
        'homework_id' => function(){
            return \App\Models\Homework::inRandomOrder()->first()->id;
        },
        'role' => 'critic',
        'majia' => str_random(5),
    ];
});
