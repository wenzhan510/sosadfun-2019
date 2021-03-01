<?php

use Illuminate\Database\Seeder;
use App\Models\Quiz;

class QuizzesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Quiz::class)->times(11)->create(['type' => 'register','quiz_level' => -1]);
        factory(Quiz::class)->times(1)->create(['type' => 'essay','quiz_level' => -1]);
        factory(Quiz::class)->times(5)->create(['type' => 'level_up','quiz_level' => 0]);
        factory(Quiz::class)->times(5)->create(['type' => 'level_up','quiz_level' => 1]);
        factory(Quiz::class)->times(5)->create(['type' => 'level_up','quiz_level' => 2]);
        factory(Quiz::class)->times(73)->create();
    }
}
