<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserInfo;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(User::class)->create([
            'name' => '皮卡丘',
            'email' => '1@gmail.com',
            'level' => 6,
        ]);
        $userInfo = UserInfo::find($user->id);
        $userInfo->salt = 1000;
        $userInfo->fish = 500;
        $userInfo->salt = 100;
        $userInfo->message_limit = 10;
        $userInfo->list_limit = 10;
        $userInfo->save();

        $user = factory(User::class)->create([
            'name' => '伊布',
            'email' => '2@gmail.com',
            'level' => 7,
        ]);
        $userInfo = UserInfo::find($user->id);
        $userInfo->salt = 2000;
        $userInfo->fish = 500;
        $userInfo->salt = 100;
        $userInfo->message_limit = 10;
        $userInfo->list_limit = 10;
        $userInfo->save();

        $user = factory(User::class)->create([
            'name' => '小火龙',
            'email' => '3@gmail.com',
            'level' => 10,
            'role' => 'admin',
        ]);
        $userInfo = UserInfo::find($user->id);
        $userInfo->salt = 2000;
        $userInfo->fish = 500;
        $userInfo->salt = 100;
        $userInfo->message_limit = 10;
        $userInfo->list_limit = 10;
        $userInfo->save();

        $users = factory(User::class)->times(10)->create();
    }
}
