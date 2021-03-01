<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Vote::class, function (Faker $faker) {
    return [
    	'user_id' => function(){
        	return \App\Models\User::inRandomOrder()->first()->id;
    	},
    	'receiver_id' => function(){
        	return \App\Models\User::inRandomOrder()->first()->id;
    	},
    	'votable_id' => function(){
        	return \App\Models\Post::inRandomOrder()->first()->id;
		},
		'votable_type' => 'quote',
		'attitude_type' => 'upvote',
    	'created_at' => Carbon::now(),
    ];
});