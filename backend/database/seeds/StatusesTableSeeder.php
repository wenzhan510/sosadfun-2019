<?php

use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\Vote;
use App\Models\Reward;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = factory(Status::class)->times(20)->create();
        $statuses->each(function($status){
            // seed votes
            $votes = factory(Vote::class)->create([
                'receiver_id' => $status->user_id,
                'votable_id' => $status->id,
                'votable_type' => 'status'
            ]);
            // seed rewards
            $votes = factory(Reward::class)->create([
                'receiver_id' => $status->user_id,
                'rewardable_id' => $status->id,
                'rewardable_type' => 'status'
            ]);
        });
    }
}
