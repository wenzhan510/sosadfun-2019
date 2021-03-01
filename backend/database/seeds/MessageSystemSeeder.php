<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MessageSystemSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        // 创建一个admin账户
        $admin = factory(\App\Models\User::class)->create([
            'role' => 'admin',
            'level' => 10,
            'activated' => 1,
        ]);
        
        // seed public notices
        $public_notices = factory(\App\Models\PublicNotice::class)->times(5)->create(['user_id' => $admin->id]);

        // seed user messages
        $messages = factory(\App\Models\Message::class)->times(100)->create();
    }
}
