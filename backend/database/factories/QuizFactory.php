<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Quiz::class, function (Faker $faker) {
    // register, essay 和 level_up的比例为 11 : 1 : 15(3+3+3)
    $rnd_int = $faker->numberBetween($min = 1, $max = 27);
    if ($rnd_int <= 11) {
        $type = 'register';
    } elseif ($rnd_int == 12) {
        $type = 'essay';
    } else {
        $type = 'level_up';
    }
    return [
        'is_online' => true,
        'type' => $type,
        'quiz_level' => $type == 'level_up' ? $faker->numberBetween($min = 0, $max = 2) : -1,
        'body' => str_random(10),
        'hint' => str_random(10)
    ];
})->afterCreating(App\Models\Quiz::class, function ($my_model, Faker $faker) {
    if ($my_model->type != 'essay') {

        // 每道题最多6个选项，最少3个选项
        $total_options_count = $faker->numberBetween($min = 3, $max = 6);

        // 每道题最多全选，最少1个正确选项
        $correct_answer_count = $faker->numberBetween($min = 1, $max = $total_options_count);
        $is_correct_arr = [];
        for ($i=0;$i<$total_options_count;++$i) {
            if ($i<$correct_answer_count) {
                $is_correct_arr[] = 1;
            } else {
                $is_correct_arr[] = 0;
            }
        }

        // 随机在quiz_option中分配正确选项
        $is_correct_arr = $faker->shuffleArray($is_correct_arr);
        for ($i=0;$i<$total_options_count;++$i) {
            factory(App\Models\QuizOption::class)->create([
                'quiz_id' => $my_model->id,
                'is_correct' => $is_correct_arr[$i]
            ]);
        }

    }
});
