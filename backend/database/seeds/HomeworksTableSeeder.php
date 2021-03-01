<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HomeworksTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        // 创建4个已经过期的作业
        $homeworks = factory(\App\Models\Homework::class)->times(4)->create([
            'is_active' => 0,
            'registration_on' => 0,
            'created_at' => Carbon::now()->subDays(40),
            'registration_on' => Carbon::now()->subDays(38),
            'end_at' => Carbon::now()->subDays(10),
        ]);
        // 创建一个admin账户
        $admin = factory(\App\Models\User::class)->create([
            'role' => 'admin',
            'level' => 10,
            'activated' => 1,
        ]);
        $homeworks->each(function($homework)use($admin){
            $profile_thread = factory(\App\Models\Thread::class)->create([
                'channel_id' => config('constants.commentary_channel_id'),
                'user_id' => $admin->id,
                'title' => $homework->title."报名",
                'brief' => "开始报名",
            ]);
            $homework->profile_thread_id = $profile_thread->id;
            $homework->save();
            $users = factory(\App\Models\User::class)->times(2)->create();
            $users->each(function($user)use($homework){
                $homework_registration = factory(\App\Models\HomeworkRegistration::class)->create([
                    'user_id' => $user->id,
                    'homework_id' => $homework->id,
                    'role' =>'critic',
                ]);
            });
            $users = factory(\App\Models\User::class)->times(2)->create();
            $users->each(function($user)use($homework){
                $order_id = rand(1,5);
                $homework_thread = factory(\App\Models\Thread::class)->create([
                    'user_id' => $user->id,
                    'channel_id' => config('constants.homework_channel_id'),
                    'title' => $homework->title.'-'.$homework->topic.'-'.$order_id,
                ]);
                $homework_registration = factory(\App\Models\HomeworkRegistration::class)->create([
                    'user_id' => $user->id,
                    'homework_id' => $homework->id,
                    'role' =>'worker',
                    'thread_id' => $homework_thread->id,
                    'order_id' => $order_id,
                ]);
            });
        });
        $homeworks = factory(\App\Models\Homework::class)->create([
            'is_active' => 1,
            'registration_on' => 1,
            'created_at' => Carbon::now(),
            'registration_on' => Carbon::now(),
        ]);
    }
}
