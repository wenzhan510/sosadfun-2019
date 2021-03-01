<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultSettingsSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(QuotesTableSeeder::class);
        $this->call(StatusesTableSeeder::class);
        $this->call(ThreadsTableSeeder::class);
        $this->call(HomeworksTableSeeder::class);
        $this->call(FollowersTableSeeder::class);
        $this->call(QuizzesTableSeeder::class);  
        $this->call(HelpfaqsTableSeeder::class);
        $this->call(MessageSystemSeeder::class);
    }
}
