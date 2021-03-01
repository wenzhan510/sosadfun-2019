<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reward::class, function (Faker $faker) {
    $rewards = array("fish", "salt", "ham");
    $rand_key = array_rand($rewards);
    $reward = $rewards[$rand_key];

    return [
    	  'user_id' => function(){
          	return \App\Models\User::inRandomOrder()->first()->id;
    	  },
    	  'receiver_id' => function(){
          	return \App\Models\User::inRandomOrder()->first()->id;
    	  },
    	  'rewardable_id' => function(){
          	return \App\Models\Post::inRandomOrder()->first()->id;
		    },
		    'rewardable_type' => 'post',
		    'reward_type' => $reward,
		    'reward_value' => rand(1,10) ,
        'created_at' => Carbon::now(), 
    ];
});