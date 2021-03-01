<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Message::class, function (Faker $faker) {
    $message_body = factory('App\Models\MessageBody')->create();
    return [
      'poster_id' => function(){
          return \App\Models\User::inRandomOrder()->first()->id;
      },
      'receiver_id' => function(){
          return \App\Models\User::inRandomOrder()->first()->id;
      },
      'body_id' => $message_body->id,
      'seen' => 0,
    ];
    // QUESTION: why the attribute 'seen' is a 'TINYINT' but not 'boolean'?
});
